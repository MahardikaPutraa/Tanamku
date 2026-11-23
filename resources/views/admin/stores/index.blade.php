@extends('layouts.app')

@section('title', 'Kelola Toko — TanamKu')

@section('content')
    <div class="flex flex-col md:flex-row min-h-screen">
        @include('partials.sidebar')

        <div class="flex-1 min-w-0 p-4 sm:p-6 lg:p-8">
            @include('partials.alert')

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Kelola Toko Mitra</h1>
                    <p class="mt-1 text-sm text-slate-500">{{ $stores->total() }} toko terdaftar. Toko berstatus <strong>aktif</strong> yang produknya tampil di katalog publik.</p>
                </div>
            </div>

            {{-- Filter Status Tabs --}}
            <div class="mt-6 flex flex-wrap gap-2">
                <a href="{{ route('admin.stores.index') }}"
                   class="px-4 py-2 rounded-xl text-xs font-bold border transition-all {{ ! request('status') ? 'bg-primary text-white border-primary shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:border-slate-300' }}">
                    Semua ({{ $hitung->sum() }})
                </a>
                @foreach (['menunggu' => 'Menunggu Persetujuan', 'aktif' => 'Aktif', 'nonaktif' => 'Nonaktif'] as $status => $label)
                    <a href="{{ route('admin.stores.index', ['status' => $status]) }}"
                       class="px-4 py-2 rounded-xl text-xs font-bold border transition-all {{ request('status') === $status ? 'bg-primary text-white border-primary shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:border-slate-300' }}">
                        {{ $label }} ({{ $hitung[$status] ?? 0 }})
                    </a>
                @endforeach
            </div>

            {{-- Pencarian --}}
            <form method="GET" action="{{ route('admin.stores.index') }}" class="mt-4 flex flex-col sm:flex-row gap-2.5">
                @if (request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <div class="relative flex-grow">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama toko mitra..."
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20 bg-white">
                </div>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary text-white text-xs font-bold shadow-xs hover:bg-primary-hover hover:shadow transition-all">Cari</button>
                @if (request('q'))
                    <a href="{{ route('admin.stores.index', request()->only('status')) }}" class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors text-center">Reset</a>
                @endif
            </form>

            {{-- Tabel Toko Mitra --}}
            <div class="mt-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm min-w-[600px]">
                        <thead class="bg-slate-50/80 text-left border-b border-slate-100">
                            <tr class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                                <th class="px-5 py-3.5">Nama Toko Mitra</th>
                                <th class="px-5 py-3.5">Lokasi Kota</th>
                                <th class="px-5 py-3.5 text-center">Total Produk</th>
                                <th class="px-5 py-3.5 text-center">Status</th>
                                <th class="px-5 py-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($stores as $store)
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-primary/10 overflow-hidden flex items-center justify-center text-primary font-bold shrink-0">
                                                @if ($store->logo)
                                                    <img src="{{ asset('storage/' . $store->logo) }}" alt="" class="w-full h-full object-cover">
                                                @else
                                                    {{ mb_substr($store->nama_toko, 0, 1) }}
                                                @endif
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-900 text-xs">{{ $store->nama_toko }}</p>
                                                <p class="text-[11px] text-slate-400">Owner: {{ $store->user->name ?? '—' }} · WA {{ $store->no_wa }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5 text-slate-700 text-xs font-medium">{{ $store->kota }}</td>
                                    <td class="px-5 py-3.5 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                                            {{ $store->plants_count }} produk
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 text-center">
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold border
                                            {{ $store->status === 'aktif' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($store->status === 'nonaktif' ? 'bg-red-50 text-red-700 border-red-200' : 'bg-amber-50 text-amber-800 border-amber-200') }}">
                                            {{ $store->status_label }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 text-right">
                                        <a href="{{ route('stores.show', $store) }}" target="_blank" class="px-3 py-1.5 rounded-lg text-xs font-bold text-primary bg-primary/10 hover:bg-primary/20 transition-colors">
                                            Lihat Toko →
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-10 text-center text-sm text-slate-400">Belum ada toko terdaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Kartu Moderasi & Aksi Cepat --}}
            <div class="mt-8">
                <div class="flex items-center gap-2 mb-4">
                    <span class="w-2.5 h-2.5 rounded-full bg-primary"></span>
                    <h2 class="text-sm font-bold text-slate-900">Aksi Cepat Moderasi Toko</h2>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    @forelse ($stores as $store)
                        @php
                            $warna = match ($store->status) {
                                'aktif' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                'nonaktif' => 'bg-red-50 text-red-700 border-red-200',
                                default => 'bg-amber-50 text-amber-800 border-amber-200',
                            };
                        @endphp

                        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 hover:border-slate-300 transition-all flex flex-col justify-between">
                            <div>
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-12 h-12 rounded-xl bg-primary-light overflow-hidden flex items-center justify-center text-primary font-bold shrink-0">
                                            @if ($store->logo)
                                                <img src="{{ asset('storage/' . $store->logo) }}" alt="" class="w-full h-full object-cover">
                                            @else
                                                <span class="text-base">{{ mb_substr($store->nama_toko, 0, 1) }}</span>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-bold text-slate-900 truncate">{{ $store->nama_toko }}</p>
                                            <p class="text-[11px] text-slate-500 font-medium">📍 {{ $store->kota }} · {{ $store->plants_count }} produk</p>
                                        </div>
                                    </div>

                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold border shrink-0 {{ $warna }}">
                                        {{ $store->status_label }}
                                    </span>
                                </div>

                                <p class="mt-3 text-xs text-slate-600 line-clamp-2 leading-relaxed">{{ $store->deskripsi }}</p>
                                <p class="mt-2 text-[11px] text-slate-400">
                                    Pemilik: <span class="text-slate-700 font-medium">{{ $store->user->name ?? '—' }}</span> · WA: <span class="font-mono text-slate-700 font-medium">{{ $store->no_wa }}</span>
                                </p>
                            </div>

                            <div class="mt-4 pt-4 border-t border-slate-100 flex flex-wrap items-center gap-2">
                                @if ($store->status !== 'aktif')
                                    <form method="POST" action="{{ route('admin.stores.status', $store) }}">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="aktif">
                                        <button class="px-3.5 py-1.5 rounded-lg text-xs font-bold text-white bg-primary hover:bg-primary-hover shadow-2xs transition-all">
                                            ✓ Setujui Toko
                                        </button>
                                    </form>
                                @endif

                                @if ($store->status !== 'nonaktif')
                                    <form method="POST" action="{{ route('admin.stores.status', $store) }}">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="nonaktif">
                                        <button class="px-3.5 py-1.5 rounded-lg text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 transition-colors">
                                            Nonaktifkan
                                        </button>
                                    </form>
                                @endif

                                @if ($store->status !== 'menunggu')
                                    <form method="POST" action="{{ route('admin.stores.status', $store) }}">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="menunggu">
                                        <button class="px-3.5 py-1.5 rounded-lg text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors">
                                            Kembalikan ke Menunggu
                                        </button>
                                    </form>
                                @endif

                                <a href="{{ route('stores.show', $store) }}" target="_blank"
                                   class="px-3.5 py-1.5 rounded-lg text-xs font-bold text-primary bg-primary/10 hover:bg-primary/20 border border-primary/20 ml-auto transition-colors">
                                    Lihat Halaman Toko →
                                </a>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400 py-10 text-center lg:col-span-2">Belum ada toko dengan status ini.</p>
                    @endforelse
                </div>
            </div>

            @if ($stores->hasPages())
                <div class="mt-6">{{ $stores->links() }}</div>
            @endif
        </div>
    </div>
@endsection
