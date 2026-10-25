@extends('layouts.app')

@section('title', 'Pesanan Saya — TanamKu')

@section('content')
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        @include('partials.alert')

        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Pesanan Saya</h1>
                <p class="mt-1 text-sm text-gray-600">Riwayat pesanan yang Anda buat lewat TanamKu.</p>
            </div>
            <a href="{{ route('plants.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-primary text-white text-sm font-semibold hover:bg-primary-hover">
                Belanja lagi
            </a>
        </div>

        @if ($orders->isEmpty())
            <div class="mt-8 text-center py-20 bg-white rounded-xl border border-dashed border-gray-200">
                <h3 class="text-base font-bold text-gray-900">Belum ada pesanan</h3>
                <p class="mt-1.5 text-sm text-gray-600">Pesanan yang Anda buat akan muncul di sini.</p>
                <a href="{{ route('plants.index') }}" class="mt-5 inline-flex px-4 py-2 rounded-lg bg-primary text-white text-sm font-semibold hover:bg-primary-hover">
                    Lihat Katalog
                </a>
            </div>
        @else
            <div class="mt-6 space-y-4">
                @foreach ($orders as $order)
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div>
                                <p class="font-mono text-xs font-semibold text-gray-800">{{ $order->kode_pesanan }}</p>
                                <p class="mt-1 text-xs text-gray-500">
                                    {{ $order->created_at->format('d/m/Y H:i') }} ·
                                    <a href="{{ route('stores.show', $order->store) }}" class="hover:text-primary font-medium">{{ $order->store->nama_toko }}</a>
                                </p>
                            </div>

                            <div class="flex items-center gap-3">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $order->status_badge }}">
                                    {{ $order->status_label }}
                                </span>
                                <a href="{{ route('orders.show', $order) }}"
                                   class="px-3 py-1.5 rounded-lg text-xs font-semibold text-primary bg-primary-light hover:bg-emerald-100">
                                    Detail
                                </a>
                            </div>
                        </div>

                        <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between gap-3">
                            <p class="text-sm text-gray-600 truncate">
                                {{ $order->items->take(2)->pluck('nama_produk')->join(', ') }}
                                @if ($order->items->count() > 2)
                                    <span class="text-gray-400">+{{ $order->items->count() - 2 }} lainnya</span>
                                @endif
                            </p>
                            <p class="text-sm font-extrabold text-gray-900 whitespace-nowrap">{{ $order->total_harga_rupiah }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">{{ $orders->links() }}</div>
        @endif
    </div>
@endsection
