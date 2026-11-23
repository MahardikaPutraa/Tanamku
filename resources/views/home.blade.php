@extends('layouts.app')

@section('title', 'TanamKu — Marketplace Tanaman Hias Terlengkap & Terpercaya')

@section('content')
    {{-- HERO SECTION: MODERN MARKETPLACE PROMO BANNER GRID --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-5 pb-6">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-stretch">
            
            {{-- BANNER UTAMA (Left 8 Cols) --}}
            <div class="hero-main-banner lg:col-span-8 relative overflow-hidden rounded-3xl bg-gradient-to-br from-primary via-emerald-800 to-teal-950 text-white p-5 sm:p-8 lg:p-12 shadow-xl flex flex-col justify-between">
                {{-- Decorative background glow & shapes (GSAP ambient float) --}}
                <div class="ambient-orb absolute -right-20 -bottom-20 w-80 h-80 bg-emerald-400/20 rounded-full blur-3xl pointer-events-none"></div>
                <div class="ambient-orb absolute right-12 top-6 w-32 h-32 bg-accent/15 rounded-full blur-2xl pointer-events-none"></div>

                {{-- Floating Balanced Badge di Banner Kiri --}}
                <div class="hero-float-left absolute top-5 right-5 sm:top-8 sm:right-8 bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl px-3.5 py-2 shadow-lg hidden sm:flex items-center gap-2.5 pointer-events-none select-none z-10">
                    <span class="text-xl">🌿</span>
                    <div>
                        <p class="text-[10px] font-bold text-emerald-200 uppercase tracking-wider">Garansi Segar</p>
                        <p class="text-xs font-black text-white">100% Kebun Terverifikasi</p>
                    </div>
                </div>

                <div>
                    <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full text-[11px] sm:text-xs font-bold bg-white/15 text-emerald-100 backdrop-blur-md ring-1 ring-white/25 shadow-xs">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                        🌿 Marketplace Tanaman Hias #1 Indonesia
                    </div>

                    <h1 class="mt-4 sm:mt-5 text-2xl sm:text-4xl lg:text-5xl font-black tracking-tight text-white leading-tight">
                        Tanaman Hias Segar,<br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-200 via-teal-100 to-accent">Langsung dari Kebun Lokal</span>
                    </h1>

                    <p class="mt-3 sm:mt-4 text-xs sm:text-base text-emerald-100/90 max-w-xl leading-relaxed font-medium">
                        Temukan koleksi monstera, aglaonema, sukulen & kaktus pilihan terbaik. Pesan langsung via WhatsApp penjual tanpa perantara dengan packing bergaransi aman.
                    </p>
                </div>

                <div class="mt-6 sm:mt-8 flex flex-col sm:flex-row items-stretch sm:items-center gap-3 sm:gap-3.5">
                    <a href="{{ route('plants.index') }}"
                       class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-white text-primary text-xs sm:text-sm font-black shadow-lg shadow-black/10 hover:bg-emerald-50 hover:shadow-xl hover:-translate-y-0.5 active:scale-95 transition-all text-center">
                        <span>Jelajahi Katalog Lengkap</span>
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>

                    @guest
                        <a href="{{ route('register', ['role' => 'seller']) }}"
                           class="inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-xl text-xs sm:text-sm font-bold text-white bg-white/10 hover:bg-white/20 backdrop-blur-md ring-1 ring-white/30 transition-all text-center">
                            <span>Buka Toko Gratis</span>
                        </a>
                    @else
                        @if (auth()->user()->isSeller())
                            <a href="{{ route('seller.dashboard') }}"
                               class="inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-xl text-xs sm:text-sm font-bold text-white bg-white/10 hover:bg-white/20 backdrop-blur-md ring-1 ring-white/30 transition-all text-center">
                                <span>Panel Toko Saya</span>
                            </a>
                        @elseif (auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}"
                               class="inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-xl text-xs sm:text-sm font-bold text-white bg-white/10 hover:bg-white/20 backdrop-blur-md ring-1 ring-white/30 transition-all text-center">
                                <span>Panel Administrator</span>
                            </a>
                        @else
                            <a href="{{ route('orders.mine') }}"
                               class="inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-xl text-xs sm:text-sm font-bold text-white bg-white/10 hover:bg-white/20 backdrop-blur-md ring-1 ring-white/30 transition-all text-center">
                                <span>Riwayat Pesanan</span>
                            </a>
                        @endif
                    @endguest
                </div>

                {{-- Live Highlights Stat Strip --}}
                <div class="mt-6 sm:mt-8 pt-5 sm:pt-6 border-t border-white/15 grid grid-cols-3 gap-2 sm:gap-3 max-w-lg">
                    <div>
                        <p class="text-lg sm:text-2xl font-black text-white"><span class="stat-counter" data-target="{{ $totalProduk }}" data-suffix="+">{{ $totalProduk }}+</span></p>
                        <p class="text-[10px] sm:text-[11px] font-semibold text-emerald-200 mt-0.5">Koleksi Tanaman</p>
                    </div>
                    <div>
                        <p class="text-lg sm:text-2xl font-black text-white"><span class="stat-counter" data-target="{{ $totalToko }}" data-suffix="+">{{ $totalToko }}+</span></p>
                        <p class="text-[10px] sm:text-[11px] font-semibold text-emerald-200 mt-0.5">Mitra Kebun & Toko</p>
                    </div>
                    <div>
                        <p class="text-lg sm:text-2xl font-black text-white"><span class="stat-counter" data-target="100" data-suffix="%">100%</span></p>
                        <p class="text-[10px] sm:text-[11px] font-semibold text-emerald-200 mt-0.5">Garansi Segar</p>
                    </div>
                </div>
            </div>

            {{-- 2 PROMO SIDE-CARDS (Right 4 Cols - Tokopedia/Shopee style) --}}
            <div class="lg:col-span-4 flex flex-col gap-5">
                {{-- Promo Card 1: WhatsApp Instant Checkout (GSAP Floating) --}}
                <div class="hero-float-1 flex-1 bg-gradient-to-br from-emerald-500/10 via-white to-slate-50 rounded-3xl border border-emerald-200/80 p-6 shadow-xs hover:shadow-md transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="px-3 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200 uppercase tracking-wider">
                                💬 WhatsApp Chat
                            </span>
                            <span class="text-2xl">📱</span>
                        </div>
                        <h3 class="mt-3 text-base font-extrabold text-slate-900 tracking-tight">Pesan Langsung Tanpa Ribet</h3>
                        <p class="mt-1 text-xs text-slate-600 leading-relaxed font-medium">
                            Pilih tanaman, klik WhatsApp, dan rincian pesanan otomatis terisi rapi siap dikirim ke penjual.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-primary">
                        <a href="{{ route('plants.index') }}" class="hover:underline inline-flex items-center gap-1 font-bold">
                            <span>Mulai Belanja Sekarang</span>
                            <span>→</span>
                        </a>
                    </div>
                </div>

                {{-- Promo Card 2: Jaminan Mutu Kebun Asli (GSAP Floating) --}}
                <div class="hero-float-2 flex-1 bg-gradient-to-br from-amber-500/10 via-white to-slate-50 rounded-3xl border border-amber-200/80 p-6 shadow-xs hover:shadow-md transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="px-3 py-1 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-900 border border-amber-200 uppercase tracking-wider">
                                ⭐ Terverifikasi
                            </span>
                            <span class="text-2xl">🪴</span>
                        </div>
                        <h3 class="mt-3 text-base font-extrabold text-slate-900 tracking-tight">Tanaman Sehat & Terkurasi</h3>
                        <p class="mt-1 text-xs text-slate-600 leading-relaxed font-medium">
                            Setiap mitra kebun diverifikasi oleh tim TanamKu untuk memastikan akar kuat, daun segar, dan bebas hama.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-amber-800">
                        <span class="inline-flex items-center gap-1 font-bold">
                            <span>Bebas Konsultasi Rawat</span>
                            <span>✓</span>
                        </span>
                    </div>
                </div>
            </div>

        </div>
    </section>

    {{-- RUNNING NEWS TICKER --}}
    <section class="bg-slate-900 text-white border-y border-slate-800 overflow-hidden relative select-none py-2.5">
        <div class="relative overflow-hidden mask-marquee">
            <div class="animate-marquee flex items-center gap-8 text-xs sm:text-sm text-slate-200 whitespace-nowrap cursor-default">
                {{-- Set 1 --}}
                <div class="inline-flex items-center gap-6">
                    <span class="inline-flex items-center gap-1.5">
                        <span class="text-emerald-400 font-bold">🌿 Tren Terkini:</span> Monstera Deliciosa & Philodendron Brasil paling diminati pecinta indoor plant minggu ini!
                    </span>
                    <span class="text-slate-600">✦</span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="text-amber-300 font-bold">💡 Tips Rawat:</span> Siram tanaman hias saat 2-3 cm lapisan tanah atas terasa mulai mengering.
                    </span>
                    <span class="text-slate-600">✦</span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="text-emerald-400 font-bold">🛡️ Penjual Terverifikasi:</span> 100% toko mitra telah lolos verifikasi mutu tanaman oleh admin TanamKu.
                    </span>
                    <span class="text-slate-600">✦</span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="text-sky-300 font-bold">💬 Pesan via WhatsApp:</span> Format pesanan otomatis terisi rapi siap dikirim langsung ke penjual.
                    </span>
                    <span class="text-slate-600">✦</span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="text-emerald-400 font-bold">📦 Packing Aman:</span> Mitra penjual melayani pengiriman bergaransi dengan packing pengaman khusus.
                    </span>
                </div>

                {{-- Set 2 (Duplikasi untuk looping mulus tanpa putus) --}}
                <div class="inline-flex items-center gap-6">
                    <span class="inline-flex items-center gap-1.5">
                        <span class="text-emerald-400 font-bold">🌿 Tren Terkini:</span> Monstera Deliciosa & Philodendron Brasil paling diminati pecinta indoor plant minggu ini!
                    </span>
                    <span class="text-slate-600">✦</span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="text-amber-300 font-bold">💡 Tips Rawat:</span> Siram tanaman hias saat 2-3 cm lapisan tanah atas terasa mulai mengering.
                    </span>
                    <span class="text-slate-600">✦</span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="text-emerald-400 font-bold">🛡️ Penjual Terverifikasi:</span> 100% toko mitra telah lolos verifikasi mutu tanaman oleh admin TanamKu.
                    </span>
                    <span class="text-slate-600">✦</span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="text-sky-300 font-bold">💬 Pesan via WhatsApp:</span> Format pesanan otomatis terisi rapi siap dikirim langsung ke penjual.
                    </span>
                    <span class="text-slate-600">✦</span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="text-emerald-400 font-bold">📦 Packing Aman:</span> Mitra penjual melayani pengiriman bergaransi dengan packing pengaman khusus.
                    </span>
                </div>
            </div>
        </div>
    </section>

    {{-- QUICK CATEGORIES SQUIRCLE STRIP (Tokopedia / Shopee Quick Nav Style) --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
        <div class="bg-white rounded-3xl border border-slate-200/80 p-5 sm:p-6 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">Kategori Pilihan Cepat</h2>
                </div>
                <a href="{{ route('plants.index') }}" class="text-xs font-bold text-primary hover:underline flex items-center gap-1 group">
                    <span>Lihat Semua Kategori</span>
                    <span class="group-hover:translate-x-1 transition-transform">→</span>
                </a>
            </div>

            @php
                $catIcons = [
                    'kaktus' => '🌵',
                    'sukulen' => '🌵',
                    'gantung' => '🪴',
                    'bunga' => '🌸',
                    'daun' => '🌿',
                    'air' => '💧',
                    'bonsai' => '🎋',
                ];
            @endphp

            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-4">
                @foreach ($categories as $cat)
                    @php
                        $icon = '🌱';
                        $slugLower = strtolower($cat->slug . ' ' . $cat->nama);
                        foreach ($catIcons as $key => $emoji) {
                            if (str_contains($slugLower, $key)) {
                                $icon = $emoji;
                                break;
                            }
                        }
                    @endphp
                    <a href="{{ url('/katalog?kategori=' . $cat->slug) }}"
                       class="flex items-center justify-between p-3 sm:p-4 rounded-2xl border border-slate-200/80 bg-slate-50/60 hover:bg-emerald-50/70 hover:border-emerald-300 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group">
                        <div class="flex items-center gap-2.5 sm:gap-3.5 min-w-0">
                            <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-xl sm:rounded-2xl bg-white text-emerald-800 flex items-center justify-center font-bold text-lg sm:text-xl shrink-0 shadow-2xs border border-slate-200/60 group-hover:scale-110 group-hover:bg-primary group-hover:border-primary transition-all duration-200">
                                {{ $icon }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs sm:text-sm font-extrabold text-slate-900 group-hover:text-primary transition-colors truncate">{{ $cat->nama }}</p>
                                <p class="text-[10px] sm:text-xs text-slate-500 font-semibold mt-0.5 truncate">{{ $cat->plants_count }} produk</p>
                            </div>
                        </div>
                        <span class="text-slate-400 group-hover:text-primary group-hover:translate-x-1 transition-all text-xs sm:text-sm font-bold pl-1 hidden sm:inline">→</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- TRUST & VALUE BAR (4 Pilar Utama Marketplace) --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-5 border-b border-slate-200/80">
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-4">
            {{-- Pilar 1 --}}
            <div class="card-lift flex items-center gap-2.5 sm:gap-3.5 p-3 sm:p-4 rounded-2xl border border-slate-200/80 bg-white hover:bg-emerald-50/70 hover:border-emerald-300 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group">
                <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-xl sm:rounded-2xl bg-emerald-50 text-primary flex items-center justify-center shrink-0 shadow-2xs border border-emerald-100 group-hover:scale-110 group-hover:bg-primary group-hover:border-primary group-hover:text-white transition-all duration-200">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="min-w-0">
                    <h4 class="text-xs sm:text-sm font-extrabold text-slate-900 group-hover:text-primary transition-colors truncate">Tanaman Terkurasi</h4>
                    <p class="text-[10px] sm:text-xs text-slate-500 font-medium mt-0.5 line-clamp-1 sm:line-clamp-none">Sehat & siap pajang dari penjual lokal</p>
                </div>
            </div>

            {{-- Pilar 2 --}}
            <div class="card-lift flex items-center gap-2.5 sm:gap-3.5 p-3 sm:p-4 rounded-2xl border border-slate-200/80 bg-white hover:bg-emerald-50/70 hover:border-emerald-300 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group">
                <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-xl sm:rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 shadow-2xs border border-emerald-100 group-hover:scale-110 group-hover:bg-emerald-600 group-hover:border-emerald-600 group-hover:text-white transition-all duration-200">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38a9.9 9.9 0 004.79 1.22h.01c5.46 0 9.91-4.45 9.91-9.91C21.96 6.45 17.5 2 12.04 2z"/></svg>
                </div>
                <div class="min-w-0">
                    <h4 class="text-xs sm:text-sm font-extrabold text-slate-900 group-hover:text-emerald-700 transition-colors truncate">Pesan via WhatsApp</h4>
                    <p class="text-[10px] sm:text-xs text-slate-500 font-medium mt-0.5 line-clamp-1 sm:line-clamp-none">Format pesan rapi langsung ke penjual</p>
                </div>
            </div>

            {{-- Pilar 3 --}}
            <div class="card-lift flex items-center gap-2.5 sm:gap-3.5 p-3 sm:p-4 rounded-2xl border border-slate-200/80 bg-white hover:bg-amber-50/70 hover:border-amber-300 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group">
                <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-xl sm:rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 shadow-2xs border border-amber-100 group-hover:scale-110 group-hover:bg-amber-600 group-hover:border-amber-600 group-hover:text-white transition-all duration-200">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div class="min-w-0">
                    <h4 class="text-xs sm:text-sm font-extrabold text-slate-900 group-hover:text-amber-700 transition-colors truncate">Penjual Berbagai Kota</h4>
                    <p class="text-[10px] sm:text-xs text-slate-500 font-medium mt-0.5 line-clamp-1 sm:line-clamp-none">Pilih toko terdekat agar tanaman segar</p>
                </div>
            </div>

            {{-- Pilar 4 --}}
            <div class="card-lift flex items-center gap-2.5 sm:gap-3.5 p-3 sm:p-4 rounded-2xl border border-slate-200/80 bg-white hover:bg-sky-50/70 hover:border-sky-300 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group">
                <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-xl sm:rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0 shadow-2xs border border-sky-100 group-hover:scale-110 group-hover:bg-sky-600 group-hover:border-sky-600 group-hover:text-white transition-all duration-200">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <div class="min-w-0">
                    <h4 class="text-xs sm:text-sm font-extrabold text-slate-900 group-hover:text-sky-700 transition-colors truncate">Panduan Rawat Lengkap</h4>
                    <p class="text-[10px] sm:text-xs text-slate-500 font-medium mt-0.5 line-clamp-1 sm:line-clamp-none">Tips media tanam, siram & cahaya</p>
                </div>
            </div>
        </div>
    </section>

    {{-- KATEGORI POPULER --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
            <div>
                <span class="inline-block text-xs font-extrabold uppercase tracking-wider text-primary bg-primary/10 px-3 py-1 rounded-full border border-primary/20 mb-2">
                    Kategori Pilihan
                </span>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Jelajahi Berdasarkan Kategori</h2>
                <p class="mt-1 text-sm text-slate-500 font-medium">Temukan ragam tanaman hias terbaik sesuai kebutuhan ruangan dan hunian Anda.</p>
            </div>
            <a href="{{ route('plants.index') }}" class="group inline-flex items-center gap-1.5 text-sm font-bold text-primary hover:text-primary-hover transition-colors">
                <span>Lihat semua produk</span>
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
            @foreach ($categories as $kategori)
                @php
                    $icon = '🌱';
                    $slugLower = strtolower($kategori->slug . ' ' . $kategori->nama);
                    foreach ($catIcons as $key => $emoji) {
                        if (str_contains($slugLower, $key)) {
                            $icon = $emoji;
                            break;
                        }
                    }
                @endphp
                <a href="{{ url('/katalog?kategori=' . $kategori->slug) }}"
                   class="group bg-white rounded-3xl border border-slate-200/80 shadow-xs hover:shadow-xl hover:border-emerald-300 hover:-translate-y-1.5 transition-all duration-300 p-5 sm:p-6 flex flex-col justify-between">
                    <div>
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-100 flex items-center justify-center text-emerald-800 text-2xl group-hover:scale-110 group-hover:bg-primary group-hover:border-primary group-hover:text-white group-hover:shadow-md group-hover:shadow-primary/25 transition-all duration-300">
                            <span>{{ $icon }}</span>
                        </div>
                        <h3 class="mt-4 text-base font-extrabold text-slate-900 group-hover:text-primary transition-colors">{{ $kategori->nama }}</h3>
                        <p class="mt-1 text-xs text-slate-500 line-clamp-2 leading-relaxed font-medium">{{ $kategori->deskripsi ?? 'Pilihan tanaman terbaik untuk mempercantik ruangan Anda.' }}</p>
                    </div>
                    <div class="mt-4 pt-3.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400 font-semibold">
                        <span class="bg-slate-100 px-2.5 py-0.5 rounded-full text-slate-700 font-bold">{{ $kategori->plants_count }} produk</span>
                        <span class="text-primary group-hover:translate-x-1 transition-transform font-bold">Jelajahi →</span>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    {{-- PRODUK UNGGULAN: FLASH DEAL / PILIHAN MINGGU INI --}}
    <section class="bg-gradient-to-b from-white via-slate-50/60 to-white border-y border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="inline-flex items-center gap-1.5 text-xs font-black uppercase tracking-wider text-rose-700 bg-rose-50 px-3 py-1 rounded-full border border-rose-200 shadow-2xs">
                            <span>🔥</span>
                            <span>Rekomendasi Spesial</span>
                        </span>
                        <span class="text-xs text-slate-400 font-medium hidden sm:inline">Paling banyak dipesan minggu ini</span>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Pilihan Minggu Ini</h2>
                    <p class="mt-1 text-sm text-slate-500 font-medium">Tanaman kualitas terbaik yang telah terkurasi dengan rating tinggi dari pembeli.</p>
                </div>
                <a href="{{ route('plants.index') }}" class="group inline-flex items-center gap-1.5 text-sm font-bold text-primary hover:text-primary-hover transition-colors">
                    <span>Eksplorasi katalog</span>
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>

            @if ($featuredPlants->isEmpty())
                <p class="text-center py-14 text-sm text-slate-400">Belum ada produk unggulan yang ditampilkan.</p>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6">
                    @foreach ($featuredPlants as $plant)
                        @include('partials.plant-card', ['plant' => $plant])
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- TOKO MITRA PILIHAN --}}
    @if ($tokoAktif->isNotEmpty())
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="mb-8">
                <span class="inline-block text-xs font-extrabold uppercase tracking-wider text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200 mb-2">
                    Mitra Terpercaya
                </span>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Toko Mitra di TanamKu</h2>
                <p class="mt-1 text-sm text-slate-500 font-medium">Pilih toko tanaman terdekat dari lokasi Anda untuk jaminan pengiriman lebih cepat dan kondisi tanaman tetap segar bugar.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                @foreach ($tokoAktif as $toko)
                    <a href="{{ route('stores.show', $toko) }}"
                       class="group bg-white rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-xl hover:border-primary/40 hover:-translate-y-1.5 transition-all duration-300 p-6 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-3.5">
                                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary-light to-emerald-100 overflow-hidden flex items-center justify-center text-primary font-bold text-lg shrink-0 group-hover:scale-105 shadow-xs transition-transform duration-300">
                                    @if ($toko->logo_url)
                                        <img src="{{ $toko->logo_url }}" alt="{{ $toko->nama_toko }}" class="w-full h-full object-cover">
                                    @else
                                        {{ mb_substr($toko->nama_toko, 0, 1) }}
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <h3 class="text-base font-extrabold text-slate-900 group-hover:text-primary transition-colors truncate">{{ $toko->nama_toko }}</h3>
                                        <span class="text-emerald-600 shrink-0 text-xs font-bold" title="Toko Terverifikasi">✓</span>
                                    </div>
                                    <p class="text-xs text-slate-500 font-semibold mt-0.5 flex items-center gap-1">
                                        <span>📍 {{ $toko->kota }}</span>
                                        <span>·</span>
                                        <span>{{ $toko->plants_count }} tanaman</span>
                                    </p>
                                </div>
                            </div>
                            <p class="mt-4 text-xs text-slate-600 line-clamp-2 leading-relaxed font-medium">{{ $toko->deskripsi }}</p>
                        </div>
                        <div class="mt-5 pt-3.5 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-primary">
                            <span>Kunjungi Halaman Toko</span>
                            <span class="group-hover:translate-x-1 transition-transform">→</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- CARA KERJA MARKETPLACE --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <div class="rounded-3xl bg-gradient-to-br from-primary-light via-emerald-50/60 to-accent-light/40 border border-primary/20 px-6 sm:px-10 py-12 sm:py-14 shadow-xs">
            <div class="text-center max-w-xl mx-auto">
                <span class="inline-block text-xs font-bold uppercase tracking-wider text-primary bg-white px-3.5 py-1 rounded-full border border-primary/20 mb-3 shadow-2xs">Mudah & Aman</span>
                <h2 class="text-2xl sm:text-3xl font-black text-primary tracking-tight">Cara Pesan di TanamKu</h2>
                <p class="mt-2 text-sm text-slate-600 font-medium">Hanya dalam 3 langkah praktis untuk mendapatkan tanaman hias impian langsung dari petaninya.</p>
            </div>

            <div class="mt-10 grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ([
                    ['1', 'Pilih Tanaman Hias', 'Jelajahi katalog, periksa tips perawatan, dan masukkan produk tanaman ke keranjang belanja.'],
                    ['2', 'Konfirmasi Pesanan', 'Tentukan metode ambil di toko atau kirim kurir. Format draf pesan WhatsApp dibuat otomatis oleh sistem.'],
                    ['3', 'Lanjut via WhatsApp', 'Diskusikan detail tanaman hidup, cara packing khusus, dan pengiriman langsung dengan penjual.'],
                ] as $langkah)
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 hover:-translate-y-1 hover:shadow-lg transition-all duration-300 group shadow-xs">
                        <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-primary to-emerald-700 text-white text-base font-black flex items-center justify-center group-hover:scale-110 group-hover:rotate-6 transition-transform duration-300 shadow-sm shadow-primary/30">
                            {{ $langkah[0] }}
                        </div>
                        <h3 class="mt-4 text-base font-bold text-slate-900 group-hover:text-primary transition-colors">{{ $langkah[1] }}</h3>
                        <p class="mt-2 text-xs text-slate-600 leading-relaxed font-medium">{{ $langkah[2] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="mt-10 text-center">
                <p class="text-xs text-slate-600 inline-flex items-center gap-2 bg-white/80 backdrop-blur-sm px-4 py-2 rounded-full border border-primary/10 shadow-2xs font-medium">
                    <span class="text-primary font-bold">💡 0% Komisi:</span> TanamKu menghubungkan pembeli dan penjual langsung tanpa potongan biaya transaksi.
                </p>
            </div>
        </div>
    </section>

    {{-- CTA BANNER BUKA TOKO --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-emerald-950 via-primary to-emerald-800 text-white p-8 sm:p-12 shadow-xl">
            <div class="ambient-orb absolute -right-12 -top-12 w-64 h-64 bg-accent/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-8">
                <div class="max-w-2xl">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-white/15 text-emerald-100 ring-1 ring-white/20 mb-3">
                        🌿 Peluang Kemitraan Penjual
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-black tracking-tight">Punya Kebun atau Menjual Tanaman Hias?</h2>
                    <p class="mt-2 text-sm sm:text-base text-emerald-100/90 leading-relaxed font-medium">
                        Buka toko Anda di TanamKu sekarang untuk memamerkan koleksi tanaman kepada ribuan pecinta tanaman di seluruh Indonesia. Gratis tanpa biaya pendaftaran!
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-3.5 shrink-0">
                    @guest
                        <a href="{{ route('register', ['role' => 'seller']) }}"
                           class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-white text-primary text-sm font-black shadow-lg hover:bg-emerald-50 hover:shadow-xl hover:-translate-y-0.5 active:scale-95 transition-all">
                            Daftar Toko Sekarang
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </a>
                    @else
                        @if (auth()->user()->isSeller())
                            <a href="{{ route('seller.plants.create') }}"
                               class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-white text-primary text-sm font-black shadow-lg hover:bg-emerald-50 hover:shadow-xl hover:-translate-y-0.5 active:scale-95 transition-all">
                                Tambah Produk Baru
                            </a>
                        @else
                            <a href="{{ route('plants.index') }}"
                               class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-white text-primary text-sm font-black shadow-lg hover:bg-emerald-50 hover:shadow-xl hover:-translate-y-0.5 active:scale-95 transition-all">
                                Jelajahi Semua Tanaman
                            </a>
                        @endif
                    @endguest
                </div>
            </div>
        </div>
    </section>
@endsection
