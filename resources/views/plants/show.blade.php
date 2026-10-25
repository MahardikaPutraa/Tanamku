@extends('layouts.app')

@section('title', $plant->nama . ' — TanamKu')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @include('partials.alert')

        <nav class="text-xs text-gray-500 mb-6">
            <a href="{{ route('home') }}" class="hover:text-primary">Beranda</a>
            <span class="mx-1.5">/</span>
            <a href="{{ route('plants.index') }}" class="hover:text-primary">Katalog</a>
            <span class="mx-1.5">/</span>
            <a href="{{ route('stores.show', $plant->store) }}" class="hover:text-primary">{{ $plant->store->nama_toko }}</a>
            <span class="mx-1.5">/</span>
            <span class="text-gray-800 font-medium">{{ $plant->nama }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            {{-- FOTO --}}
            <div>
                <div class="relative w-full aspect-square rounded-xl overflow-hidden bg-gray-50 border border-gray-100">
                    @if ($plant->foto)
                        <img src="{{ asset('storage/' . $plant->foto) }}" alt="{{ $plant->nama }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center text-emerald-700/50 gap-3">
                            <svg class="w-24 h-24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 21v-8m0 0C12 8 8 4 3 4c0 5 4 9 9 9zm0 0c0-5 4-9 9-9 0 5-4 9-9 9z"/>
                            </svg>
                            <span class="text-xs font-medium">Foto belum tersedia</span>
                        </div>
                    @endif

                    <div class="absolute top-4 right-4">
                        @if ($plant->stok <= 0)
                            <span class="px-3 py-1.5 rounded-full text-xs font-bold bg-danger text-white shadow">Stok Habis</span>
                        @elseif ($plant->stok <= 4)
                            <span class="px-3 py-1.5 rounded-full text-xs font-bold bg-amber-500 text-white shadow">Sisa {{ $plant->stok }}</span>
                        @else
                            <span class="px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200 shadow">Stok {{ $plant->stok }}</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- INFO + AKSI --}}
            <div>
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ $plant->nama }}</h1>
                        <p class="mt-1.5 text-sm text-gray-500">
                            {{ $plant->category->nama }} · Kondisi {{ $plant->kondisi === 'sehat' ? 'sehat terawat' : 'perlu perhatian' }}
                        </p>
                    </div>
                </div>

                <div class="mt-4 flex items-baseline gap-3">
                    <span class="text-3xl font-extrabold text-primary">{{ $plant->harga_rupiah }}</span>
                    <span class="text-sm text-gray-500">/ pot</span>
                </div>

                <p class="mt-5 text-sm text-gray-700 leading-relaxed">{{ $plant->deskripsi }}</p>

                {{-- Kartu toko --}}
                <div class="mt-6 bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                    <div class="flex items-center justify-between gap-3">
                        <a href="{{ route('stores.show', $plant->store) }}" class="flex items-center gap-3 min-w-0 group">
                            <div class="w-11 h-11 rounded-lg bg-primary-light overflow-hidden flex items-center justify-center text-primary font-bold shrink-0">
                                @if ($plant->store->logo)
                                    <img src="{{ asset('storage/' . $plant->store->logo) }}" alt="{{ $plant->store->nama_toko }}" class="w-full h-full object-cover">
                                @else
                                    {{ mb_substr($plant->store->nama_toko, 0, 1) }}
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-gray-900 group-hover:text-primary truncate">{{ $plant->store->nama_toko }}</p>
                                <p class="text-xs text-gray-500">{{ $plant->store->kota }} · {{ $plant->store->metode_serah_label }}</p>
                            </div>
                        </a>

                        <a href="{{ route('stores.show', $plant->store) }}"
                           class="shrink-0 px-3 py-1.5 rounded-lg text-xs font-semibold text-primary bg-primary-light hover:bg-emerald-100">
                            Lihat toko
                        </a>
                    </div>
                </div>

                {{-- Aksi pembeli --}}
                <div class="mt-6 bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    @auth
                        @if (auth()->user()->isBuyer())
                            @if ($plant->stok <= 0)
                                <div class="p-4 rounded-lg bg-red-50 border border-red-200 text-sm text-red-800">
                                    Stok sedang habis. Coba lihat produk lain dari toko ini.
                                </div>
                            @else
                                <form method="POST" action="{{ route('carts.store', $plant) }}" class="flex items-end gap-3">
                                    @csrf
                                    <div class="w-28">
                                        <label for="jumlah" class="block text-xs font-semibold text-gray-700 mb-1.5">
                                            Jumlah <span class="font-normal text-gray-400">(maks {{ $plant->stok }})</span>
                                        </label>
                                        <input type="number" id="jumlah" name="jumlah" value="1" min="1" max="{{ $plant->stok }}"
                                               class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                                    </div>
                                    <button type="submit"
                                            class="flex-1 px-5 py-2.5 rounded-lg bg-primary text-white text-sm font-bold shadow-sm hover:bg-primary-hover transition-colors">
                                        Tambah ke Keranjang
                                    </button>
                                </form>

                                <p class="mt-3 text-[11px] text-gray-500">
                                    Checkout akan membuat pesan WhatsApp otomatis ke penjual.
                                </p>
                            @endif
                        @elseif (auth()->user()->isSeller())
                            <p class="text-sm text-gray-600">
                                Anda masuk sebagai penjual. Hanya akun pembeli yang bisa memesan.
                            </p>
                        @else
                            <p class="text-sm text-gray-600">
                                Anda masuk sebagai admin. Pembelian dilakukan oleh akun pembeli.
                            </p>
                        @endif
                    @else
                        <div class="text-center">
                            <p class="text-sm font-bold text-gray-900">Masuk dulu untuk memesan</p>
                            <p class="mt-1.5 text-xs text-gray-600">
                                Anda perlu akun pembeli agar pesanan tercatat dan bisa dilacak.
                            </p>
                            <div class="mt-4 flex flex-col sm:flex-row gap-2 justify-center">
                                <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-lg bg-primary text-white text-sm font-bold hover:bg-primary-hover">Masuk</a>
                                <a href="{{ route('register') }}" class="px-5 py-2.5 rounded-lg bg-white border border-gray-300 text-sm font-semibold text-gray-800 hover:bg-gray-100">Daftar Akun</a>
                            </div>
                            <p class="mt-3 text-[11px] text-gray-500">Hanya ingin melihat-lihat? Tidak perlu masuk.</p>
                        </div>
                    @endauth
                </div>
            </div>
        </div>

        {{-- CARA MERAWAT --}}
        @if (! empty($plant->perawatan_poin))
            <div class="mt-14">
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 lg:p-8">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-lg bg-primary-light text-primary flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21v-8m0 0C12 8 8 4 3 4c0 5 4 9 9 9zm0 0c0-5 4-9 9-9 0 5-4 9-9 9z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-extrabold text-gray-900 tracking-tight">Cara Merawat {{ $plant->nama }}</h2>
                            <p class="mt-1 text-xs text-gray-500">Panduan dari penjual agar tanaman tetap sehat.</p>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach ($plant->perawatan_poin as $poin)
                            <div class="flex gap-3 p-4 rounded-lg bg-surface border border-gray-100">
                                <span class="mt-1.5 w-1.5 h-1.5 rounded-full bg-primary shrink-0"></span>
                                <p class="text-sm text-gray-700 leading-relaxed">
                                    @if ($poin['label'])
                                        <strong class="text-gray-900">{{ $poin['label'] }}:</strong>
                                    @endif
                                    {{ $poin['isi'] }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- LAINNYA DARI TOKO INI --}}
        @if ($lainnya->isNotEmpty())
            <div class="mt-14">
                <h2 class="text-xl font-extrabold text-gray-900 tracking-tight mb-6">Produk lain dari {{ $plant->store->nama_toko }}</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($lainnya as $item)
                        @include('partials.plant-card', ['plant' => $item])
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection
