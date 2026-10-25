<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Plant;
use App\Models\Store;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $statistik = [
            'pembeli' => User::where('role', 'buyer')->count(),
            'penjual' => User::where('role', 'seller')->count(),
            'toko_aktif' => Store::where('status', 'aktif')->count(),
            'toko_menunggu' => Store::where('status', 'menunggu')->count(),
            'produk' => Plant::count(),
            'produk_tersembunyi' => Plant::where('is_active', false)->count(),
            'pesanan' => Order::count(),
            'omzet' => Order::where('status', '!=', 'batal')->sum('total_harga'),
        ];

        $tokoMenunggu = Store::with('user')
            ->where('status', 'menunggu')
            ->oldest()
            ->take(5)
            ->get();

        $pesananTerbaru = Order::with(['store', 'buyer'])
            ->latest()
            ->take(6)
            ->get();

        $tokoTeratas = Store::aktif()
            ->withCount(['plants' => fn ($q) => $q->where('is_active', true)])
            ->orderByDesc('plants_count')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('statistik', 'tokoMenunggu', 'pesananTerbaru', 'tokoTeratas'));
    }
}
