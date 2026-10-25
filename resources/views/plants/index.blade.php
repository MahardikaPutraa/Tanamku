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

            <form method="GET" action="{{ route('plants.index') }}" class="mt-6 flex flex-col sm:flex-row gap-3">
                <div class="relative flex-grow">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/>
                        </svg>
                    </span>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari tanaman, misal: monstera"
                           class="w-full pl-9 pr-3 py-2.5 rounded-lg border border-gray-300 text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                </div>

                <select name="kategori" class="px-3 py-2.5 rounded-lg border border-gray-300 text-sm bg-white focus:border-primary focus:ring-1 focus:ring-primary outline-none sm:w-52">
                    <option value="">Semua kategori</option>
                    @foreach ($categories as $kategori)
                        <option value="{{ $kategori->slug }}" @selected(request('kategori') === $kategori->slug)>{{ $kategori->nama }}</option>
                    @endforeach
                </select>

                <select name="urut" class="px-3 py-2.5 rounded-lg border border-gray-300 text-sm bg-white focus:border-primary focus:ring-1 focus:ring-primary outline-none sm:w-44">
                    <option value="">Terbaru</option>
                    <option value="murah" @selected(request('urut') === 'murah')>Harga terendah</option>
                    <option value="mahal" @selected(request('urut') === 'mahal')>Harga tertinggi</option>
                </select>

                <button type="submit" class="px-5 py-2.5 rounded-lg bg-primary text-white text-sm font-semibold hover:bg-primary-hover transition-colors">
                    Cari
                </button>

                @if (request()->hasAny(['q', 'kategori', 'urut', 'toko']))
                    <a href="{{ route('plants.index') }}" class="px-5 py-2.5 rounded-lg text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors text-center">
                        Reset
                    </a>
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
