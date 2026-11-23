@extends('layouts.app')

@section('title', 'Pesanan Masuk — TanamKu')

@php
    $alur = [
        'baru' => ['diproses', 'batal'],
        'diproses' => ['selesai', 'batal'],
        'selesai' => [],
        'batal' => [],
    ];
@endphp

@section('content')
    <div class="flex flex-col md:flex-row min-h-screen">
        @include('partials.sidebar')

        <div class="flex-1 min-w-0 p-4 sm:p-6 lg:p-8">
            @include('partials.alert')

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Pesanan Masuk</h1>
                    <p class="mt-1 text-sm text-slate-500">{{ $orders->total() }} pesanan untuk toko <strong>{{ $store->nama_toko }}</strong>.</p>
                </div>

                <a href="{{ route('seller.orders.export', request()->only('status')) }}"
                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/90 hover:bg-emerald-100 shadow-2xs transition-all shrink-0">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    <span>Unduh Laporan (CSV)</span>
                </a>
            </div>

            {{-- Status Filter Tabs --}}
            <div class="mt-6 flex flex-wrap gap-2">
                <a href="{{ route('seller.orders.index') }}"
                   class="px-4 py-2 rounded-xl text-xs font-bold border transition-all {{ ! request('status') ? 'bg-primary text-white border-primary shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:border-slate-300' }}">
                    Semua ({{ $hitung->sum() }})
                </a>
                @foreach (['baru', 'diproses', 'selesai', 'batal'] as $status)
                    <a href="{{ route('seller.orders.index', ['status' => $status]) }}"
                       class="px-4 py-2 rounded-xl text-xs font-bold border transition-all {{ request('status') === $status ? 'bg-primary text-white border-primary shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:border-slate-300' }}">
                        {{ ucfirst($status) }} ({{ $hitung[$status] ?? 0 }})
                    </a>
                @endforeach
            </div>

            {{-- Order Table Card --}}
            <div class="mt-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm min-w-[650px]">
                        <thead class="bg-slate-50/80 text-left border-b border-slate-100">
                            <tr class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                                <th class="px-5 py-3.5">Kode & Waktu</th>
                                <th class="px-5 py-3.5">Pembeli</th>
                                <th class="px-5 py-3.5">Item Produk</th>
                                <th class="px-5 py-3.5 text-right">Total Nilai</th>
                                <th class="px-5 py-3.5 text-center">Status</th>
                                <th class="px-5 py-3.5 text-right">Ubah Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($orders as $order)
                                <tr class="hover:bg-slate-50/60 transition-colors align-top">
                                    <td class="px-5 py-3.5">
                                        <a href="{{ route('orders.show', $order) }}" class="font-mono text-xs font-bold text-slate-800 hover:text-primary transition-colors">
                                            {{ $order->kode_pesanan }}
                                        </a>
                                        <p class="text-[11px] text-slate-400 mt-0.5">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <p class="font-bold text-slate-900 text-xs">{{ $order->nama_penerima }}</p>
                                        <p class="text-[11px] text-slate-500 font-mono">{{ $order->no_hp }}</p>
                                        <p class="text-[11px] text-slate-400 max-w-[13rem] truncate mt-0.5" title="{{ $order->alamat }}">{{ $order->alamat }}</p>
                                    </td>
                                    <td class="px-5 py-3.5 text-slate-700">
                                        @foreach ($order->items as $item)
                                            <p class="text-xs font-medium text-slate-800">{{ $item->jumlah }}× {{ $item->nama_produk }}</p>
                                        @endforeach
                                    </td>
                                    <td class="px-5 py-3.5 text-right font-extrabold text-slate-900 whitespace-nowrap">{{ $order->total_harga_rupiah }}</td>
                                    <td class="px-5 py-3.5 text-center">
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $order->status_badge }}">{{ $order->status_label }}</span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        @php $lanjut = $alur[$order->status] ?? []; @endphp
                                        @if (empty($lanjut))
                                            <p class="text-[11px] font-semibold text-slate-400 text-right">Final</p>
                                        @else
                                            <form method="POST" action="{{ route('seller.orders.status', $order) }}" class="flex items-center justify-end gap-2">
                                                @csrf
                                                @method('PATCH')
                                                <select name="status"
                                                        class="px-2.5 py-1.5 rounded-lg border border-slate-200 text-xs font-semibold bg-white text-slate-700 outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition cursor-pointer">
                                                    @foreach ($lanjut as $st)
                                                        <option value="{{ $st }}">{{ ucfirst($st) }}</option>
                                                    @endforeach
                                                </select>
                                                <button type="submit"
                                                        class="px-3 py-1.5 rounded-lg text-xs font-bold text-white bg-primary hover:bg-primary-hover shadow-2xs transition-all">
                                                    Update
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-5 py-12 text-center text-sm text-slate-400">Belum ada pesanan dengan status ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($orders->hasPages())
                    <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/50">
                        {{ $orders->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
