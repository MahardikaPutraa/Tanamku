<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\Plant;
use App\Models\Store;
use App\Services\WhatsAppMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    /**
     * Form checkout untuk satu toko.
     */
    public function create(Store $store)
    {
        abort_if(! $store->isAktif(), 404);

        $items = $this->itemKeranjang($store);

        if ($items->isEmpty()) {
            return redirect()->route('carts.index')
                ->with('error', 'Keranjang Anda kosong untuk toko ini.');
        }

        $user = auth()->user();
        $total = $items->sum(fn (CartItem $item) => $item->subtotal);

        return view('checkout.create', compact('store', 'items', 'total', 'user'));
    }

    /**
     * Buat pesanan, kurangi stok, kosongkan keranjang toko ini,
     * lalu arahkan ke halaman pesanan dengan tautan WhatsApp.
     */
    public function store(Request $request, Store $store)
    {
        abort_if(! $store->isAktif(), 403, 'Toko ini belum aktif.');

        $items = $this->itemKeranjang($store);

        if ($items->isEmpty()) {
            return redirect()->route('carts.index')
                ->with('error', 'Keranjang Anda kosong untuk toko ini.');
        }

        $metodeBoleh = match ($store->metode_serah) {
            'ambil' => ['ambil'],
            'kirim' => ['kirim'],
            default => ['ambil', 'kirim'],
        };

        $data = $request->validate([
            'nama_penerima' => ['required', 'string', 'max:100'],
            'no_hp' => ['required', 'string', 'max:20', 'regex:/^[0-9\+\-\s]{8,20}$/'],
            'alamat' => ['required', 'string', 'min:10', 'max:500'],
            'metode_serah' => ['required', 'in:' . implode(',', $metodeBoleh)],
            'catatan' => ['nullable', 'string', 'max:500'],
        ], [
            'no_hp.regex' => 'Nomor HP hanya boleh berisi angka, spasi, tanda + atau -.',
            'alamat.min' => 'Alamat terlalu pendek, minimal 10 karakter.',
            'metode_serah.in' => 'Metode penyerahan tidak tersedia di toko ini.',
        ]);

        try {
            $order = DB::transaction(function () use ($items, $store, $data) {
                // Kunci baris produk dan validasi stok terkini secara atomik (mencegah race condition)
                foreach ($items as $item) {
                    $plant = Plant::whereKey($item->plant_id)->lockForUpdate()->first();

                    if (! $plant || ! $plant->is_active) {
                        throw new \RuntimeException(($item->plant->nama ?? 'Produk') . ' sudah tidak dijual.');
                    }

                    if ($item->jumlah > $plant->stok) {
                        throw new \RuntimeException('Stok ' . $plant->nama . ' tersisa ' . $plant->stok . ' unit.');
                    }
                }

                $total = $items->sum(fn (CartItem $item) => $item->subtotal);

                $order = Order::create([
                    'buyer_id' => auth()->id(),
                    'store_id' => $store->id,
                    'nama_penerima' => $data['nama_penerima'],
                    'no_hp' => $data['no_hp'],
                    'alamat' => $data['alamat'],
                    'metode_serah' => $data['metode_serah'],
                    'catatan' => $data['catatan'] ?? null,
                    'total_harga' => $total,
                    'status' => 'baru',
                ]);

                foreach ($items as $item) {
                    $order->items()->create([
                        'plant_id' => $item->plant_id,
                        'nama_produk' => $item->plant->nama,
                        'harga_satuan' => $item->plant->harga,
                        'jumlah' => $item->jumlah,
                        'subtotal' => $item->subtotal,
                    ]);

                    // Stok dikurangi secara aman di dalam lock transaksi
                    Plant::whereKey($item->plant_id)->decrement('stok', $item->jumlah);
                }

                // Kosongkan keranjang khusus toko ini saja.
                CartItem::where('user_id', auth()->id())
                    ->whereIn('id', $items->pluck('id'))
                    ->delete();

                return $order;
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }

        // Simpan data kontak terakhir ke profil pembeli.
        auth()->user()->update([
            'no_hp' => $data['no_hp'],
            'alamat' => $data['alamat'],
        ]);

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Pesanan ' . $order->kode_pesanan . ' dibuat. Lanjutkan lewat WhatsApp penjual.');
    }

    /**
     * Ambil isi keranjang user untuk satu toko.
     */
    private function itemKeranjang(Store $store)
    {
        return CartItem::with('plant')
            ->where('user_id', auth()->id())
            ->whereHas('plant', fn ($q) => $q->where('store_id', $store->id))
            ->get();
    }
}
