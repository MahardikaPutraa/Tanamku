@extends('layouts.app')

@section('title', 'Dashboard Admin — TanamKu')

@section('content')
    <div class="flex flex-col md:flex-row min-h-screen">
        @include('partials.sidebar')

        <div class="flex-1 min-w-0 p-4 sm:p-6 lg:p-8">
            @include('partials.alert')

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Dashboard Admin</h1>
                    <p class="mt-1 text-sm text-slate-500">Ringkasan operasional platform TanamKu, {{ now()->format('d F Y') }}.</p>
                </div>
            </div>

            {{-- 4 Stat Utama --}}
            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                @foreach ([
                    ['Pembeli', $statistik['pembeli'], 'akun terdaftar', 'bg-sky-50 text-sky-700 border-sky-200/80', '👤', 'from-sky-500/10 to-blue-500/5'],
                    ['Penjual', $statistik['penjual'], 'akun terdaftar', 'bg-emerald-50 text-emerald-700 border-emerald-200/80', '🏪', 'from-emerald-500/10 to-teal-500/5'],
                    ['Toko Aktif', $statistik['toko_aktif'], $statistik['toko_menunggu'] . ' menunggu', 'bg-primary-light text-primary border-emerald-200/80', '🌱', 'from-primary/10 to-emerald-500/5'],
                    ['Pesanan', $statistik['pesanan'], 'Rp ' . number_format($statistik['omzet'], 0, ',', '.') . ' omzet', 'bg-amber-50 text-amber-700 border-amber-200/80', '📦', 'from-amber-500/10 to-orange-500/5'],
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
                        <p class="mt-4 text-3xl font-extrabold text-slate-900 tracking-tight">{{ $kartu[1] }}</p>
                    </div>
                @endforeach
            </div>

            {{-- Metric Strip Tambahan --}}
            <div class="mt-4 grid grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-4">
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 sm:p-5">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Produk</p>
                    <p class="mt-2 text-2xl font-extrabold text-slate-900">{{ $statistik['produk'] }}</p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 sm:p-5">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Produk Disembunyikan</p>
                    <p class="mt-2 text-2xl font-extrabold {{ $statistik['produk_tersembunyi'] > 0 ? 'text-red-500' : 'text-slate-900' }}">{{ $statistik['produk_tersembunyi'] }}</p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 sm:p-5">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Toko Menunggu</p>
                    <p class="mt-2 text-2xl font-extrabold {{ $statistik['toko_menunggu'] > 0 ? 'text-amber-600' : 'text-slate-900' }}">{{ $statistik['toko_menunggu'] }}</p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 sm:p-5">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Omzet Tercatat</p>
                    <p class="mt-2 text-lg font-extrabold text-slate-900 truncate">Rp {{ number_format($statistik['omzet'], 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 xl:grid-cols-3 gap-6">
                {{-- Toko menunggu persetujuan --}}
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 sm:p-6">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                            <h2 class="text-sm font-bold text-slate-900">Menunggu Persetujuan</h2>
                        </div>
                        <a href="{{ route('admin.stores.index', ['status' => 'menunggu']) }}" class="text-xs font-bold text-primary hover:underline">Semua →</a>
                    </div>

                    <div class="mt-4 space-y-3">
                        @forelse ($tokoMenunggu as $toko)
                            <div class="p-3.5 rounded-xl bg-amber-50/60 border border-amber-200/80">
                                <div class="flex items-center justify-between">
                                    <p class="text-xs font-bold text-slate-900 truncate">{{ $toko->nama_toko }}</p>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-800">Menunggu</span>
                                </div>
                                <p class="text-[11px] text-slate-500 mt-0.5">📍 {{ $toko->kota }} · Owner: {{ $toko->user->name ?? '-' }}</p>

                                <form method="POST" action="{{ route('admin.stores.status', $toko) }}" class="mt-3">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="aktif">
                                    <button type="submit" class="w-full py-2 rounded-lg bg-primary text-white text-xs font-bold shadow-xs hover:bg-primary-hover hover:shadow transition-all">
                                        ✓ Setujui Toko
                                    </button>
                                </form>
                            </div>
                        @empty
                            <div class="text-center py-10">
                                <div class="w-10 h-10 mx-auto rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold mb-2">✓</div>
                                <p class="text-xs font-semibold text-slate-500">Tidak ada toko yang menunggu.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Pesanan terbaru --}}
                <div class="xl:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-primary"></span>
                            <h2 class="text-sm font-bold text-slate-900">Pesanan Terbaru</h2>
                        </div>
                        <a href="{{ route('admin.orders.index') }}" class="text-xs font-bold text-primary hover:underline">Semua Pesanan →</a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm min-w-[550px]">
                            <thead class="bg-slate-50/80 text-left border-b border-slate-100">
                                <tr class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                                    <th class="px-5 py-3.5">Kode Pesanan</th>
                                    <th class="px-5 py-3.5">Toko</th>
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
                                        <td class="px-5 py-3.5 text-slate-700 font-medium text-xs truncate">{{ $order->store->nama_toko }}</td>
                                        <td class="px-5 py-3.5 text-right font-extrabold text-slate-900 whitespace-nowrap">{{ $order->total_harga_rupiah }}</td>
                                        <td class="px-5 py-3.5 text-center">
                                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $order->status_badge }}">{{ $order->status_label }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="px-5 py-10 text-center text-sm text-slate-400">Belum ada pesanan terbaru.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
