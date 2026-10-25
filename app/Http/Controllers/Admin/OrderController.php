<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Store;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Pemantauan seluruh pesanan lintas toko.
     */
    public function index(Request $request)
    {
        $orders = Order::with(['store', 'buyer', 'items'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('toko'), fn ($q) => $q->where('store_id', $request->toko))
            ->when($request->filled('q'), fn ($q) => $q->where('kode_pesanan', 'like', '%' . $request->q . '%'))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $hitung = Order::selectRaw('status, count(*) as jumlah')->groupBy('status')->pluck('jumlah', 'status');
        $stores = Store::orderBy('nama_toko')->get();

        return view('admin.orders.index', compact('orders', 'hitung', 'stores'));
    }
}
