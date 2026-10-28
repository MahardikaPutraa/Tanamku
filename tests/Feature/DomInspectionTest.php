<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Plant;
use App\Models\Store;
use App\Models\User;
use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DomInspectionTest extends TestCase
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

    /**
     * Parse content into DOMDocument & DOMXPath.
     */
    private function parseDom(string $html): array
    {
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();
        $xpath = new DOMXPath($dom);

        return [$dom, $xpath];
    }

    /**
     * 1. DOM Inspection: Halaman Publik (Home, Katalog, Detail Produk, Toko, Login, Register)
     */
    public function test_dom_halaman_publik(): void
    {
        // ------------------ A. Beranda (Home)
        $resp = $this->get('/');
        $resp->assertOk();
        [$dom, $xpath] = $this->parseDom($resp->getContent());

        // Header & Title
        $titles = $xpath->query('//title');
        $this->assertGreaterThan(0, $titles->length, 'Beranda harus memiliki tag <title>');
        $this->assertStringContainsString('TanamKu', $titles->item(0)->textContent);

        // Favicon link exists
        $favicons = $xpath->query('//head//link[@rel="icon"]');
        $this->assertGreaterThan(0, $favicons->length, 'Favicon harus terpasang di <head>');

        // Hero H1 exists
        $heroH1 = $xpath->query('//section//h1');
        $this->assertGreaterThan(0, $heroH1->length, 'Hero H1 harus ada');

        // Trust Bar: 4 Pilar
        $trustCards = $xpath->query('//section[contains(@class, "border-b")]//h4');
        $this->assertGreaterThanOrEqual(4, $trustCards->length, 'Trust Bar harus memiliki 4 pilar');

        // Kategori cards & Produk Unggulan
        $catCards = $xpath->query('//a[contains(@href, "/katalog?kategori=")]');
        $this->assertGreaterThan(0, $catCards->length, 'Kartu kategori beranda harus ada');

        // ------------------ B. Katalog (/katalog)
        $respKatalog = $this->get('/katalog');
        $respKatalog->assertOk();
        [, $xpathKat] = $this->parseDom($respKatalog->getContent());

        // Search form inputs
        $this->assertGreaterThan(0, $xpathKat->query('//form//input[@name="q"]')->length, 'Input q harus ada');
        $this->assertGreaterThan(0, $xpathKat->query('//form//select[@name="kategori"]')->length, 'Select kategori harus ada');
        $this->assertGreaterThan(0, $xpathKat->query('//form//select[@name="urut"]')->length, 'Select urut harus ada');
        $this->assertGreaterThan(0, $xpathKat->query('//form//select[@name="kota"]')->length, 'Select kota harus ada');
        $this->assertGreaterThan(0, $xpathKat->query('//form//input[@name="harga_min"]')->length, 'Input harga_min harus ada');
        $this->assertGreaterThan(0, $xpathKat->query('//form//input[@name="harga_max"]')->length, 'Input harga_max harus ada');
        $this->assertGreaterThan(0, $xpathKat->query('//form//button[@type="submit"]')->length, 'Tombol submit filter harus ada');

        // Plant cards
        $plantCards = $xpathKat->query('//div[contains(@class, "group") and contains(@class, "rounded-xl")]');
        $this->assertGreaterThan(0, $plantCards->length, 'Harus ada kartu tanaman di katalog');

        // ------------------ C. Detail Produk (/tanaman/{slug})
        $plant = Plant::where('slug', 'monstera-deliciosa')->firstOrFail();
        $respShow = $this->get(route('plants.show', $plant));
        $respShow->assertOk();
        [, $xpathShow] = $this->parseDom($respShow->getContent());

        // H1 Title & Harga
        $h1 = $xpathShow->query('//h1');
        $this->assertGreaterThan(0, $h1->length);
        $this->assertStringContainsString($plant->nama, $h1->item(0)->textContent);

        // Highlight spesifikasi (4 box)
        $specs = $xpathShow->query('//span[contains(@class, "tracking-wider") and contains(text(), "Kategori")]');
        $this->assertGreaterThan(0, $specs->length, 'Highlight spesifikasi produk harus ada');

        // WhatsApp Share link
        $waShare = $xpathShow->query('//a[contains(@href, "wa.me")]');
        $this->assertGreaterThan(0, $waShare->length, 'Tombol bagikan WhatsApp harus ada');

        // Review section
        $reviewSection = $xpathShow->query('//*[@id="ulasan"]');
        $this->assertGreaterThan(0, $reviewSection->length, 'Section #ulasan harus ada');

        // ------------------ D. Detail Toko (/toko/{slug})
        $store = Store::where('slug', 'kebun-hijau-jaya')->firstOrFail();
        $respStore = $this->get(route('stores.show', $store));
        $respStore->assertOk();
        [, $xpathStore] = $this->parseDom($respStore->getContent());
        $this->assertGreaterThan(0, $xpathStore->query('//h1[contains(text(), "' . $store->nama_toko . '")]')->length);

        // ------------------ E. Login (/login)
        $respLogin = $this->get('/login');
        $respLogin->assertOk();
        [, $xpathLogin] = $this->parseDom($respLogin->getContent());

        // Form login inputs
        $this->assertGreaterThan(0, $xpathLogin->query('//form//input[@name="email"]')->length);
        $this->assertGreaterThan(0, $xpathLogin->query('//form//input[@name="password"]')->length);
        $this->assertGreaterThan(0, $xpathLogin->query('//form//input[@name="_token"]')->length);
        $this->assertGreaterThan(0, $xpathLogin->query('//form//button[@type="submit"]')->length);



        // ------------------ F. Register (/register)
        $respReg = $this->get('/register');
        $respReg->assertOk();
        [, $xpathReg] = $this->parseDom($respReg->getContent());
        $this->assertGreaterThan(0, $xpathReg->query('//form//input[@name="name"]')->length);
        $this->assertGreaterThan(0, $xpathReg->query('//form//input[@name="email"]')->length);
        $this->assertGreaterThan(0, $xpathReg->query('//form//input[@name="password"]')->length);
        $this->assertGreaterThan(0, $xpathReg->query('//form//input[@name="no_hp"]')->length);
    }

    /**
     * 2. DOM Inspection: Alur Pembeli (Keranjang, Checkout, Pesanan Saya, Detail Pesanan)
     */
    public function test_dom_halaman_pembeli(): void
    {
        $buyer = $this->buyer();

        // ------------------ A. Keranjang
        $respCart = $this->actingAs($buyer)->get(route('carts.index'));
        $respCart->assertOk();
        [, $xpathCart] = $this->parseDom($respCart->getContent());
        $this->assertGreaterThan(0, $xpathCart->query('//h1[contains(text(), "Keranjang")]')->length);

        // ------------------ B. Checkout
        $store = Store::where('status', 'aktif')->firstOrFail();
        $plant = $store->plants()->firstOrFail();

        // Add to cart first
        $this->actingAs($buyer)->post(route('carts.store', $plant), ['jumlah' => 1]);

        $respCheckout = $this->actingAs($buyer)->get(route('checkout.create', $store));
        $respCheckout->assertOk();
        [, $xpathCheck] = $this->parseDom($respCheckout->getContent());

        // Checkout Stepper (1. Keranjang, 2. Data Pengiriman, 3. Chat WhatsApp)
        $this->assertGreaterThan(0, $xpathCheck->query('//*[contains(text(), "Data Pengiriman")]')->length, 'Stepper harus ada');

        // Inputs
        $this->assertGreaterThan(0, $xpathCheck->query('//form//input[@name="nama_penerima"]')->length);
        $this->assertGreaterThan(0, $xpathCheck->query('//form//input[@name="no_hp"]')->length);
        $this->assertGreaterThan(0, $xpathCheck->query('//form//textarea[@name="alamat"]')->length);
        $this->assertGreaterThan(0, $xpathCheck->query('//form//input[@name="metode_serah"]')->length);
        $this->assertGreaterThan(0, $xpathCheck->query('//form//button[@type="submit"]')->length);

        // ------------------ C. Pesanan Saya (/pesanan)
        $respOrders = $this->actingAs($buyer)->get(route('orders.mine'));
        $respOrders->assertOk();
        [, $xpathOrders] = $this->parseDom($respOrders->getContent());
        $this->assertGreaterThan(0, $xpathOrders->query('//h1[contains(text(), "Pesanan Saya")]')->length);

        // ------------------ D. Detail Pesanan (/pesanan/{order})
        $order = Order::where('buyer_id', $buyer->id)->firstOrFail();
        $respOrder = $this->actingAs($buyer)->get(route('orders.show', $order));
        $respOrder->assertOk();
        [, $xpathOrder] = $this->parseDom($respOrder->getContent());

        $this->assertGreaterThan(0, $xpathOrder->query('//h1[contains(text(), "' . $order->kode_pesanan . '")]')->length);
        $this->assertGreaterThan(0, $xpathOrder->query('//*[contains(text(), "' . $order->status_label . '")]')->length);
    }

    /**
     * 3. DOM Inspection: Panel Penjual (Dashboard, Toko, Produk Saya, Tambah Produk, Pesanan Masuk)
     */
    public function test_dom_halaman_penjual(): void
    {
        $seller = $this->seller();

        // ------------------ A. Dashboard Seller
        $respDash = $this->actingAs($seller)->get(route('seller.dashboard'));
        $respDash->assertOk();
        [, $xpathDash] = $this->parseDom($respDash->getContent());

        // Sidebar link exists
        $this->assertGreaterThan(0, $xpathDash->query('//aside//a[contains(@href, "/seller/dashboard")]')->length);
        $this->assertGreaterThan(0, $xpathDash->query('//aside//a[contains(@href, "/seller/produk")]')->length);
        $this->assertGreaterThan(0, $xpathDash->query('//aside//a[contains(@href, "/seller/pesanan")]')->length);

        // Stat cards (Produk, Stok Menipis, Pesanan Baru, Omzet)
        $this->assertGreaterThan(0, $xpathDash->query('//*[contains(text(), "Omzet")]')->length);
        $this->assertGreaterThan(0, $xpathDash->query('//*[contains(text(), "Stok Menipis")]')->length);

        // ------------------ B. Kelola Toko (/seller/toko)
        $respStore = $this->actingAs($seller)->get(route('seller.store.edit'));
        $respStore->assertOk();
        [, $xpathStore] = $this->parseDom($respStore->getContent());
        $this->assertGreaterThan(0, $xpathStore->query('//form//input[@name="nama_toko"]')->length);
        $this->assertGreaterThan(0, $xpathStore->query('//form//input[@name="no_wa"]')->length);
        $this->assertGreaterThan(0, $xpathStore->query('//form//input[@name="kota"]')->length);

        // ------------------ C. Produk Penjual (/seller/produk)
        $respPlants = $this->actingAs($seller)->get(route('seller.plants.index'));
        $respPlants->assertOk();
        [, $xpathPlants] = $this->parseDom($respPlants->getContent());
        $this->assertGreaterThan(0, $xpathPlants->query('//a[contains(@href, "/seller/produk/create")]')->length);
        $this->assertGreaterThan(0, $xpathPlants->query('//table')->length, 'Tabel produk penjual harus ada');

        // ------------------ D. Tambah Produk (/seller/produk/create)
        $respCreate = $this->actingAs($seller)->get(route('seller.plants.create'));
        $respCreate->assertOk();
        [, $xpathCreate] = $this->parseDom($respCreate->getContent());
        $this->assertGreaterThan(0, $xpathCreate->query('//form//input[@name="nama"]')->length);
        $this->assertGreaterThan(0, $xpathCreate->query('//form//select[@name="category_id"]')->length);
        $this->assertGreaterThan(0, $xpathCreate->query('//form//input[@name="harga"]')->length);
        $this->assertGreaterThan(0, $xpathCreate->query('//form//input[@name="stok"]')->length);
        $this->assertGreaterThan(0, $xpathCreate->query('//form//input[@name="foto"]')->length);

        // ------------------ E. Pesanan Masuk Penjual (/seller/pesanan)
        $respOrders = $this->actingAs($seller)->get(route('seller.orders.index'));
        $respOrders->assertOk();
        [, $xpathOrders] = $this->parseDom($respOrders->getContent());

        // Tombol Unduh CSV Laporan Penjualan
        $this->assertGreaterThan(0, $xpathOrders->query('//a[contains(@href, "/seller/pesanan/export")]')->length, 'Tombol export CSV harus ada');
        $this->assertGreaterThan(0, $xpathOrders->query('//table')->length, 'Tabel pesanan masuk harus ada');
    }

    /**
     * 4. DOM Inspection: Panel Admin (Dashboard, Pengguna, Toko, Kategori, Pesanan)
     */
    public function test_dom_halaman_admin(): void
    {
        $admin = $this->admin();

        // ------------------ A. Dashboard Admin
        $respDash = $this->actingAs($admin)->get(route('admin.dashboard'));
        $respDash->assertOk();
        [, $xpathDash] = $this->parseDom($respDash->getContent());

        // Admin sidebar brand & menu
        $this->assertGreaterThan(0, $xpathDash->query('//aside//a[contains(@href, "/admin/dashboard")]')->length);
        $this->assertGreaterThan(0, $xpathDash->query('//aside//a[contains(@href, "/admin/pengguna")]')->length);
        $this->assertGreaterThan(0, $xpathDash->query('//aside//a[contains(@href, "/admin/toko")]')->length);
        $this->assertGreaterThan(0, $xpathDash->query('//aside//a[contains(@href, "/admin/kategori")]')->length);
        $this->assertGreaterThan(0, $xpathDash->query('//aside//a[contains(@href, "/admin/pesanan")]')->length);

        // Stat cards
        $this->assertGreaterThan(0, $xpathDash->query('//*[contains(text(), "Pembeli")]')->length);
        $this->assertGreaterThan(0, $xpathDash->query('//*[contains(text(), "Penjual")]')->length);
        $this->assertGreaterThan(0, $xpathDash->query('//*[contains(text(), "Toko Aktif")]')->length);

        // ------------------ B. Pengguna (/admin/pengguna)
        $respUsers = $this->actingAs($admin)->get(route('admin.users.index'));
        $respUsers->assertOk();
        [, $xpathUsers] = $this->parseDom($respUsers->getContent());
        $this->assertGreaterThan(0, $xpathUsers->query('//table//tbody//tr')->length, 'Baris tabel user harus ada');

        // ------------------ C. Toko (/admin/toko)
        $respStores = $this->actingAs($admin)->get(route('admin.stores.index'));
        $respStores->assertOk();
        [, $xpathStores] = $this->parseDom($respStores->getContent());
        $this->assertGreaterThan(0, $xpathStores->query('//table//tbody//tr')->length, 'Baris tabel toko harus ada');

        // ------------------ D. Kategori (/admin/kategori)
        $respCats = $this->actingAs($admin)->get(route('admin.categories.index'));
        $respCats->assertOk();
        [, $xpathCats] = $this->parseDom($respCats->getContent());
        $this->assertGreaterThan(0, $xpathCats->query('//form//input[@name="nama"]')->length);
        $this->assertGreaterThan(0, $xpathCats->query('//table//tbody//tr')->length);

        // ------------------ E. Pesanan Marketplace (/admin/pesanan)
        $respOrders = $this->actingAs($admin)->get(route('admin.orders.index'));
        $respOrders->assertOk();
        [, $xpathOrders] = $this->parseDom($respOrders->getContent());

        // Tombol Unduh CSV Admin
        $this->assertGreaterThan(0, $xpathOrders->query('//a[contains(@href, "/admin/pesanan/export")]')->length, 'Tombol export CSV admin harus ada');
        $this->assertGreaterThan(0, $xpathOrders->query('//table//tbody//tr')->length);
    }

    /**
     * 5. DOM Inspection: Keamanan CSRF Token di Setiap Form POST/PUT/PATCH/DELETE
     */
    public function test_dom_semua_form_memiliki_csrf_token(): void
    {
        $urlsToInspect = [
            '/',
            '/login',
            '/register',
            '/katalog',
        ];

        foreach ($urlsToInspect as $url) {
            $resp = $this->get($url);
            [, $xpath] = $this->parseDom($resp->getContent());

            $postForms = $xpath->query('//form[translate(@method, "abcdefghijklmnopqrstuvwxyz", "ABCDEFGHIJKLMNOPQRSTUVWXYZ")="POST"]');
            foreach ($postForms as $form) {
                $tokens = $xpath->query('.//input[@name="_token"]', $form);
                $this->assertGreaterThan(0, $tokens->length, "Form POST di {$url} harus memiliki input _token CSRF");
                $this->assertNotEmpty($tokens->item(0)->getAttribute('value'), "Token CSRF di {$url} tidak boleh kosong");
            }
        }
    }
}

