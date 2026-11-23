@extends('layouts.app')

@section('title', 'Pesanan Saya — TanamKu')

@section('content')
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        @include('partials.alert')

        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Pesanan Saya</h1>
                <p class="mt-1 text-sm text-slate-600">Riwayat pesanan yang Anda buat lewat TanamKu.</p>
            </div>
            <a href="{{ route('plants.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:bg-primary-hover shadow-sm transition">
                Belanja lagi
            </a>
        </div>

        @if ($orders->isEmpty())
            <div class="mt-8 text-center py-16 bg-white rounded-2xl border border-dashed border-slate-200 shadow-xs">
                <div class="w-12 h-12 mx-auto rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-900">Belum ada pesanan</h3>
                <p class="mt-1.5 text-sm text-slate-600">Pesanan yang Anda buat akan muncul di sini.</p>
                <a href="{{ route('plants.index') }}" class="mt-5 inline-flex px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:bg-primary-hover shadow-sm transition">
                    Lihat Katalog Tanaman
                </a>
            </div>
        @else
            <div class="mt-6 space-y-3.5">
                @foreach ($orders as $order)
                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 sm:p-5 hover:border-slate-300 transition-all">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-xs font-bold text-slate-800 bg-slate-100 px-2.5 py-0.5 rounded-md">{{ $order->kode_pesanan }}</span>
                                    <span class="text-xs text-slate-400">·</span>
                                    <span class="text-xs text-slate-500">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                <p class="mt-1.5 text-xs text-slate-600">
                                    Toko: <a href="{{ route('stores.show', $order->store) }}" class="font-semibold text-slate-900 hover:text-primary transition-colors">{{ $order->store->nama_toko }}</a>
                                </p>
                            </div>

                            <div class="flex items-center justify-between sm:justify-end gap-3 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-100">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $order->status_badge }}">
                                    {{ $order->status_label }}
                                </span>
                                <a href="{{ route('orders.show', $order) }}"
                                   class="px-3.5 py-1.5 rounded-xl text-xs font-semibold text-primary bg-primary/10 hover:bg-primary/20 border border-primary/20 transition-colors">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>

                        <div class="mt-3.5 pt-3.5 border-t border-slate-100 flex items-center justify-between gap-3">
                            <p class="text-sm text-slate-600 truncate">
                                {{ $order->items->take(2)->pluck('nama_produk')->join(', ') }}
                                @if ($order->items->count() > 2)
                                    <span class="text-slate-400 font-medium">+{{ $order->items->count() - 2 }} lainnya</span>
                                @endif
                            </p>
                            <p class="text-sm font-extrabold text-slate-900 whitespace-nowrap">{{ $order->total_harga_rupiah }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">{{ $orders->links() }}</div>
        @endif
    </div>
@endsection
