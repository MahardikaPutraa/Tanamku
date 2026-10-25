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

        return view('orders.show', compact('order', 'waUrl', 'waPesan'));
    }
}
