<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plant;
use App\Models\Store;
use Illuminate\Http\Request;

class PlantController extends Controller
{
    /**
     * Moderasi seluruh produk lintas toko.
     */
    public function index(Request $request)
    {
        $plants = Plant::with(['store', 'category'])
            ->when($request->filled('q'), fn ($q) => $q->where('nama', 'like', '%' . $request->q . '%'))
            ->when($request->filled('toko'), fn ($q) => $q->where('store_id', $request->toko))
            ->when($request->filled('status'), function ($q) use ($request) {
                $request->status === 'tersembunyi'
                    ? $q->where('is_active', false)
                    : $q->where('is_active', true);
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $stores = Store::orderBy('nama_toko')->get();

        return view('admin.plants.index', compact('plants', 'stores'));
    }

    /**
     * Sembunyikan / tampilkan produk.
     */
    public function toggleActive(Plant $plant)
    {
        $plant->update(['is_active' => ! $plant->is_active]);

        return back()->with('success', 'Produk "' . $plant->nama . '" sekarang '
            . ($plant->is_active ? 'tampil' : 'disembunyikan') . ' di katalog.');
    }

    /**
     * Hapus produk. Bila sudah pernah dipesan, cukup disembunyikan.
     */
    public function destroy(Plant $plant)
    {
        if ($plant->orderItems()->exists()) {
            $plant->update(['is_active' => false]);

            return back()->with('warning', 'Produk "' . $plant->nama . '" sudah pernah dipesan, jadi hanya disembunyikan agar riwayat pesanan tetap utuh.');
        }

        $plant->delete();

        return back()->with('success', 'Produk "' . $plant->nama . '" dihapus permanen.');
    }
}
