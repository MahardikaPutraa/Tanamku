<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Alur status yang diizinkan.
     */
    private const ALUR = [
        'baru' => ['diproses', 'batal'],
        'diproses' => ['selesai', 'batal'],
        'selesai' => [],
        'batal' => [],
    ];

    /**
     * Pesanan yang masuk ke toko ini.
     */
    public function index(Request $request)
    {
        $store = auth()->user()->store;
        abort_if(! $store, 403, 'Anda belum memiliki toko.');

        $orders = Order::with(['items', 'buyer'])
            ->where('store_id', $store->id)
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $hitung = Order::where('store_id', $store->id)
            ->selectRaw('status, count(*) as jumlah')
            ->groupBy('status')
            ->pluck('jumlah', 'status');

        return view('seller.orders.index', compact('store', 'orders', 'hitung'));
    }

    /**
     * Ubah status pesanan. Bila dibatalkan, stok dikembalikan.
     */
    public function updateStatus(Request $request, Order $order)
    {
        $store = auth()->user()->store;
        abort_if(! $store || $order->store_id !== $store->id, 403);

        $data = $request->validate([
            'status' => ['required', 'in:baru,diproses,selesai,batal'],
        ]);

        $boleh = self::ALUR[$order->status] ?? [];

        if (! in_array($data['status'], $boleh, true)) {
            return back()->with('error', 'Status tidak bisa diubah dari "' . $order->status_label . '" ke "' . $data['status'] . '".');
        }

        if ($data['status'] === 'batal') {
            foreach ($order->items as $item) {
                $item->plant?->increment('stok', $item->jumlah);
            }
        }

        $order->update(['status' => $data['status']]);

        return back()->with('success', 'Status pesanan ' . $order->kode_pesanan . ' diubah menjadi "' . $order->status_label . '".');
    }
}
