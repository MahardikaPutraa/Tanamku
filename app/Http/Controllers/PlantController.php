<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Plant;
use Illuminate\Http\Request;

class PlantController extends Controller
{
    /**
     * Katalog gabungan dari semua toko aktif.
     */
    public function index(Request $request)
    {
        $plants = Plant::with(['category', 'store'])
            ->tampil()
            ->when($request->filled('q'), fn ($q) => $q->where('nama', 'like', '%' . $request->q . '%'))
            ->when($request->filled('kategori'), fn ($q) => $q->whereHas(
                'category',
                fn ($c) => $c->where('slug', $request->kategori)
            ))
            ->when($request->filled('toko'), fn ($q) => $q->whereHas(
                'store',
                fn ($s) => $s->where('slug', $request->toko)
            ))
            ->when($request->filled('urut'), function ($q) use ($request) {
                match ($request->urut) {
                    'murah' => $q->orderBy('harga'),
                    'mahal' => $q->orderByDesc('harga'),
                    default => $q->latest(),
                };
            }, fn ($q) => $q->latest())
            ->paginate(12)
            ->withQueryString();

        $categories = Category::orderBy('nama')->get();

        return view('plants.index', compact('plants', 'categories'));
    }

    /**
     * Detail produk + tombol tambah ke keranjang.
     */
    public function show(Plant $plant)
    {
        abort_if(! $plant->is_active || ! $plant->store->isAktif(), 404);

        $plant->load(['category', 'store']);

        $lainnya = Plant::with(['category', 'store'])
            ->tampil()
            ->where('store_id', $plant->store_id)
            ->whereKeyNot($plant->getKey())
            ->take(4)
            ->get();

        return view('plants.show', compact('plant', 'lainnya'));
    }
}
