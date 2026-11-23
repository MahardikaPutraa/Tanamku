@extends('layouts.app')

@section('title', 'Katalog Tanaman — TanamKu')

@section('content')
    <div class="bg-gradient-to-b from-white to-slate-50/50 border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
            <nav class="flex items-center gap-2 text-xs text-slate-500 mb-3">
                <a href="{{ route('home') }}" class="hover:text-primary transition-colors flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Beranda
                </a>
                <span class="text-slate-400">/</span>
                <span class="text-slate-800 font-semibold">Katalog Tanaman</span>
            </nav>

            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-slate-900 tracking-tight">Katalog Tanaman Hias</h1>
                    <p class="mt-1.5 text-sm text-slate-600">Jelajahi {{ $plants->total() }} koleksi tanaman segar dari penjual lokal terpercaya di berbagai kota.</p>
                </div>
            </div>

            {{-- FORM FILTER & PENCARIAN MARKETPLACE --}}
            <form method="GET" action="{{ route('plants.index') }}" class="mt-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 sm:p-6 space-y-4">
                <div class="flex flex-col lg:flex-row gap-3">
                    {{-- Input Pencarian Utama --}}
                    <div class="relative flex-grow">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 pointer-events-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/>
                            </svg>
                        </span>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari tanaman, misal: Monstera, Calathea, Kaktus..."
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition bg-slate-50/50 focus:bg-white shadow-2xs">
                    </div>

                    {{-- Kategori Dropdown --}}
                    <select name="kategori" class="px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition cursor-pointer lg:w-52 shadow-2xs">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $kategori)
                            <option value="{{ $kategori->slug }}" @selected(request('kategori') === $kategori->slug)>{{ $kategori->nama }}</option>
                        @endforeach
                    </select>

                    {{-- Urutkan Dropdown --}}
                    <select name="urut" class="px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition cursor-pointer lg:w-48 shadow-2xs">
                        <option value="">Urutkan: Terbaru</option>
                        <option value="murah" @selected(request('urut') === 'murah')>Harga Terendah</option>
                        <option value="mahal" @selected(request('urut') === 'mahal')>Harga Tertinggi</option>
                    </select>
                </div>

                {{-- Baris Filter Lanjutan (Kota, Rentang Harga, Tombol) --}}
                <div class="flex flex-col lg:flex-row items-center gap-3 pt-1 border-t border-slate-100">
                    <div class="w-full lg:w-56">
                        <select name="kota" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition cursor-pointer shadow-2xs">
                            <option value="">Semua Kota Penjual</option>
                            @foreach ($cities as $city)
                                <option value="{{ $city }}" @selected(request('kota') === $city)>📍 {{ $city }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="w-full lg:flex-1 flex items-center gap-2">
                        <div class="relative flex-1">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs text-slate-400 font-semibold pointer-events-none">Rp</span>
                            <input type="number" name="harga_min" value="{{ request('harga_min') }}" placeholder="Harga Min" min="0" step="5000"
                                   class="w-full pl-8 pr-3 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition shadow-2xs">
                        </div>
                        <span class="text-slate-400 text-xs font-bold">—</span>
                        <div class="relative flex-1">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs text-slate-400 font-semibold pointer-events-none">Rp</span>
                            <input type="number" name="harga_max" value="{{ request('harga_max') }}" placeholder="Harga Max" min="0" step="5000"
                                   class="w-full pl-8 pr-3 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition shadow-2xs">
                        </div>
                    </div>

                    <div class="w-full lg:w-auto flex gap-2 shrink-0">
                        <button type="submit" class="flex-1 lg:flex-none px-6 py-2.5 rounded-xl bg-primary text-white text-sm font-bold hover:bg-primary-hover active-press transition-all shadow-sm hover:shadow-md">
                            Terapkan Filter
                        </button>

                        @if (request()->hasAny(['q', 'kategori', 'urut', 'toko', 'kota', 'harga_min', 'harga_max']))
                            <a href="{{ route('plants.index') }}" class="px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 active-press transition-colors text-center">
                                Reset
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Tag Filter Aktif --}}
                @if (request()->hasAny(['q', 'kategori', 'kota', 'harga_min', 'harga_max']))
                    <div class="pt-2 flex flex-wrap items-center gap-1.5 text-xs border-t border-slate-100">
                        <span class="text-slate-400 text-[11px] font-semibold mr-1">Filter Aktif:</span>

                        @if (request('q'))
                            <a href="{{ route('plants.index', request()->except('q')) }}"
                               class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 hover:bg-emerald-100 font-medium transition">
                                Cari: "{{ request('q') }}" <span class="font-bold">×</span>
                            </a>
                        @endif

                        @if (request('kategori'))
                            @php $katAktif = $categories->firstWhere('slug', request('kategori')); @endphp
                            <a href="{{ route('plants.index', request()->except('kategori')) }}"
                               class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 hover:bg-emerald-100 font-medium transition">
                                Kategori: {{ $katAktif?->nama ?? request('kategori') }} <span class="font-bold">×</span>
                            </a>
                        @endif

                        @if (request('kota'))
                            <a href="{{ route('plants.index', request()->except('kota')) }}"
                               class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 hover:bg-emerald-100 font-medium transition">
                                Kota: {{ request('kota') }} <span class="font-bold">×</span>
                            </a>
                        @endif

                        @if (request('harga_min') || request('harga_max'))
                            <a href="{{ route('plants.index', request()->except(['harga_min', 'harga_max'])) }}"
                               class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 hover:bg-emerald-100 font-medium transition">
                                Harga: Rp {{ number_format((float) request('harga_min', 0), 0, ',', '.') }} – {{ request('harga_max') ? 'Rp ' . number_format((float) request('harga_max'), 0, ',', '.') : 'Maks' }} <span class="font-bold">×</span>
                            </a>
                        @endif

                        <a href="{{ route('plants.index') }}" class="text-[11px] text-slate-500 hover:text-danger underline ml-2 transition">
                            Hapus semua filter
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        @include('partials.alert')

        @if ($plants->isEmpty())
            <div class="text-center py-20 bg-white rounded-3xl border border-dashed border-slate-200 shadow-xs max-w-lg mx-auto">
                <div class="w-16 h-16 mx-auto rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900">Tidak ada produk yang cocok</h3>
                <p class="mt-1.5 text-sm text-slate-500 max-w-xs mx-auto">Coba gunakan kata kunci pencarian yang lebih umum atau sesuaikan filter harga dan kategori.</p>
                <a href="{{ route('plants.index') }}" class="mt-6 inline-flex px-6 py-2.5 rounded-xl bg-primary text-white text-sm font-bold hover:bg-primary-hover shadow-sm active-press transition-all">
                    Tampilkan Semua Produk
                </a>
            </div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-6">
                @foreach ($plants as $plant)
                    @include('partials.plant-card', ['plant' => $plant])
                @endforeach
            </div>

            <div class="mt-10">{{ $plants->links() }}</div>
        @endif
    </div>
@endsection
