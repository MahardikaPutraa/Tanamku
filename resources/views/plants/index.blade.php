@extends('layouts.app')

@section('title', 'Katalog Tanaman — TanamKu')

@section('content')
    <div class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <nav class="text-xs text-gray-500 mb-3">
                <a href="{{ route('home') }}" class="hover:text-primary">Beranda</a>
                <span class="mx-1.5">/</span>
                <span class="text-gray-800 font-medium">Katalog</span>
            </nav>

            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Katalog Tanaman</h1>
            <p class="mt-2 text-sm text-gray-600">{{ $plants->total() }} produk dari berbagai toko.</p>

            <form method="GET" action="{{ route('plants.index') }}" class="mt-6 space-y-3">
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-grow">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/>
                            </svg>
                        </span>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama tanaman, misal: monstera, aglaonema..."
                               class="w-full pl-9 pr-3 py-2.5 rounded-lg border border-gray-300 text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                    </div>

                    <select name="kategori" class="px-3 py-2.5 rounded-lg border border-gray-300 text-sm bg-white focus:border-primary focus:ring-1 focus:ring-primary outline-none sm:w-48">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $kategori)
                            <option value="{{ $kategori->slug }}" @selected(request('kategori') === $kategori->slug)>{{ $kategori->nama }}</option>
                        @endforeach
                    </select>

                    <select name="urut" class="px-3 py-2.5 rounded-lg border border-gray-300 text-sm bg-white focus:border-primary focus:ring-1 focus:ring-primary outline-none sm:w-44">
                        <option value="">Urutkan: Terbaru</option>
                        <option value="murah" @selected(request('urut') === 'murah')>Harga Terendah</option>
                        <option value="mahal" @selected(request('urut') === 'mahal')>Harga Tertinggi</option>
                    </select>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-3 pt-1">
                    <div class="w-full sm:w-48">
                        <select name="kota" class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm bg-white focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                            <option value="">Semua Kota Toko</option>
                            @foreach ($cities as $city)
                                <option value="{{ $city }}" @selected(request('kota') === $city)>📍 {{ $city }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="w-full sm:flex-1 flex items-center gap-2">
                        <input type="number" name="harga_min" value="{{ request('harga_min') }}" placeholder="Harga Min (Rp)" min="0" step="5000"
                               class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                        <span class="text-gray-400 text-xs">—</span>
                        <input type="number" name="harga_max" value="{{ request('harga_max') }}" placeholder="Harga Max (Rp)" min="0" step="5000"
                               class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                    </div>

                    <div class="w-full sm:w-auto flex gap-2 shrink-0">
                        <button type="submit" class="flex-1 sm:flex-none px-6 py-2.5 rounded-lg bg-primary text-white text-sm font-semibold hover:bg-primary-hover transition-colors shadow-sm">
                            Terapkan Filter
                        </button>

                        @if (request()->hasAny(['q', 'kategori', 'urut', 'toko', 'kota', 'harga_min', 'harga_max']))
                            <a href="{{ route('plants.index') }}" class="px-4 py-2.5 rounded-lg text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors text-center">
                                Reset
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Tag Filter Aktif --}}
                @if (request()->hasAny(['q', 'kategori', 'kota', 'harga_min', 'harga_max']))
                    <div class="pt-2 flex flex-wrap items-center gap-1.5 text-xs">
                        <span class="text-gray-400 text-[11px] font-medium mr-1">Filter aktif:</span>

                        @if (request('q'))
                            <a href="{{ route('plants.index', request()->except('q')) }}"
                               class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 hover:bg-emerald-100 font-medium">
                                Cari: "{{ request('q') }}" <span class="font-bold">×</span>
                            </a>
                        @endif

                        @if (request('kategori'))
                            @php $katAktif = $categories->firstWhere('slug', request('kategori')); @endphp
                            <a href="{{ route('plants.index', request()->except('kategori')) }}"
                               class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 hover:bg-emerald-100 font-medium">
                                Kategori: {{ $katAktif?->nama ?? request('kategori') }} <span class="font-bold">×</span>
                            </a>
                        @endif

                        @if (request('kota'))
                            <a href="{{ route('plants.index', request()->except('kota')) }}"
                               class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 hover:bg-emerald-100 font-medium">
                                Kota: {{ request('kota') }} <span class="font-bold">×</span>
                            </a>
                        @endif

                        @if (request('harga_min') || request('harga_max'))
                            <a href="{{ route('plants.index', request()->except(['harga_min', 'harga_max'])) }}"
                               class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 hover:bg-emerald-100 font-medium">
                                Harga: Rp {{ number_format((float) request('harga_min', 0), 0, ',', '.') }} – {{ request('harga_max') ? 'Rp ' . number_format((float) request('harga_max'), 0, ',', '.') : 'Maks' }} <span class="font-bold">×</span>
                            </a>
                        @endif

                        <a href="{{ route('plants.index') }}" class="text-[11px] text-gray-500 hover:text-danger underline ml-1">
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
            <div class="text-center py-20 bg-white rounded-xl border border-dashed border-gray-200">
                <h3 class="text-base font-bold text-gray-900">Tidak ada produk yang cocok</h3>
                <p class="mt-1.5 text-sm text-gray-600">Coba kata kunci atau kategori lain.</p>
                <a href="{{ route('plants.index') }}" class="mt-5 inline-flex px-4 py-2 rounded-lg bg-primary text-white text-sm font-semibold hover:bg-primary-hover">
                    Tampilkan semua
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach ($plants as $plant)
                    @include('partials.plant-card', ['plant' => $plant])
                @endforeach
            </div>

            <div class="mt-10">{{ $plants->links() }}</div>
        @endif
    </div>
@endsection
