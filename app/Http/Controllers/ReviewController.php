<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Simpan ulasan tanaman dari pesanan yang sudah selesai.
     */
    public function store(Request $request, Order $order)
    {
        $user = auth()->user();

        abort_if($order->buyer_id !== $user->id, 403, 'Anda bukan pemilik pesanan ini.');
        abort_if($order->status !== 'selesai', 422, 'Ulasan hanya dapat diberikan untuk pesanan yang sudah selesai.');

        $data = $request->validate([
            'plant_id' => ['required', 'exists:plants,id'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'komentar' => ['nullable', 'string', 'max:1000'],
        ]);

        // Pastikan tanaman ini memang dibeli dalam pesanan ini
        $dibeli = $order->items()->where('plant_id', $data['plant_id'])->exists();
        abort_if(! $dibeli, 422, 'Produk ini tidak ada dalam pesanan Anda.');

        Review::updateOrCreate(
            [
                'order_id' => $order->id,
                'plant_id' => $data['plant_id'],
            ],
            [
                'user_id' => $user->id,
                'rating' => $data['rating'],
                'komentar' => $data['komentar'] ?: null,
            ]
        );

        return back()->with('success', 'Terima kasih! Ulasan dan rating Anda berhasil disimpan.');
    }
}

