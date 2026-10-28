<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\WhatsAppMessage;

class OrderController extends Controller
{
    /**
     * Daftar pesanan milik pembeli yang sedang login.
     */
    public function mine()
    {
        $orders = Order::with(['store', 'items'])
            ->where('buyer_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('orders.mine', compact('orders'));
    }

    /**
     * Detail pesanan + tautan WhatsApp ke penjual.
     */
    public function show(Order $order)
    {
        $user = auth()->user();

        $boleh = $order->buyer_id === $user->id
            || $user->isAdmin()
            || ($user->isSeller() && $user->store && $user->store->id === $order->store_id);

        abort_if(! $boleh, 403);

        $order->load(['store', 'items.plant', 'buyer']);

        $waUrl = WhatsAppMessage::url($order);
        $waPesan = WhatsAppMessage::untukOrder($order);

        $order->loadMissing(['reviews']);

        return view('orders.show', compact('order', 'waUrl', 'waPesan'));
    }

    /**
     * Pembeli mengonfirmasi bahwa pesanan telah diterima / selesai.
     */
    public function markCompleted(Order $order)
    {
        $user = auth()->user();

        abort_if($order->buyer_id !== $user->id, 403, 'Hanya pembeli yang dapat menyelesaikan pesanan ini.');

        if ($order->status !== 'diproses') {
            return back()->with('error', 'Hanya pesanan yang sedang diproses yang dapat dikonfirmasi selesai.');
        }

        $order->update(['status' => 'selesai']);

        return back()->with('success', 'Pesanan telah selesai! Silakan berikan ulasan dan rating di bawah.');
    }
}
