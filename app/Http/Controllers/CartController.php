<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Plant;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Keranjang, dikelompokkan per toko (checkout dipisah per penjual).
     */
    public function index()
    {
        $items = CartItem::with(['plant.store', 'plant.category'])
            ->where('user_id', auth()->id())
            ->get()
            ->filter(fn (CartItem $item) => $item->plant
                && $item->plant->is_active
                && $item->plant->store->isAktif());

        $perToko = $items->groupBy(fn (CartItem $item) => $item->plant->store_id);
        $totalSemua = $items->sum(fn (CartItem $item) => $item->subtotal);

        return view('carts.index', compact('perToko', 'totalSemua'));
    }

    /**
     * Tambah produk ke keranjang (menumpuk bila sudah ada).
     */
    public function store(Request $request, Plant $plant)
    {
        abort_if(! $plant->is_active || ! $plant->store->isAktif(), 404);

        $data = $request->validate([
            'jumlah' => ['nullable', 'integer', 'min:1'],
        ]);

        $tambah = (int) ($data['jumlah'] ?? 1);

        if ($plant->stok < 1) {
            return back()->with('error', 'Stok ' . $plant->nama . ' sedang habis.');
        }

        $item = CartItem::firstOrNew([
            'user_id' => auth()->id(),
            'plant_id' => $plant->id,
        ]);

        $jumlahBaru = ($item->exists ? $item->jumlah : 0) + $tambah;

        if ($jumlahBaru > $plant->stok) {
            return back()->with('error', 'Stok ' . $plant->nama . ' tersisa ' . $plant->stok . ' unit.');
        }

        $item->jumlah = $jumlahBaru;
        $item->save();

        return back()->with('success', $plant->nama . ' ditambahkan ke keranjang.');
    }

    /**
     * Ubah jumlah item.
     */
    public function update(Request $request, CartItem $cartItem)
    {
        abort_if($cartItem->user_id !== auth()->id(), 403);

        $data = $request->validate([
            'jumlah' => ['required', 'integer', 'min:1'],
        ]);

        if ($data['jumlah'] > $cartItem->plant->stok) {
            return back()->with('error', 'Stok tersisa ' . $cartItem->plant->stok . ' unit.');
        }

        $cartItem->update(['jumlah' => $data['jumlah']]);

        return back()->with('success', 'Jumlah diperbarui.');
    }

    /**
     * Hapus item dari keranjang.
     */
    public function destroy(CartItem $cartItem)
    {
        abort_if($cartItem->user_id !== auth()->id(), 403);

        $cartItem->delete();

        return back()->with('success', 'Item dihapus dari keranjang.');
    }
}
