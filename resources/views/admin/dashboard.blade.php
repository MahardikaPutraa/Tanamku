@extends('layouts.app')

@section('title', 'Dashboard Admin — TanamKu')

@section('content')
    <div class="flex">
        @include('partials.sidebar')

        <div class="flex-1 min-w-0 p-5 lg:p-8">
            @include('partials.alert')

            <div>
                <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Dashboard Admin</h1>
                <p class="mt-1 text-sm text-gray-600">Ringkasan platform TanamKu, {{ now()->format('d F Y') }}.</p>
            </div>

            {{-- Statistik --}}
            <div class="mt-6 grid grid-cols-2 xl:grid-cols-4 gap-4">
                @foreach ([
                    ['Pembeli', $statistik['pembeli'], 'akun pembeli', 'bg-sky-50 text-sky-700 border-sky-100'],
                    ['Penjual', $statistik['penjual'], 'akun penjual', 'bg-emerald-50 text-emerald-700 border-emerald-100'],
                    ['Toko Aktif', $statistik['toko_aktif'], $statistik['toko_menunggu'] . ' menunggu', 'bg-primary-light text-primary border-emerald-200'],
                    ['Pesanan', $statistik['pesanan'], 'Rp ' . number_format($statistik['omzet'], 0, ',', '.') . ' omzet', 'bg-amber-50 text-amber-700 border-amber-100'],
                ] as $kartu)
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <p class="text-[11px] font-bold text-gray-500 uppercase tracking-wide">{{ $kartu[0] }}</p>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $kartu[3] }}">
                                {{ $kartu[2] }}
                            </span>
                        </div>
                        <p class="mt-3 text-2xl font-extrabold text-gray-900">{{ $kartu[1] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="mt-4 grid grid-cols-2 xl:grid-cols-4 gap-4">
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                    <p class="text-[11px] font-semibold text-gray-500 uppercase">Total Produk</p>
                    <p class="mt-2 text-2xl font-extrabold text-gray-900">{{ $statistik['produk'] }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                    <p class="text-[11px] font-semibold text-gray-500 uppercase">Produk Disembunyikan</p>
                    <p class="mt-2 text-2xl font-extrabold {{ $statistik['produk_tersembunyi'] > 0 ? 'text-danger' : 'text-gray-900' }}">{{ $statistik['produk_tersembunyi'] }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                    <p class="text-[11px] font-semibold text-gray-500 uppercase">Toko Menunggu</p>
                    <p class="mt-2 text-2xl font-extrabold {{ $statistik['toko_menunggu'] > 0 ? 'text-amber-600' : 'text-gray-900' }}">{{ $statistik['toko_menunggu'] }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                    <p class="text-[11px] font-semibold text-gray-500 uppercase">Omzet Tercatat</p>
                    <p class="mt-2 text-lg font-extrabold text-gray-900 truncate">Rp {{ number_format($statistik['omzet'], 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 xl:grid-cols-3 gap-6">
                {{-- Toko menunggu --}}
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                    <div class="flex items-center justify-between">
                        <h2 class="text-sm font-bold text-gray-900">Menunggu Persetujuan</h2>
                        <a href="{{ route('admin.stores.index', ['status' => 'menunggu']) }}" class="text-xs font-semibold text-primary hover:underline">Semua →</a>
                    </div>

                    <div class="mt-4 space-y-3">
                        @forelse ($tokoMenunggu as $toko)
                            <div class="p-3 rounded-lg bg-amber-50 border border-amber-200">
                                <p class="text-xs font-bold text-gray-900 truncate">{{ $toko->nama_toko }}</p>
                                <p class="text-[11px] text-gray-600">{{ $toko->kota }} · {{ $toko->user->name ?? '-' }}</p>

                                <form method="POST" action="{{ route('admin.stores.status', $toko) }}" class="mt-2">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="aktif">
                                    <button type="submit" class="w-full py-1.5 rounded-md bg-primary text-white text-[11px] font-bold hover:bg-primary-hover">
                                        Setujui Toko
                                    </button>
                                </form>
                            </div>
                        @empty
                            <p class="text-xs text-gray-500 py-8 text-center">Tidak ada toko yang menunggu. 👍</p>
                        @endforelse
                    </div>
                </div>

                {{-- Pesanan terbaru --}}
                <div class="xl:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h2 class="text-sm font-bold text-gray-900">Pesanan Terbaru</h2>
                        <a href="{{ route('admin.orders.index') }}" class="text-xs font-semibold text-primary hover:underline">Semua →</a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-left">
                                <tr class="text-[11px] font-semibold text-gray-600 uppercase tracking-wide">
                                    <th class="px-5 py-3">Kode</th>
                                    <th class="px-5 py-3">Toko</th>
                                    <th class="px-5 py-3 text-right">Total</th>
                                    <th class="px-5 py-3 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($pesananTerbaru as $order)
                                    <tr class="hover:bg-gray-50/60">
                                        <td class="px-5 py-3">
                                            <a href="{{ route('orders.show', $order) }}" class="font-mono text-xs text-gray-800 hover:text-primary">{{ $order->kode_pesanan }}</a>
                                        </td>
                                        <td class="px-5 py-3 text-gray-700 truncate">{{ $order->store->nama_toko }}</td>
                                        <td class="px-5 py-3 text-right font-semibold text-gray-900 whitespace-nowrap">{{ $order->total_harga_rupiah }}</td>
                                        <td class="px-5 py-3 text-center">
                                            <span class="px-2 py-0.5 rounded-full text-[11px] font-bold border {{ $order->status_badge }}">{{ $order->status_label }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="px-5 py-10 text-center text-sm text-gray-500">Belum ada pesanan.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Toko teratas --}}
            <div class="mt-6 bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <h2 class="text-sm font-bold text-gray-900">Toko Paling Aktif</h2>
                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                    @forelse ($tokoTeratas as $toko)
                        <a href="{{ route('stores.show', $toko) }}" class="p-3 rounded-lg border border-gray-100 hover:border-primary/30 hover:shadow-sm transition-all">
                            <p class="text-xs font-bold text-gray-900 truncate">{{ $toko->nama_toko }}</p>
                            <p class="text-[11px] text-gray-500">{{ $toko->plants_count }} produk</p>
                        </a>
                    @empty
                        <p class="text-xs text-gray-500">Belum ada toko aktif.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
