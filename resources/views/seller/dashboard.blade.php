@extends('layouts.app')

@section('title', 'Dashboard Toko — TanamKu')

@section('content')
    <div class="flex flex-col md:flex-row min-h-screen">
        @include('partials.sidebar')

        <div class="flex-1 min-w-0 p-4 sm:p-6 lg:p-8">
            @include('partials.alert')

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ $store->nama_toko }}</h1>
                    <p class="mt-1 text-sm text-slate-500">📍 {{ $store->kota }} · {{ $store->metode_serah_label }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('stores.show', $store) }}" class="px-4 py-2.5 rounded-xl bg-slate-100 text-xs font-bold text-slate-700 hover:bg-slate-200 transition-colors">
                        Lihat Toko Publik →
                    </a>
                    <a href="{{ route('seller.plants.create') }}" class="px-4 py-2.5 rounded-xl bg-primary text-white text-xs font-bold shadow-xs hover:bg-primary-hover hover:shadow transition-all">
                        ＋ Tambah Produk Baru
                    </a>
                </div>
            </div>

            @if (! $store->isAktif())
                <div class="mt-5 p-4 rounded-2xl border text-sm shadow-2xs
                    {{ $store->status === 'menunggu' ? 'bg-amber-50/80 border-amber-200 text-amber-900' : 'bg-red-50/80 border-red-200 text-red-900' }}">
                    @if ($store->status === 'menunggu')
                        <div class="flex items-center gap-2.5 font-bold mb-1">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                            <span>Toko sedang menunggu persetujuan admin</span>
                        </div>
                        <p class="text-xs text-amber-800 leading-relaxed">Produk Anda sudah bisa diunggah, tetapi belum tampil di katalog publik sampai toko disetujui admin.</p>
                    @else
                        <div class="flex items-center gap-2.5 font-bold mb-1">
                            <span class="w-2.5 h-2.5 rounded-full bg-red-500"></span>
                            <span>Toko dinonaktifkan sementara</span>
                        </div>
                        <p class="text-xs text-red-800 leading-relaxed">Produk Anda tidak tampil di katalog publik. Hubungi administrator TanamKu untuk informasi lebih lanjut.</p>
                    @endif
                </div>
            @endif

            {{-- 4 Stat Penjual --}}
            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                @foreach ([
                    ['Produk', $statistik['produk'], $statistik['produk_aktif'] . ' aktif', 'bg-emerald-50 text-emerald-700 border-emerald-200/80', '🌿', 'from-emerald-500/10 to-teal-500/5'],
                    ['Stok Menipis', $statistik['stok_menipis'], 'stok < 5', 'bg-red-50 text-red-700 border-red-200/80', '⚠️', 'from-red-500/10 to-rose-500/5'],
                    ['Pesanan Baru', $statistik['pesanan_baru'], $statistik['pesanan_total'] . ' total', 'bg-amber-50 text-amber-700 border-amber-200/80', '📬', 'from-amber-500/10 to-orange-500/5'],
                    ['Omzet', 'Rp ' . number_format($statistik['omzet'], 0, ',', '.'), 'tanpa yang batal', 'bg-sky-50 text-sky-700 border-sky-200/80', '💰', 'from-sky-500/10 to-blue-500/5'],
                ] as $kartu)
                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 p-5 flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-xl bg-gradient-to-br {{ $kartu[5] }} flex items-center justify-center text-sm shadow-2xs">{{ $kartu[4] }}</span>
                                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">{{ $kartu[0] }}</p>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $kartu[3] }}">
                                {{ $kartu[2] }}
                            </span>
                        </div>
                        <p class="mt-4 text-3xl font-extrabold text-slate-900 tracking-tight truncate">{{ $kartu[1] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 grid grid-cols-1 xl:grid-cols-3 gap-6">
                {{-- Pesanan terbaru --}}
                <div class="xl:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-primary"></span>
                            <h2 class="text-sm font-bold text-slate-900">Pesanan Masuk Terbaru</h2>
                        </div>
                        <a href="{{ route('seller.orders.index') }}" class="text-xs font-bold text-primary hover:underline">Semua Pesanan →</a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm min-w-[550px]">
                            <thead class="bg-slate-50/80 text-left border-b border-slate-100">
                                <tr class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                                    <th class="px-5 py-3.5">Kode Pesanan</th>
                                    <th class="px-5 py-3.5">Pembeli</th>
                                    <th class="px-5 py-3.5 text-right">Total Nilai</th>
                                    <th class="px-5 py-3.5 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($pesananTerbaru as $order)
                                    <tr class="hover:bg-slate-50/60 transition-colors">
                                        <td class="px-5 py-3.5">
                                            <a href="{{ route('orders.show', $order) }}" class="font-mono text-xs font-bold text-slate-800 hover:text-primary transition-colors">
                                                {{ $order->kode_pesanan }}
                                            </a>
                                            <span class="block text-[10px] text-slate-400 mt-0.5">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                                        </td>
                                        <td class="px-5 py-3.5 text-slate-700 font-medium text-xs">{{ $order->nama_penerima }}</td>
                                        <td class="px-5 py-3.5 text-right font-extrabold text-slate-900 whitespace-nowrap">{{ $order->total_harga_rupiah }}</td>
                                        <td class="px-5 py-3.5 text-center">
                                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $order->status_badge }}">{{ $order->status_label }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="px-5 py-10 text-center text-sm text-slate-400">Belum ada pesanan masuk.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Stok Menipis --}}
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 sm:p-6">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-red-500 animate-pulse"></span>
                            <h2 class="text-sm font-bold text-slate-900">Perlu Restock</h2>
                        </div>
                        <a href="{{ route('seller.plants.index') }}" class="text-xs font-bold text-primary hover:underline">Semua Produk →</a>
                    </div>

                    <div class="mt-4 space-y-3">
                        @forelse ($produkMenipis as $item)
                            <div class="flex items-center justify-between gap-3 p-3 rounded-xl bg-slate-50 border border-slate-200/70">
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-slate-900 truncate">{{ $item->nama }}</p>
                                    <p class="text-[11px] text-slate-500 font-semibold">{{ $item->harga_rupiah }}</p>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="px-2 py-0.5 rounded-full text-[11px] font-extrabold {{ $item->stok <= 0 ? 'bg-red-100 text-red-700 border border-red-200' : 'bg-amber-100 text-amber-800 border border-amber-200' }}">
                                        Sisa {{ $item->stok }}
                                    </span>
                                    <a href="{{ route('seller.plants.edit', $item) }}" class="px-2 py-1 rounded-lg text-xs font-bold text-primary bg-primary/10 hover:bg-primary/20 transition-colors">
                                        Edit
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-10">
                                <div class="w-10 h-10 mx-auto rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold mb-2">✓</div>
                                <p class="text-xs font-semibold text-slate-500">Semua stok tanaman aman.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
