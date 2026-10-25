<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;

class DashboardController extends Controller
{
    public function index()
    {
        $store = auth()->user()->store;

        if (! $store) {
            return redirect()->route('seller.store.edit')
                ->with('error', 'Lengkapi profil toko Anda terlebih dahulu.');
        }

        $produk = $store->plants();
        $pesanan = Order::where('store_id', $store->id);

        $statistik = [
            'produk' => (clone $produk)->count(),
            'produk_aktif' => (clone $produk)->where('is_active', true)->count(),
            'stok_menipis' => (clone $produk)->where('stok', '<', 5)->count(),
            'pesanan_baru' => (clone $pesanan)->where('status', 'baru')->count(),
            'pesanan_total' => (clone $pesanan)->count(),
            'omzet' => (clone $pesanan)->where('status', '!=', 'batal')->sum('total_harga'),
        ];

        $pesananTerbaru = Order::with('items')
            ->where('store_id', $store->id)
            ->latest()
            ->take(5)
            ->get();

        $produkMenipis = $store->plants()
            ->where('stok', '<', 5)
            ->orderBy('stok')
            ->take(6)
            ->get();

        return view('seller.dashboard', compact('store', 'statistik', 'pesananTerbaru', 'produkMenipis'));
    }
}
