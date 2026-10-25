@extends('layouts.app')

@section('title', 'TanamKu — Marketplace Tanaman Hias')

@section('content')
    {{-- HERO --}}
    <section class="relative overflow-hidden bg-gradient-to-br from-primary via-primary to-emerald-800">
        <div class="absolute inset-0 opacity-10"
             style="background-image: radial-gradient(circle at 20% 20%, #fff 0, transparent 45%), radial-gradient(circle at 80% 70%, #fff 0, transparent 40%);"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-24">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-white/15 text-white ring-1 ring-white/25">
                        🌿 Marketplace tanaman dari penjual lokal
                    </span>

                    <h1 class="mt-5 text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-white leading-tight">
                        Tanaman Sehat,<br>
                        <span class="text-accent">Rumah Lebih Hidup</span>
                    </h1>

                    <p class="mt-5 text-base sm:text-lg text-emerald-50/90 max-w-xl leading-relaxed">
                        Temukan tanaman dari berbagai toko, baca panduan perawatannya, lalu pesan langsung
                        ke WhatsApp penjual. Tanpa akun pun bisa melihat-lihat.
                    </p>

                    <div class="mt-8 flex flex-wrap items-center gap-3">
                        <a href="{{ route('plants.index') }}"
                           class="inline-flex items-center gap-2 px-6 py-3 rounded-lg bg-white text-primary text-sm font-bold shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all">
                            Jelajahi Katalog
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </a>
                        <a href="{{ route('register', ['role' => 'seller']) }}"
                           class="inline-flex items-center gap-2 px-6 py-3 rounded-lg text-sm font-semibold text-white ring-1 ring-white/40 hover:bg-white/10 transition-colors">
                            Buka Toko
                        </a>
                    </div>

                    @guest
                        <p class="mt-4 text-xs text-emerald-100/80">
                            Sudah punya akun?
                            <a href="{{ route('login') }}" class="font-semibold text-white underline decoration-white/40 hover:decoration-white">Masuk</a>
                            <span class="mx-1">·</span>
                            Belum punya?
                            <a href="{{ route('register') }}" class="font-semibold text-white underline decoration-white/40 hover:decoration-white">Daftar gratis</a>
                        </p>
                    @endguest

                    <dl class="mt-10 grid grid-cols-3 gap-4 max-w-md">
                        <div>
                            <dt class="text-xs text-emerald-100/70">Produk</dt>
                            <dd class="text-2xl font-extrabold text-white">{{ $totalProduk }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-emerald-100/70">Toko</dt>
                            <dd class="text-2xl font-extrabold text-white">{{ $totalToko }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-emerald-100/70">Kategori</dt>
                            <dd class="text-2xl font-extrabold text-white">{{ $categories->count() }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="hidden lg:flex justify-center">
                    <div class="relative w-80 h-80">
                        <div class="absolute inset-0 rounded-full bg-white/10 ring-1 ring-white/20"></div>
                        <div class="absolute inset-8 rounded-full bg-white/10 ring-1 ring-white/20"></div>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <svg class="w-40 h-40 text-accent/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 21v-8m0 0C12 8 8 4 3 4c0 5 4 9 9 9zm0 0c0-5 4-9 9-9 0 5-4 9-9 9z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- KATEGORI --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
        <div class="flex items-end justify-between mb-6">
            <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight">Kategori</h2>
            <a href="{{ route('plants.index') }}" class="text-sm font-semibold text-primary hover:underline">Semua produk →</a>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach ($categories as $kategori)
                <a href="{{ route('plants.index', ['kategori' => $kategori->slug]) }}"
                   class="group bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md hover:border-primary/30 transition-all p-5">
                    <div class="w-10 h-10 rounded-lg bg-primary-light flex items-center justify-center text-primary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                        </svg>
                    </div>
                    <h3 class="mt-4 text-sm font-bold text-gray-900 group-hover:text-primary">{{ $kategori->nama }}</h3>
                    <p class="mt-1 text-xs text-gray-500">{{ $kategori->plants_count }} produk</p>
                </a>
            @endforeach
        </div>
    </section>

    {{-- PRODUK UNGGULAN --}}
    <section class="bg-white border-y border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
            <div class="flex items-end justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight">Pilihan Minggu Ini</h2>
                    <p class="mt-1 text-sm text-gray-600">Produk unggulan dari penjual terverifikasi.</p>
                </div>
                <a href="{{ route('plants.index') }}" class="hidden sm:inline text-sm font-semibold text-primary hover:underline">Lihat semua →</a>
            </div>

            @if ($featuredPlants->isEmpty())
                <p class="text-center py-14 text-sm text-gray-500">Belum ada produk unggulan.</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($featuredPlants as $plant)
                        @include('partials.plant-card', ['plant' => $plant])
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- TOKO --}}
    @if ($tokoAktif->isNotEmpty())
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
            <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight mb-6">Toko di TanamKu</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                @foreach ($tokoAktif as $toko)
                    <a href="{{ route('stores.show', $toko) }}"
                       class="group bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-all p-5">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-lg bg-primary-light overflow-hidden flex items-center justify-center text-primary font-bold shrink-0">
                                @if ($toko->logo)
                                    <img src="{{ asset('storage/' . $toko->logo) }}" alt="{{ $toko->nama_toko }}" class="w-full h-full object-cover">
                                @else
                                    {{ mb_substr($toko->nama_toko, 0, 1) }}
                                @endif
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-sm font-bold text-gray-900 group-hover:text-primary truncate">{{ $toko->nama_toko }}</h3>
                                <p class="text-xs text-gray-500">{{ $toko->kota }} · {{ $toko->plants_count }} produk</p>
                            </div>
                        </div>
                        <p class="mt-3 text-xs text-gray-600 line-clamp-2">{{ $toko->deskripsi }}</p>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- CARA KERJA --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
        <div class="rounded-2xl bg-primary-light border border-primary/15 px-8 py-12">
            <h2 class="text-2xl font-extrabold text-primary tracking-tight text-center">Cara pesan di TanamKu</h2>

            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ([
                    ['1', 'Pilih tanaman', 'Masukkan produk dari satu atau beberapa toko ke keranjang.'],
                    ['2', 'Checkout', 'Pesanan dicatat, lalu pesan WhatsApp dibuat otomatis untuk penjual.'],
                    ['3', 'Lanjut via WhatsApp', 'Bahas stok, pembayaran, dan pengiriman langsung dengan penjual.'],
                ] as $langkah)
                    <div class="bg-white rounded-xl border border-primary/10 p-5">
                        <div class="w-8 h-8 rounded-lg bg-primary text-white text-sm font-bold flex items-center justify-center">
                            {{ $langkah[0] }}
                        </div>
                        <h3 class="mt-3 text-sm font-bold text-gray-900">{{ $langkah[1] }}</h3>
                        <p class="mt-1.5 text-xs text-gray-600 leading-relaxed">{{ $langkah[2] }}</p>
                    </div>
                @endforeach
            </div>

            <p class="mt-8 text-center text-xs text-gray-600">
                TanamKu hanya mempertemukan pembeli dan penjual. Pembayaran dan pengiriman diatur langsung antara keduanya.
            </p>
        </div>
    </section>
@endsection
