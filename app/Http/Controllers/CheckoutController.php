<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Order;
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

        // Validasi stok terakhir sebelum menyimpan.
        foreach ($items as $item) {
            if (! $item->plant->is_active) {
                return back()->with('error', $item->plant->nama . ' sudah tidak dijual.')->withInput();
            }
            if ($item->jumlah > $item->plant->stok) {
                return back()->with('error', 'Stok ' . $item->plant->nama . ' tersisa ' . $item->plant->stok . ' unit.')->withInput();
            }
        }

        $order = DB::transaction(function () use ($items, $store, $data) {
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

                // Stok dikunci saat pesanan dibuat; dikembalikan bila pesanan dibatalkan.
                $item->plant->decrement('stok', $item->jumlah);
            }

            // Kosongkan keranjang khusus toko ini saja.
            CartItem::where('user_id', auth()->id())
                ->whereIn('id', $items->pluck('id'))
                ->delete();

            return $order;
        });

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
