@extends('layouts.app')

@section('title', $plant->nama . ' — TanamKu')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10">
        @include('partials.alert')

        {{-- Breadcrumb Navigation --}}
        <nav class="flex items-center gap-2 text-xs text-slate-500 mb-6 flex-wrap">
            <a href="{{ route('home') }}" class="hover:text-primary transition-colors flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Beranda
            </a>
            <span class="text-slate-400">/</span>
            <a href="{{ route('plants.index') }}" class="hover:text-primary transition-colors">Katalog</a>
            <span class="text-slate-400">/</span>
            <a href="{{ route('stores.show', $plant->store) }}" class="hover:text-primary transition-colors">{{ $plant->store->nama_toko }}</a>
            <span class="text-slate-400">/</span>
            <span class="text-slate-900 font-semibold truncate max-w-xs">{{ $plant->nama }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
            {{-- KOLOM KIRI: FOTO & JAMINAN (5 kolom) --}}
            <div class="lg:col-span-5 space-y-4">
                <div class="relative w-full aspect-square rounded-3xl overflow-hidden bg-slate-50 border border-slate-200/80 shadow-md group">
                    @if ($plant->foto_url)
                        <img src="{{ $plant->foto_url }}" alt="{{ $plant->nama }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center text-emerald-700/50 gap-3">
                            <svg class="w-24 h-24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 21v-8m0 0C12 8 8 4 3 4c0 5 4 9 9 9zm0 0c0-5 4-9 9-9 0 5-4 9-9 9z"/>
                            </svg>
                            <span class="text-xs font-medium">Foto belum tersedia</span>
                        </div>
                    @endif

                    {{-- Badge Status Stok --}}
                    <div class="absolute top-4 right-4 z-10">
                        @if ($plant->stok <= 0)
                            <span class="px-3.5 py-1.5 rounded-full text-xs font-bold bg-danger text-white shadow-md">Stok Habis</span>
                        @elseif ($plant->stok <= 4)
                            <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-amber-500 text-white shadow-md">
                                <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                                Sisa {{ $plant->stok }} Pot
                            </span>
                        @else
                            <span class="px-3.5 py-1.5 rounded-full text-xs font-semibold bg-emerald-600/90 text-white backdrop-blur-md border border-white/20 shadow-md">Tersedia {{ $plant->stok }} Pot</span>
                        @endif
                    </div>
                </div>

                {{-- Pilar Jaminan Belanja Modern --}}
                <div class="grid grid-cols-3 gap-2.5 p-3.5 rounded-2xl bg-white border border-slate-200/80 text-center text-slate-700 shadow-2xs">
                    <div class="flex flex-col items-center gap-1 p-2">
                        <span class="text-lg">🌿</span>
                        <span class="text-[11px] font-bold leading-tight">100% Segar</span>
                        <span class="text-[10px] text-slate-400">Dari Kebun Asli</span>
                    </div>
                    <div class="flex flex-col items-center gap-1 p-2 border-x border-slate-100">
                        <span class="text-lg">📦</span>
                        <span class="text-[11px] font-bold leading-tight">Packing Aman</span>
                        <span class="text-[10px] text-slate-400">Proteksi Khusus</span>
                    </div>
                    <div class="flex flex-col items-center gap-1 p-2">
                        <span class="text-lg">💬</span>
                        <span class="text-[11px] font-bold leading-tight">Foto Real-Time</span>
                        <span class="text-[10px] text-slate-400">Via WhatsApp</span>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: DETAIL PRODUK, TOKO & AKSI PEMBELIAN (7 kolom) --}}
            <div class="lg:col-span-7 space-y-6">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="inline-block px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                            {{ $plant->category->nama }}
                        </span>
                        <span class="text-xs text-slate-400">·</span>
                        <span class="text-xs font-semibold {{ $plant->kondisi === 'sehat' ? 'text-emerald-700' : 'text-amber-700' }}">
                            {{ $plant->kondisi === 'sehat' ? '✓ Kondisi Prima' : '⚠ Perlu Perhatian' }}
                        </span>
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">{{ $plant->nama }}</h1>

                    @if ($plant->rating_rata_rata)
                        <div class="mt-3 flex items-center gap-2 text-sm">
                            <div class="flex items-center text-amber-500 font-bold bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200/60">
                                <svg class="w-4 h-4 fill-current mr-1" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <span>{{ $plant->rating_rata_rata }}</span>
                                <span class="text-xs text-slate-400 font-normal ml-0.5">/ 5.0</span>
                            </div>
                            <span class="text-slate-300">·</span>
                            <a href="#ulasan" class="text-xs text-primary hover:underline font-semibold">
                                {{ $plant->total_ulasan }} ulasan pembeli
                            </a>
                        </div>
                    @else
                        <div class="mt-2.5 flex items-center gap-1.5 text-xs text-slate-400">
                            <span class="text-amber-400">★</span>
                            <span>Belum ada ulasan (Koleksi Baru)</span>
                        </div>
                    @endif
                </div>

                {{-- KOTAK HARGA MARKETPLACE --}}
                <div class="p-4 sm:p-5 rounded-2xl bg-slate-50/80 border border-slate-200/80 flex items-center justify-between">
                    <div>
                        <span class="text-xs text-slate-400 font-semibold block">Harga Produk</span>
                        <div class="flex items-baseline gap-2 mt-0.5">
                            <span class="text-3xl sm:text-4xl font-extrabold text-primary tracking-tight">{{ $plant->harga_rupiah }}</span>
                            <span class="text-xs text-slate-500 font-medium">/ tanaman</span>
                        </div>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                        Harga Terbaik
                    </span>
                </div>

                {{-- SPESIFIKASI PRODUK (DOM Inspection Contract Preserved) --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 text-xs">
                    <div class="p-3.5 rounded-2xl bg-white border border-slate-200/80 shadow-2xs">
                        <span class="text-[10px] text-slate-400 block uppercase font-bold tracking-wider">Kategori</span>
                        <span class="font-bold text-slate-800 mt-1 block truncate">{{ $plant->category->nama }}</span>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-white border border-slate-200/80 shadow-2xs">
                        <span class="text-[10px] text-slate-400 block uppercase font-bold tracking-wider">Lokasi Kebun</span>
                        <span class="font-bold text-slate-800 mt-1 block truncate">📍 {{ $plant->store->kota }}</span>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-white border border-slate-200/80 shadow-2xs">
                        <span class="text-[10px] text-slate-400 block uppercase font-bold tracking-wider">Pengiriman</span>
                        <span class="font-bold text-slate-800 mt-1 block truncate">{{ $plant->store->metode_serah_label }}</span>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-white border border-slate-200/80 shadow-2xs">
                        <span class="text-[10px] text-slate-400 block uppercase font-bold tracking-wider">Kondisi</span>
                        <span class="font-bold {{ $plant->kondisi === 'sehat' ? 'text-emerald-700' : 'text-amber-700' }} mt-1 block truncate">
                            {{ $plant->kondisi === 'sehat' ? '✓ Sehat Terawat' : '⚠ Perlu Rawat' }}
                        </span>
                    </div>
                </div>

                {{-- Deskripsi Produk --}}
                <div class="pt-2">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Deskripsi Produk</h3>
                    <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">{{ $plant->deskripsi }}</p>
                </div>

                {{-- KARTU PROFIL TOKO MITRA --}}
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 sm:p-5">
                    <div class="flex items-center justify-between gap-3">
                        <a href="{{ route('stores.show', $plant->store) }}" class="flex items-center gap-3.5 min-w-0 group">
                            <div class="w-12 h-12 rounded-2xl bg-primary-light overflow-hidden flex items-center justify-center text-primary font-extrabold text-base shrink-0 group-hover:scale-105 transition-transform shadow-xs">
                                @if ($plant->store->logo_url)
                                    <img src="{{ $plant->store->logo_url }}" alt="{{ $plant->store->nama_toko }}" class="w-full h-full object-cover">
                                @else
                                    {{ mb_substr($plant->store->nama_toko, 0, 1) }}
                                @endif
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-1.5">
                                    <p class="text-sm font-bold text-slate-900 group-hover:text-primary transition-colors truncate">{{ $plant->store->nama_toko }}</p>
                                    <span class="text-emerald-600 font-bold shrink-0 text-xs" title="Toko Terverifikasi">✓</span>
                                </div>
                                <p class="text-xs text-slate-500 font-medium mt-0.5">📍 {{ $plant->store->kota }} · {{ $plant->store->metode_serah_label }}</p>
                            </div>
                        </a>

                        <a href="{{ route('stores.show', $plant->store) }}"
                           class="shrink-0 px-4 py-2 rounded-xl text-xs font-bold text-primary bg-primary/10 hover:bg-primary/20 border border-primary/20 transition-all">
                            Kunjungi Toko
                        </a>
                    </div>
                </div>

                {{-- KOTAK AKSI PEMESANAN (CHECKOUT & KERANJANG) --}}
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-md p-5 sm:p-6 space-y-4">
                    @auth
                        @if (auth()->user()->isBuyer())
                            @if ($plant->stok <= 0)
                                <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-sm text-red-800">
                                    Stok saat ini sedang habis. Silakan periksa produk lain dari toko ini.
                                </div>
                            @else
                                <form method="POST" action="{{ route('carts.store', $plant) }}" class="space-y-4">
                                    @csrf
                                    <div>
                                        <div class="flex items-center justify-between mb-2">
                                            <label for="jumlah" class="block text-xs font-bold text-slate-700">
                                                Atur Jumlah <span class="font-normal text-slate-400">(maks {{ $plant->stok }} pot)</span>
                                            </label>
                                            <div class="text-xs text-slate-500">
                                                Subtotal: <span id="subtotal-text" class="text-base font-extrabold text-primary ml-1">{{ $plant->harga_rupiah }}</span>
                                            </div>
                                        </div>

                                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                                            <div class="w-full sm:w-36 flex items-center border border-slate-200 rounded-xl overflow-hidden bg-slate-50 shadow-2xs">
                                                <button type="button" onclick="const el = document.getElementById('jumlah'); if(el.value > 1) { el.value--; el.dispatchEvent(new Event('input')); }"
                                                        class="px-3.5 py-2.5 text-slate-600 hover:bg-slate-200 font-bold transition select-none">−</button>
                                                <input type="number" id="jumlah" name="jumlah" value="1" min="1" max="{{ $plant->stok }}"
                                                       class="w-full text-center bg-transparent text-sm font-bold text-slate-800 outline-none">
                                                <button type="button" onclick="const el = document.getElementById('jumlah'); if(el.value < {{ $plant->stok }}) { el.value++; el.dispatchEvent(new Event('input')); }"
                                                        class="px-3.5 py-2.5 text-slate-600 hover:bg-slate-200 font-bold transition select-none">+</button>
                                            </div>

                                            <button type="submit"
                                                    class="flex-1 px-6 py-3 rounded-xl bg-primary text-white text-sm font-bold shadow-md hover:bg-primary-hover hover:shadow-lg active-press transition-all flex items-center justify-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                </svg>
                                                Tambah ke Keranjang
                                            </button>
                                        </div>
                                    </div>
                                </form>

                                <p class="text-xs text-slate-500 flex items-center gap-1.5 pt-1">
                                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38a9.9 9.9 0 004.79 1.22h.01c5.46 0 9.91-4.45 9.91-9.91C21.96 6.45 17.5 2 12.04 2z"/>
                                    </svg>
                                    Format pesanan dibuat otomatis dan dapat disepakati langsung via WhatsApp penjual.
                                </p>
                            @endif
                        @elseif (auth()->user()->isSeller())
                            <p class="text-sm text-slate-600 bg-slate-50 p-3.5 rounded-xl border border-slate-200">
                                Anda sedang masuk dengan akun <strong>Penjual</strong>. Pemesanan produk dilakukan oleh akun Pembeli.
                            </p>
                        @else
                            <p class="text-sm text-slate-600 bg-slate-50 p-3.5 rounded-xl border border-slate-200">
                                Anda sedang masuk dengan akun <strong>Admin</strong>.
                            </p>
                        @endif
                    @else
                        <div class="text-center py-2">
                            <p class="text-sm font-bold text-slate-900">Masuk untuk memesan tanaman ini</p>
                            <p class="mt-1 text-xs text-slate-500">Buat akun pembeli agar pesanan tercatat rapi dan bisa dilacak kapan saja.</p>
                            <div class="mt-4 flex flex-col sm:flex-row gap-2.5 justify-center">
                                <a href="{{ route('login') }}" class="px-6 py-2.5 rounded-xl bg-primary text-white text-sm font-bold hover:bg-primary-hover active-press transition-all shadow-sm">Masuk</a>
                                <a href="{{ route('register') }}" class="px-6 py-2.5 rounded-xl bg-white border border-slate-200 text-sm font-semibold text-slate-800 hover:bg-slate-50 active-press transition-all">Daftar Akun Baru</a>
                            </div>
                        </div>
                    @endauth

                    {{-- Bagikan Produk (DOM Inspection Contract Preserved) --}}
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                        <span>Bagikan tanaman ini:</span>
                        <a href="https://wa.me/?text={{ rawurlencode('Lihat tanaman ' . $plant->nama . ' di TanamKu: ' . request()->url()) }}"
                           target="_blank" rel="noopener"
                           class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 font-bold hover:bg-emerald-100 border border-emerald-200 active-press transition-all">
                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                                <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38a9.9 9.9 0 004.79 1.22h.01c5.46 0 9.91-4.45 9.91-9.91C21.96 6.45 17.5 2 12.04 2z"/>
                            </svg>
                            Bagikan ke WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- CARA MERAWAT TANAMAN --}}
        @if (! empty($plant->perawatan_poin))
            <div class="mt-14">
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 lg:p-8">
                    <div class="flex items-start gap-3.5 mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-primary-light text-primary flex items-center justify-center shrink-0 shadow-2xs">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21v-8m0 0C12 8 8 4 3 4c0 5 4 9 9 9zm0 0c0-5 4-9 9-9 0 5-4 9-9 9z"/>
                            </svg>
                        </div>
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-primary bg-primary/10 px-2.5 py-0.5 rounded-md">Tips Petani</span>
                            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight mt-1">Panduan Perawatan {{ $plant->nama }}</h2>
                            <p class="mt-0.5 text-xs text-slate-500">Petunjuk resmi dari penjual agar tanaman tumbuh subur dan rimbun.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($plant->perawatan_poin as $poin)
                            <div class="p-4 rounded-2xl bg-slate-50/60 border border-slate-200/80 hover:bg-white hover:shadow-xs transition-all flex flex-col justify-between">
                                <div>
                                    @if ($poin['label'])
                                        <span class="inline-block text-[11px] font-extrabold uppercase tracking-wider text-emerald-800 bg-emerald-100/70 px-2 py-0.5 rounded-md mb-2">
                                            {{ $poin['label'] }}
                                        </span>
                                    @endif
                                    <p class="text-sm text-slate-700 leading-relaxed">{{ $poin['isi'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- ULASAN PEMBELI --}}
        <div id="ulasan" class="mt-14 scroll-mt-6">
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 lg:p-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-100">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-md border border-emerald-200">Ulasan Nyata</span>
                        <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight mt-1">Penilaian Pembeli</h2>
                        <p class="mt-0.5 text-xs text-slate-500">Ulasan asli dari pembeli yang telah menerima tanaman ini.</p>
                    </div>

                    <div class="flex items-center gap-3">
                        @if ($plant->rating_rata_rata)
                            <div class="flex items-center text-amber-500 text-2xl font-black bg-amber-50 px-3.5 py-1.5 rounded-2xl border border-amber-200">
                                <svg class="w-6 h-6 fill-current mr-1.5" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <span>{{ $plant->rating_rata_rata }}</span>
                                <span class="text-sm text-slate-400 font-normal ml-1">/ 5.0</span>
                            </div>
                            <span class="text-xs text-slate-500">({{ $plant->total_ulasan }} ulasan)</span>
                        @else
                            <span class="text-xs text-slate-400 font-medium">Belum ada penilaian</span>
                        @endif
                    </div>
                </div>

                <div class="mt-6 space-y-4">
                    @forelse ($plant->reviews as $review)
                        <div class="p-4 sm:p-5 rounded-2xl bg-slate-50/70 border border-slate-200/80">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-800 font-bold text-xs flex items-center justify-center">
                                        {{ mb_substr($review->user->name ?? 'P', 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-900">{{ $review->user->name ?? 'Pembeli' }}</p>
                                        <p class="text-[10px] text-slate-400">{{ $review->created_at->format('d M Y') }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center text-amber-400">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= $review->rating ? 'fill-current' : 'text-slate-200 fill-current' }}" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                </div>
                            </div>

                            @if ($review->komentar)
                                <p class="mt-3 text-sm text-slate-700 leading-relaxed">{{ $review->komentar }}</p>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-8 text-slate-400 text-sm">
                            <p>Belum ada ulasan untuk tanaman ini.</p>
                            <p class="text-xs mt-1 text-slate-400">Beli tanaman ini dan jadilah yang pertama memberikan ulasan!</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- LAINNYA DARI TOKO INI --}}
        @if ($lainnya->isNotEmpty())
            <div class="mt-14">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-primary bg-primary/10 px-2.5 py-0.5 rounded-md">Dari Toko yang Sama</span>
                        <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight mt-1">Produk Lain dari {{ $plant->store->nama_toko }}</h2>
                    </div>
                    <a href="{{ route('stores.show', $plant->store) }}" class="text-xs font-bold text-primary hover:underline">Lihat Semua →</a>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6">
                    @foreach ($lainnya as $item)
                        @include('partials.plant-card', ['plant' => $item])
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const harga = {{ (float) $plant->harga }};
                const input = document.getElementById('jumlah');
                const subtotal = document.getElementById('subtotal-text');
                if (input && subtotal) {
                    input.addEventListener('input', function () {
                        let qty = parseInt(this.value, 10);
                        if (isNaN(qty) || qty < 1) qty = 1;
                        const total = harga * qty;
                        subtotal.textContent = 'Rp ' + total.toLocaleString('id-ID');
                    });
                }
            });
        </script>
    @endpush
@endsection
