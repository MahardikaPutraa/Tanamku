<?php

namespace Tests\Feature;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\Plant;
use App\Models\Store;
use App\Models\User;
use App\Services\WhatsAppMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketplaceFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function buyer(): User
    {
        return User::where('email', 'buyer@tanamku.test')->firstOrFail();
    }

    private function seller(): User
    {
        return User::where('email', 'seller@tanamku.test')->firstOrFail();
    }

    private function admin(): User
    {
        return User::where('email', 'admin@tanamku.test')->firstOrFail();
    }

    /** Produk toko yang belum disetujui tidak boleh muncul di katalog. */
    public function test_produk_toko_belum_disetujui_tidak_tampil(): void
    {
        $menunggu = Store::where('status', 'menunggu')->firstOrFail();
        $produk = $menunggu->plants()->firstOrFail();

        $this->get('/katalog')->assertDontSee($produk->nama);

        $menunggu->update(['status' => 'aktif']);

        $this->get('/katalog')->assertSee($produk->nama);
    }

    /** Tamu tidak bisa menambah ke keranjang. */
    public function test_tamu_tidak_bisa_menambah_keranjang(): void
    {
        $plant = Plant::where('slug', 'monstera-deliciosa')->firstOrFail();

        $this->post(route('carts.store', $plant), ['jumlah' => 1])
            ->assertRedirect('/login');

        $this->assertSame(0, CartItem::count());
    }

    /** Pembeli bisa menambah ke keranjang, dan jumlahnya menumpuk. */
    public function test_pembeli_bisa_menambah_keranjang_dua_kali(): void
    {
        $plant = Plant::where('slug', 'monstera-deliciosa')->firstOrFail();

        $this->actingAs($this->buyer())->post(route('carts.store', $plant), ['jumlah' => 2]);
        $this->actingAs($this->buyer())->post(route('carts.store', $plant), ['jumlah' => 1]);

        $item = CartItem::where('user_id', $this->buyer()->id)->firstOrFail();

        $this->assertSame(3, $item->jumlah);
    }

    /** Jumlah di keranjang tidak boleh melebihi stok. */
    public function test_jumlah_keranjang_tidak_boleh_melebihi_stok(): void
    {
        $plant = Plant::where('slug', 'anthurium-merah')->firstOrFail(); // stok 3

        $this->actingAs($this->buyer())
            ->post(route('carts.store', $plant), ['jumlah' => 10])
            ->assertSessionHas('error');

        $this->assertSame(0, CartItem::count());
    }

    /** Checkout membuat pesanan, mengurangi stok, dan mengosongkan keranjang. */
    public function test_checkout_membuat_pesanan_dan_mengurangi_stok(): void
    {
        $plant = Plant::where('slug', 'monstera-deliciosa')->firstOrFail();
        $stokAwal = $plant->stok;
        $store = $plant->store;

        $this->actingAs($this->buyer())
            ->post(route('carts.store', $plant), ['jumlah' => 2]);

        $this->actingAs($this->buyer())->post(route('checkout.store', $store), [
            'nama_penerima' => 'Rina Pelanggan',
            'no_hp' => '081298765432',
            'alamat' => 'Jl. Anggrek No. 21, Jakarta Barat',
            'metode_serah' => 'kirim',
            'catatan' => 'tes',
        ])->assertRedirect();

        $order = Order::latest('id')->firstOrFail();

        $this->assertSame($this->buyer()->id, $order->buyer_id);
        $this->assertSame($store->id, $order->store_id);
        $this->assertEquals((float) $plant->harga * 2, (float) $order->total_harga);
        $this->assertSame(1, $order->items()->count());

        $this->assertSame($stokAwal - 2, $plant->fresh()->stok);
        $this->assertSame(0, CartItem::where('user_id', $this->buyer()->id)->count());
    }

    /** Pesan WhatsApp memuat kode, item, dan total yang benar. */
    public function test_pesan_whatsapp_berisi_rincian_pesanan(): void
    {
        $order = Order::with(['items', 'store'])->firstOrFail();
        $pesan = WhatsAppMessage::untukOrder($order);
        $url = WhatsAppMessage::url($order);

        $this->assertStringContainsString($order->kode_pesanan, $pesan);
        $this->assertStringContainsString($order->items->first()->nama_produk, $pesan);
        $this->assertStringContainsString('TOTAL', $pesan);

        // Nomor 08xx harus dinormalisasi ke 62xx.
        $this->assertStringStartsWith('https://wa.me/62', $url);
        $this->assertStringNotContainsString('wa.me/08', $url);
    }

    /** Isolasi peran: penjual tidak bisa membuka panel admin, dan sebaliknya. */
    public function test_isolasi_akses_antar_peran(): void
    {
        $this->actingAs($this->seller())->get('/admin/dashboard')->assertForbidden();
        $this->actingAs($this->admin())->get('/seller/dashboard')->assertForbidden();
        $this->actingAs($this->buyer())->get('/seller/dashboard')->assertForbidden();
        $this->actingAs($this->buyer())->get('/admin/dashboard')->assertForbidden();
    }

    /** Penjual tidak boleh mengedit produk milik toko lain. */
    public function test_penjual_tidak_bisa_mengedit_produk_toko_lain(): void
    {
        $produkTokoLain = Plant::whereHas('store', function ($q) {
            $q->where('slug', 'rumah-daun-bandung');
        })->firstOrFail();

        $this->actingAs($this->seller())
            ->get(route('seller.plants.edit', $produkTokoLain))
            ->assertForbidden();
    }

    /** Admin menyetujui toko -> produknya langsung tampil. */
    public function test_admin_menyetujui_toko(): void
    {
        $store = Store::where('status', 'menunggu')->firstOrFail();

        $this->actingAs($this->admin())
            ->patch(route('admin.stores.status', $store), ['status' => 'aktif'])
            ->assertRedirect();

        $this->assertSame('aktif', $store->fresh()->status);
    }

    /** Admin tidak bisa mengubah perannya sendiri (mencegah lockout). */
    public function test_admin_tidak_bisa_mengubah_peran_sendiri(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->patch(route('admin.users.role', $admin), ['role' => 'buyer'])
            ->assertSessionHas('error');

        $this->assertSame('admin', $admin->fresh()->role);
    }

    /** Registrasi tidak bisa dipakai membuat akun admin. */
    public function test_registrasi_tidak_bisa_membuat_admin(): void
    {
        $this->post(route('register'), [
            'name' => 'Penyusup',
            'email' => 'penyusup@test.local',
            'no_hp' => '081200002222',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
            'role' => 'admin',
        ])->assertSessionHasErrors('role');

        $this->assertNull(User::where('email', 'penyusup@test.local')->first());
    }

    /** Penjual baru diarahkan melengkapi profil toko, dan toko berstatus menunggu. */
    public function test_daftar_sebagai_penjual_diarahkan_ke_form_toko(): void
    {
        $this->post(route('register'), [
            'name' => 'Penjual Baru',
            'email' => 'penjualbaru@test.local',
            'no_hp' => '081200003333',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
            'role' => 'seller',
        ])->assertRedirect(route('seller.store.edit'));

        $user = User::where('email', 'penjualbaru@test.local')->firstOrFail();
        $this->assertSame('seller', $user->role);

        $this->actingAs($user)->put(route('seller.store.update'), [
            'nama_toko' => 'Toko Uji',
            'no_wa' => '081200003333',
            'alamat' => 'Jl. Uji No. 1, Jakarta',
            'kota' => 'Jakarta',
            'deskripsi' => 'Toko untuk keperluan pengujian otomatis.',
            'metode_serah' => 'keduanya',
        ])->assertRedirect(route('seller.dashboard'));

        $this->assertSame('menunggu', $user->fresh()->store->status);
    }

    /** Tombol WhatsApp hilang setelah pesanan dibatalkan. */
    public function test_tombol_whatsapp_hilang_bila_pesanan_dibatalkan(): void
    {
        $order = Order::where('status', 'baru')->firstOrFail();
        $buyer = $order->buyer;

        $this->actingAs($buyer)->get(route('orders.show', $order))
            ->assertOk()
            ->assertSee('Buka WhatsApp')
            ->assertSee('wa.me', false);

        $order->update(['status' => 'batal']);

        $this->actingAs($buyer)->get(route('orders.show', $order))
            ->assertOk()
            ->assertSee('Pesanan ini dibatalkan')
            ->assertDontSee('Buka WhatsApp')
            ->assertDontSee('wa.me', false);
    }

    /** Pembatalan oleh penjual mengembalikan stok produk. */
    public function test_penjual_membatalkan_pesanan_mengembalikan_stok(): void
    {
        $order = Order::where('status', 'baru')->with('items.plant')->firstOrFail();
        $item = $order->items->first();
        $stokAwal = $item->plant->stok;

        $this->actingAs($order->store->user)
            ->patch(route('seller.orders.status', $order), ['status' => 'batal'])
            ->assertRedirect();

        $this->assertSame('batal', $order->fresh()->status);
        $this->assertSame($stokAwal + $item->jumlah, $item->plant->fresh()->stok);
    }

    /** Pencarian katalog bersifat case-insensitive (whereLike). */
    public function test_pencarian_katalog_case_insensitive(): void
    {
        $this->get('/katalog?q=monstera')->assertSee('Monstera Deliciosa');
        $this->get('/katalog?q=MONSTERA')->assertSee('Monstera Deliciosa');
        $this->get('/katalog?q=mOnStErA')->assertSee('Monstera Deliciosa');
    }

    /** Checkout gagal dan rollback bila stok habis tepat sebelum transaksi selesai. */
    public function test_checkout_rollback_bila_stok_berubah_habis(): void
    {
        $plant = Plant::where('slug', 'monstera-deliciosa')->firstOrFail();
        $store = $plant->store;

        $this->actingAs($this->buyer())
            ->post(route('carts.store', $plant), ['jumlah' => 2]);

        // Simulasikan produk dibeli pembeli lain sebelum transaksi ini selesai
        $plant->update(['stok' => 1]);

        $orderCountBefore = Order::count();

        $this->actingAs($this->buyer())->post(route('checkout.store', $store), [
            'nama_penerima' => 'Rina Pelanggan',
            'no_hp' => '081298765432',
            'alamat' => 'Jl. Anggrek No. 21, Jakarta Barat',
            'metode_serah' => 'kirim',
        ])->assertSessionHas('error');

        $this->assertSame($orderCountBefore, Order::count());
        $this->assertSame(1, $plant->fresh()->stok);
        // Keranjang tidak terhapus bila transaksi gagal
        $this->assertSame(1, CartItem::where('user_id', $this->buyer()->id)->count());
    }

    /** Filter katalog berdasarkan rentang harga dan kota toko. */
    public function test_filter_katalog_harga_dan_kota(): void
    {
        // Filter harga murah (<= 60.000)
        $this->get('/katalog?harga_max=60000')
            ->assertOk()
            ->assertSee('Sirih Gading');

        // Filter kota toko (Bandung)
        $this->get('/katalog?kota=Bandung')
            ->assertOk()
            ->assertSee('Peace Lily');
    }

    /** Pembeli dapat mengonfirmasi pesanan diterima dan selesai. */
    public function test_pembeli_konfirmasi_pesanan_selesai(): void
    {
        $order = Order::where('status', 'diproses')->firstOrFail();
        $buyer = $order->buyer;

        $this->actingAs($buyer)
            ->patch(route('orders.complete', $order))
            ->assertSessionHas('success');

        $this->assertSame('selesai', $order->fresh()->status);
    }

    /** Pembeli dapat memberi rating dan ulasan untuk produk yang dibeli. */
    public function test_pembeli_memberi_ulasan_dan_rating(): void
    {
        $order = Order::where('status', 'selesai')->firstOrFail();
        $buyer = $order->buyer;
        $item = $order->items->first();

        $this->actingAs($buyer)
            ->post(route('reviews.store', $order), [
                'plant_id' => $item->plant_id,
                'rating' => 5,
                'komentar' => 'Tanaman sangat segar dan pengiriman cepat!',
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('reviews', [
            'order_id' => $order->id,
            'plant_id' => $item->plant_id,
            'user_id' => $buyer->id,
            'rating' => 5,
        ]);

        // Verifikasi ulasan tampil di halaman produk
        $this->get(route('plants.show', $item->plant))
            ->assertSee('Tanaman sangat segar dan pengiriman cepat!')
            ->assertSee('ulasan pembeli');
    }

    /** Seller dan Admin dapat mengunduh laporan pesanan dalam format CSV. */
    public function test_ekspor_laporan_pesanan_csv(): void
    {
        // Seller export
        $responseSeller = $this->actingAs($this->seller())
            ->get(route('seller.orders.export'));
        $responseSeller->assertOk();
        $this->assertStringContainsString('text/csv', $responseSeller->headers->get('content-type'));

        // Admin export
        $responseAdmin = $this->actingAs($this->admin())
            ->get(route('admin.orders.export'));
        $responseAdmin->assertOk();
        $this->assertStringContainsString('text/csv', $responseAdmin->headers->get('content-type'));
    }
}
