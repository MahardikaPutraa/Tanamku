<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        DB::transaction(function () use ($order, $data) {
            if ($data['status'] === 'batal') {
                foreach ($order->items as $item) {
                    $item->plant?->increment('stok', $item->jumlah);
                }
            }

            $order->update(['status' => $data['status']]);
        });

        return back()->with('success', 'Status pesanan ' . $order->kode_pesanan . ' diubah menjadi "' . $order->status_label . '".');
    }

    /**
     * Ekspor daftar pesanan toko ke format CSV (Excel).
     */
    public function exportCsv(Request $request)
    {
        $store = auth()->user()->store;
        abort_if(! $store, 403, 'Anda belum memiliki toko.');

        $orders = Order::with(['items', 'buyer'])
            ->where('store_id', $store->id)
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->get();

        $filename = 'laporan-pesanan-' . $store->slug . '-' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($orders) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Kode Pesanan', 'Tanggal', 'Nama Pembeli', 'No HP', 'Alamat', 'Metode Serah', 'Produk', 'Total Harga (Rp)', 'Status']);

            foreach ($orders as $order) {
                $produkList = $order->items->map(fn ($i) => $i->jumlah . 'x ' . $i->nama_produk)->implode(', ');
                fputcsv($handle, [
                    $order->kode_pesanan,
                    $order->created_at->format('Y-m-d H:i'),
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
