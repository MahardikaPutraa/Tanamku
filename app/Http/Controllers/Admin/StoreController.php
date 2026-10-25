<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        $stores = Store::with('user')
            ->withCount('plants')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('q'), fn ($q) => $q->where('nama_toko', 'like', '%' . $request->q . '%'))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $hitung = Store::selectRaw('status, count(*) as jumlah')->groupBy('status')->pluck('jumlah', 'status');

        return view('admin.stores.index', compact('stores', 'hitung'));
    }

    /**
     * Setujui / tolak / nonaktifkan toko.
     */
    public function updateStatus(Request $request, Store $store)
    {
        $data = $request->validate([
            'status' => ['required', 'in:menunggu,aktif,nonaktif'],
        ]);

        $store->update(['status' => $data['status']]);

        $pesan = match ($data['status']) {
            'aktif' => 'Toko "' . $store->nama_toko . '" disetujui dan produknya kini tampil di katalog.',
            'nonaktif' => 'Toko "' . $store->nama_toko . '" dinonaktifkan; produknya disembunyikan dari katalog.',
            default => 'Status toko "' . $store->nama_toko . '" dikembalikan ke menunggu.',
        };

        return back()->with('success', $pesan);
    }
}
