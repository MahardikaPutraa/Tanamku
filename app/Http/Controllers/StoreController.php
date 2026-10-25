<?php

namespace App\Http\Controllers;

use App\Models\Store;

class StoreController extends Controller
{
    /**
     * Halaman publik toko: profil + produk milik seller tersebut.
     */
    public function show(Store $store)
    {
        abort_if(! $store->isAktif(), 404);

        $plants = $store->plants()
            ->with('category')
            ->where('is_active', true)
            ->latest()
            ->paginate(12);

        return view('stores.show', compact('store', 'plants'));
    }
}
