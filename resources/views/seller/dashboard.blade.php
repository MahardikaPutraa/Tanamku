@extends('layouts.app')

@section('title', 'Dashboard Toko — TanamKu')

@section('content')
    <div class="flex">
        @include('partials.sidebar')

        <div class="flex-1 min-w-0 p-5 lg:p-8">
            @include('partials.alert')

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">{{ $store->nama_toko }}</h1>
                    <p class="mt-1 text-sm text-gray-600">{{ $store->kota }} · {{ $store->metode_serah_label }}</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('stores.show', $store) }}" class="px-4 py-2.5 rounded-lg bg-gray-100 text-sm font-semibold text-gray-700 hover:bg-gray-200">
                        Lihat toko publik
                    </a>
                    <a href="{{ route('seller.plants.create') }}" class="px-4 py-2.5 rounded-lg bg-primary text-white text-sm font-semibold hover:bg-primary-hover">
                        + Tambah Produk
                    </a>
                </div>
            </div>

            @if (! $store->isAktif())
                <div class="mt-5 p-4 rounded-xl border text-sm
                    {{ $store->status === 'menunggu' ? 'bg-amber-50 border-amber-200 text-amber-900' : 'bg-red-50 border-red-200 text-red-900' }}">
                    @if ($store->status === 'menunggu')
                        <strong>Toko menunggu persetujuan admin.</strong>
                        Produk Anda sudah bisa diunggah, tetapi belum tampil di katalog sampai toko disetujui.
                    @else
                        <strong>Toko sedang dinonaktifkan admin.</strong>
                        Produk Anda tidak tampil di katalog. Hubungi admin untuk informasi lebih lanjut.
                    @endif
                </div>
            @endif

            {{-- Statistik --}}
            <div class="mt-6 grid grid-cols-2 xl:grid-cols-4 gap-4">
                @foreach ([
                    ['Produk', $statistik['produk'], $statistik['produk_aktif'] . ' aktif', 'bg-emerald-50 text-emerald-600'],
                    ['Stok Menipis', $statistik['stok_menipis'], 'stok < 5', 'bg-red-50 text-danger'],
                    ['Pesanan Baru', $statistik['pesanan_baru'], $statistik['pesanan_total'] . ' total', 'bg-amber-50 text-amber-600'],
                    ['Omzet', 'Rp ' . number_format($statistik['omzet'], 0, ',', '.'), 'tanpa yang batal', 'bg-sky-50 text-sky-600'],
                ] as $kartu)
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                        <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wide">{{ $kartu[0] }}</p>
                        <p class="mt-2 text-2xl font-extrabold text-gray-900 truncate">{{ $kartu[1] }}</p>
                        <p class="mt-1 text-[11px] text-gray-500">{{ $kartu[2] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 grid grid-cols-1 xl:grid-cols-3 gap-6">
                {{-- Pesanan terbaru --}}
                <div class="xl:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h2 class="text-sm font-bold text-gray-900">Pesanan Terbaru</h2>
                        <a href="{{ route('seller.orders.index') }}" class="text-xs font-semibold text-primary hover:underline">Lihat semua →</a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-left">
                                <tr class="text-[11px] font-semibold text-gray-600 uppercase tracking-wide">
                                    <th class="px-5 py-3">Kode</th>
                                    <th class="px-5 py-3">Pembeli</th>
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
                                        <td class="px-5 py-3 text-gray-700">{{ $order->nama_penerima }}</td>
                                        <td class="px-5 py-3 text-right font-semibold text-gray-900 whitespace-nowrap">{{ $order->total_harga_rupiah }}</td>
                                        <td class="px-5 py-3 text-center">
                                            <span class="px-2 py-0.5 rounded-full text-[11px] font-bold border {{ $order->status_badge }}">{{ $order->status_label }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="px-5 py-10 text-center text-sm text-gray-500">Belum ada pesanan masuk.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Stok menipis --}}
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                    <h2 class="text-sm font-bold text-gray-900">Perlu Restock</h2>

                    <div class="mt-4 space-y-3">
                        @forelse ($produkMenipis as $item)
                            <div class="flex items-center justify-between gap-3 p-2.5 rounded-lg bg-gray-50 border border-gray-100">
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-gray-900 truncate">{{ $item->nama }}</p>
                                    <p class="text-[11px] text-gray-500">{{ $item->harga_rupiah }}</p>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="text-xs font-extrabold {{ $item->stok <= 0 ? 'text-danger' : 'text-amber-600' }}">{{ $item->stok }}</span>
                                    <a href="{{ route('seller.plants.edit', $item) }}" class="text-[11px] font-semibold text-primary hover:underline">Edit</a>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-gray-500 py-8 text-center">Semua stok aman. 👍</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
