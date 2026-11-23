@extends('layouts.app')

@section('title', 'Keranjang — TanamKu')

@section('content')
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
        @include('partials.alert')

        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs text-slate-500 mb-2">
                    <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Beranda</a>
                    <span class="text-slate-400">/</span>
                    <span class="text-slate-900 font-semibold">Keranjang Belanja</span>
                </nav>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Keranjang Belanja</h1>
                <p class="mt-1 text-sm text-slate-600">
                    Pemesanan diproses per toko untuk menghubungkan Anda langsung dengan masing-masing penjual.
                </p>
            </div>
            <a href="{{ route('plants.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-slate-100 text-sm font-bold text-slate-700 hover:bg-slate-200 active-press transition-all shadow-2xs">
                ← Lanjut Belanja
            </a>
        </div>

        @if ($perToko->isEmpty())
            <div class="mt-8 text-center py-20 bg-white rounded-3xl border border-dashed border-slate-200 shadow-xs max-w-md mx-auto">
                <div class="w-16 h-16 mx-auto rounded-full bg-emerald-50 flex items-center justify-center text-emerald-600 mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-900">Keranjang masih kosong</h3>
                <p class="mt-1.5 text-xs text-slate-500 max-w-xs mx-auto">Yuk cari tanaman hias impian Anda dari ratusan koleksi terbaik di katalog TanamKu.</p>
                <a href="{{ route('plants.index') }}" class="mt-5 inline-flex px-6 py-2.5 rounded-xl bg-primary text-white text-sm font-bold hover:bg-primary-hover shadow-sm active-press transition-all">
                    Jelajahi Katalog Tanaman
                </a>
            </div>
        @else
            <div class="mt-8 space-y-6">
                @foreach ($perToko as $storeId => $items)
                    @php
                        $store = $items->first()->plant->store;
                        $subtotal = $items->sum(fn ($i) => $i->subtotal);
                    @endphp

                    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
                        {{-- Header Toko --}}
                        <div class="px-5 py-4 bg-slate-50/70 border-b border-slate-100 flex items-center justify-between gap-3">
                            <a href="{{ route('stores.show', $store) }}" class="flex items-center gap-3 min-w-0 group">
                                <div class="w-10 h-10 rounded-xl bg-primary-light overflow-hidden flex items-center justify-center text-primary text-sm font-bold shrink-0 group-hover:scale-105 shadow-2xs transition-transform">
                                    @if ($store->logo_url)
                                        <img src="{{ $store->logo_url }}" alt="" class="w-full h-full object-cover">
                                    @else
                                        {{ mb_substr($store->nama_toko, 0, 1) }}
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <p class="text-sm font-bold text-slate-900 group-hover:text-primary transition-colors truncate">{{ $store->nama_toko }}</p>
                                        <span class="text-emerald-600 font-bold shrink-0 text-xs" title="Toko Terverifikasi">✓</span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 font-medium">📍 {{ $store->kota }} · {{ $store->metode_serah_label }}</p>
                                </div>
                            </a>
                            <span class="text-xs font-semibold text-slate-500 bg-white px-3 py-1 rounded-full border border-slate-200 shadow-2xs">{{ $items->count() }} Produk</span>
                        </div>

                        {{-- Daftar Produk di Toko Ini --}}
                        <div class="divide-y divide-slate-100">
                            @foreach ($items as $item)
                                <div class="px-4 sm:px-6 py-4 sm:py-5 flex flex-col sm:flex-row sm:items-center gap-4">
                                    <div class="flex items-center gap-3.5 min-w-0 flex-1">
                                        <div class="w-16 sm:w-20 h-16 sm:h-20 rounded-2xl bg-slate-50 border border-slate-200/80 overflow-hidden shrink-0 shadow-2xs">
                                            @if ($item->plant->foto_url)
                                                <img src="{{ $item->plant->foto_url }}" alt="{{ $item->plant->nama }}" class="w-full h-full object-cover">
                                            @endif
                                        </div>

                                        <div class="min-w-0 flex-1">
                                            <a href="{{ route('plants.show', $item->plant) }}" class="text-sm sm:text-base font-bold text-slate-900 hover:text-primary transition-colors block truncate">
                                                {{ $item->plant->nama }}
                                            </a>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="text-xs font-extrabold text-primary">{{ $item->plant->harga_rupiah }}</span>
                                                <span class="text-slate-300">·</span>
                                                <span class="text-[11px] text-slate-500">Stok: {{ $item->plant->stok }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between sm:justify-end gap-4 w-full sm:w-auto pt-3 sm:pt-0 border-t sm:border-t-0 border-slate-100">
                                        <form method="POST" action="{{ route('carts.update', $item) }}" class="flex items-center gap-1.5">
                                            @csrf
                                            @method('PATCH')
                                            <input type="number" name="jumlah" value="{{ $item->jumlah }}" min="1" max="{{ $item->plant->stok }}"
                                                   class="w-16 sm:w-20 px-2 py-1.5 rounded-xl border border-slate-200 text-sm font-bold text-center outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 shadow-2xs">
                                            <button type="submit" class="px-3 py-1.5 rounded-xl text-xs font-bold text-primary bg-primary/10 hover:bg-primary/20 transition-colors">
                                                Ubah
                                            </button>
                                        </form>

                                        <div class="text-right sm:w-32">
                                            <span class="text-[10px] text-slate-400 block font-medium">Subtotal</span>
                                            <p class="text-sm sm:text-base font-black text-slate-900">{{ $item->subtotal_rupiah }}</p>
                                        </div>

                                        <form method="POST" action="{{ route('carts.destroy', $item) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 rounded-xl text-slate-400 hover:text-danger hover:bg-red-50 transition-colors" title="Hapus item">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Footer Checkout Toko --}}
                        <div class="px-5 py-4 bg-slate-50/80 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div class="text-sm">
                                <span class="text-slate-600 font-medium">Subtotal pesanan toko ini:</span>
                                <span class="ml-2 font-black text-slate-900 text-base sm:text-lg">
                                    Rp {{ number_format($subtotal, 0, ',', '.') }}
                                </span>
                            </div>

                            <a href="{{ route('checkout.create', $store) }}"
                               class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-bold hover:bg-emerald-700 active-press shadow-md shadow-emerald-600/20 transition-all">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38a9.9 9.9 0 004.79 1.22h.01c5.46 0 9.91-4.45 9.91-9.91C21.96 6.45 17.5 2 12.04 2z"/>
                                </svg>
                                Lanjut Checkout (WhatsApp)
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Ringkasan Total Keranjang --}}
            <div class="mt-8 bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Ringkasan Total</span>
                    <span class="text-sm text-slate-600 mt-0.5 block">Total perkiraan dari semua toko di keranjang belanja Anda:</span>
                </div>
                <div class="flex items-center gap-2 sm:text-right">
                    <span class="text-2xl sm:text-3xl font-black text-primary">Rp {{ number_format($totalSemua, 0, ',', '.') }}</span>
                </div>
            </div>
        @endif
    </div>
@endsection
