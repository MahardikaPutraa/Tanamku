<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Plant;
use App\Models\Store;

class HomeController extends Controller
{
    /**
     * Landing page: katalog unggulan dari berbagai toko.
     */
    public function index()
    {
        // Admin tidak punya beranda publik — langsung diarahkan ke panel.
        if (auth()->check() && auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        $featuredPlants = Plant::with(['category', 'store', 'reviews'])
            ->tampil()
            ->featured()
            ->latest()
            ->take(8)
            ->get();

        $categories = Category::withCount(['plants' => fn ($q) => $q->tampil()])
            ->orderBy('nama')
            ->take(4)
            ->get();

        $tokoAktif = Store::aktif()->withCount(['plants' => fn ($q) => $q->where('is_active', true)])->latest()->take(3)->get();

        return view('home', [
            'featuredPlants' => $featuredPlants,
            'categories' => $categories,
            'tokoAktif' => $tokoAktif,
            'totalProduk' => Plant::tampil()->count(),
            'totalToko' => Store::aktif()->count(),
        ]);
    }
}
