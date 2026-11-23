@extends('layouts.app')

@section('title', 'Semua Pesanan — TanamKu')

@section('content')
    <div class="flex flex-col md:flex-row min-h-screen">
        @include('partials.sidebar')

        <div class="flex-1 min-w-0 p-4 sm:p-6 lg:p-8">
            @include('partials.alert')

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Semua Pesanan</h1>
                    <p class="mt-1 text-sm text-slate-500">{{ $orders->total() }} total transaksi tercatat di seluruh toko.</p>
                </div>

                <a href="{{ route('admin.orders.export', request()->only(['status', 'toko', 'q'])) }}"
                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/90 hover:bg-emerald-100 shadow-2xs transition-all shrink-0">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    <span>Unduh CSV Transaksi</span>
                </a>
            </div>

            {{-- Status Tabs --}}
            <div class="mt-6 flex flex-wrap gap-2">
                <a href="{{ route('admin.orders.index') }}"
                   class="px-4 py-2 rounded-xl text-xs font-bold border transition-all {{ ! request('status') ? 'bg-primary text-white border-primary shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:border-slate-300' }}">
                    Semua ({{ $hitung->sum() }})
                </a>
                @foreach (['baru', 'diproses', 'selesai', 'batal'] as $status)
                    <a href="{{ route('admin.orders.index', ['status' => $status]) }}"
                       class="px-4 py-2 rounded-xl text-xs font-bold border transition-all {{ request('status') === $status ? 'bg-primary text-white border-primary shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:border-slate-300' }}">
                        {{ ucfirst($status) }} ({{ $hitung[$status] ?? 0 }})
                    </a>
                @endforeach
            </div>

            {{-- Filter Form --}}
            <form method="GET" action="{{ route('admin.orders.index') }}" class="mt-4 flex flex-col sm:flex-row gap-3">
                @if (request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif

                <div class="relative flex-grow">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari kode pesanan (contoh: ORD-)..."
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20 bg-white">
                </div>

                <select name="toko" class="px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm bg-white outline-none transition sm:w-56 focus:border-primary focus:ring-2 focus:ring-primary/20">
                    <option value="">Semua Toko Mitra</option>
                    @foreach ($stores as $toko)
                        <option value="{{ $toko->id }}" @selected((string) request('toko') === (string) $toko->id)>{{ $toko->nama_toko }}</option>
                    @endforeach
                </select>

                <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary text-white text-xs font-bold shadow-xs hover:bg-primary-hover hover:shadow transition-all">Filter</button>

                @if (request()->hasAny(['q', 'toko']))
                    <a href="{{ route('admin.orders.index', request()->only('status')) }}" class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors text-center">Reset</a>
                @endif
            </form>

            {{-- Table --}}
            <div class="mt-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm min-w-[650px]">
                        <thead class="bg-slate-50/80 text-left border-b border-slate-100">
                            <tr class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                                <th class="px-5 py-3.5">Kode & Waktu</th>
                                <th class="px-5 py-3.5">Toko Mitra</th>
                                <th class="px-5 py-3.5">Penerima & Pembeli</th>
                                <th class="px-5 py-3.5 text-right">Total Nilai</th>
                                <th class="px-5 py-3.5 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($orders as $order)
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="px-5 py-3.5">
                                        <a href="{{ route('orders.show', $order) }}" class="font-mono text-xs font-bold text-slate-800 hover:text-primary transition-colors">
                                            {{ $order->kode_pesanan }}
                                        </a>
                                        <p class="text-[10px] text-slate-400 mt-0.5">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                                    </td>
                                    <td class="px-5 py-3.5 text-slate-800 font-semibold text-xs">{{ $order->store->nama_toko }}</td>
                                    <td class="px-5 py-3.5 text-slate-700 text-xs">
                                        <p class="font-bold text-slate-900">{{ $order->nama_penerima }}</p>
                                        <span class="text-[11px] text-slate-400 font-mono">{{ $order->buyer->email ?? 'Pembeli Tamu' }}</span>
                                    </td>
                                    <td class="px-5 py-3.5 text-right font-extrabold text-slate-900 whitespace-nowrap">{{ $order->total_harga_rupiah }}</td>
                                    <td class="px-5 py-3.5 text-center">
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $order->status_badge }}">{{ $order->status_label }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-5 py-12 text-center text-sm text-slate-400">Tidak ada transaksi ditemukan dengan filter ini.</td></tr>
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
