@extends('layouts.app')

@section('title', $store->nama_toko . ' — TanamKu')

@section('content')
    <div class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <nav class="text-xs text-gray-500 mb-5">
                <a href="{{ route('home') }}" class="hover:text-primary">Beranda</a>
                <span class="mx-1.5">/</span>
                <a href="{{ route('plants.index') }}" class="hover:text-primary">Katalog</a>
                <span class="mx-1.5">/</span>
                <span class="text-gray-800 font-medium">{{ $store->nama_toko }}</span>
            </nav>

            <div class="flex flex-col sm:flex-row sm:items-start gap-5">
                <div class="w-20 h-20 rounded-xl bg-primary-light overflow-hidden flex items-center justify-center text-primary text-2xl font-extrabold shrink-0">
                    @if ($store->logo)
                        <img src="{{ asset('storage/' . $store->logo) }}" alt="{{ $store->nama_toko }}" class="w-full h-full object-cover">
                    @else
                        {{ mb_substr($store->nama_toko, 0, 1) }}
                    @endif
                </div>

                <div class="flex-1 min-w-0">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">{{ $store->nama_toko }}</h1>

                    <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-gray-500">
                        <span class="inline-flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{ $store->kota }}
                        </span>
                        <span class="inline-flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            {{ $store->metode_serah_label }}
                        </span>
                        <span>{{ $store->plants()->where('is_active', true)->count() }} produk</span>
                    </div>

                    <p class="mt-3 text-sm text-gray-700 leading-relaxed max-w-3xl">{{ $store->deskripsi }}</p>
                    <p class="mt-2 text-xs text-gray-500">Alamat toko: {{ $store->alamat }}</p>
                </div>

                <a href="https://wa.me/{{ $store->wa_number }}" target="_blank" rel="noopener"
                   class="shrink-0 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 transition-colors">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38a9.9 9.9 0 004.79 1.22h.01c5.46 0 9.91-4.45 9.91-9.91C21.96 6.45 17.5 2 12.04 2zm5.79 14.09c-.24.68-1.4 1.3-1.93 1.35-.53.05-1.03.24-3.46-.72-2.93-1.16-4.78-4.17-4.92-4.36-.14-.19-1.17-1.56-1.17-2.97 0-1.41.74-2.11 1-2.4.26-.29.57-.36.76-.36.19 0 .38 0 .55.01.18.01.41-.07.64.49.24.58.81 1.99.88 2.13.07.15.12.32.02.51-.1.19-.15.31-.29.48-.15.17-.31.38-.44.51-.15.14-.3.3-.13.59.17.29.76 1.25 1.63 2.02 1.12 1 2.06 1.31 2.35 1.46.29.15.46.12.63-.07.17-.19.73-.85.93-1.14.19-.29.39-.24.65-.14.27.1 1.68.79 1.97.94.29.15.48.22.55.34.07.12.07.68-.17 1.36z"/>
                    </svg>
                    Chat Penjual
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        @include('partials.alert')

        @if ($plants->isEmpty())
            <p class="text-center py-16 text-sm text-gray-500">Toko ini belum menampilkan produk.</p>
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
