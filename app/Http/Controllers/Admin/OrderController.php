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
            ->when($request->filled('q'), fn ($q) => $q->whereLike('kode_pesanan', '%' . $request->q . '%'))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $hitung = Order::selectRaw('status, count(*) as jumlah')->groupBy('status')->pluck('jumlah', 'status');
        $stores = Store::orderBy('nama_toko')->get();

        return view('admin.orders.index', compact('orders', 'hitung', 'stores'));
    }

    /**
     * Ekspor seluruh data transaksi marketplace ke format CSV (Excel).
     */
    public function exportCsv(Request $request)
    {
        $orders = Order::with(['store', 'buyer', 'items'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('toko'), fn ($q) => $q->where('store_id', $request->toko))
            ->when($request->filled('q'), fn ($q) => $q->whereLike('kode_pesanan', '%' . $request->q . '%'))
            ->latest()
            ->get();

        $filename = 'laporan-pesanan-marketplace-' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($orders) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Kode Pesanan', 'Tanggal', 'Toko', 'Nama Pembeli', 'No HP', 'Alamat', 'Metode Serah', 'Produk', 'Total Harga (Rp)', 'Status']);

            foreach ($orders as $order) {
                $produkList = $order->items->map(fn ($i) => $i->jumlah . 'x ' . $i->nama_produk)->implode(', ');
                fputcsv($handle, [
                    $order->kode_pesanan,
                    $order->created_at->format('Y-m-d H:i'),
                    $order->store?->nama_toko ?? '-',
                    $order->nama_penerima,
                    $order->no_hp,
                    $order->alamat,
                    $order->metode_serah_label,
                    $produkList,
                    (float) $order->total_harga,
                    $order->status_label,
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
