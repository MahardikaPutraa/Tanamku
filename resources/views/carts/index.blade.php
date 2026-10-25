@extends('layouts.app')

@section('title', 'Keranjang — TanamKu')

@section('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        @include('partials.alert')

        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Keranjang</h1>
                <p class="mt-1 text-sm text-gray-600">
                    Checkout dipisah per toko, karena setiap penjual punya WhatsApp sendiri.
                </p>
            </div>
            <a href="{{ route('plants.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-gray-100 text-sm font-semibold text-gray-700 hover:bg-gray-200">
                Lanjut belanja
            </a>
        </div>

        @if ($perToko->isEmpty())
            <div class="mt-8 text-center py-20 bg-white rounded-xl border border-dashed border-gray-200">
                <div class="w-14 h-14 mx-auto rounded-full bg-gray-50 flex items-center justify-center text-gray-400">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <h3 class="mt-4 text-base font-bold text-gray-900">Keranjang masih kosong</h3>
                <p class="mt-1.5 text-sm text-gray-600">Tambahkan tanaman dari katalog untuk mulai memesan.</p>
                <a href="{{ route('plants.index') }}" class="mt-5 inline-flex px-4 py-2 rounded-lg bg-primary text-white text-sm font-semibold hover:bg-primary-hover">
                    Lihat Katalog
                </a>
            </div>
        @else
            <div class="mt-6 space-y-6">
                @foreach ($perToko as $storeId => $items)
                    @php
                        $store = $items->first()->plant->store;
                        $subtotal = $items->sum(fn ($i) => $i->subtotal);
                    @endphp

                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between gap-3">
                            <a href="{{ route('stores.show', $store) }}" class="flex items-center gap-3 min-w-0 group">
                                <div class="w-9 h-9 rounded-lg bg-primary-light overflow-hidden flex items-center justify-center text-primary text-sm font-bold shrink-0">
                                    @if ($store->logo)
                                        <img src="{{ asset('storage/' . $store->logo) }}" alt="" class="w-full h-full object-cover">
                                    @else
                                        {{ mb_substr($store->nama_toko, 0, 1) }}
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-gray-900 group-hover:text-primary truncate">{{ $store->nama_toko }}</p>
                                    <p class="text-[11px] text-gray-500">{{ $store->kota }} · {{ $store->metode_serah_label }}</p>
                                </div>
                            </a>
                            <span class="text-xs text-gray-500">{{ $items->count() }} produk</span>
                        </div>

                        <div class="divide-y divide-gray-100">
                            @foreach ($items as $item)
                                <div class="px-5 py-4 flex flex-col sm:flex-row sm:items-center gap-4">
                                    <div class="w-16 h-16 rounded-lg bg-gray-50 border border-gray-100 overflow-hidden shrink-0">
                                        @if ($item->plant->foto)
                                            <img src="{{ asset('storage/' . $item->plant->foto) }}" alt="{{ $item->plant->nama }}" class="w-full h-full object-cover">
                                        @endif
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <a href="{{ route('plants.show', $item->plant) }}" class="text-sm font-bold text-gray-900 hover:text-primary">
                                            {{ $item->plant->nama }}
                                        </a>
                                        <p class="text-xs text-gray-500 mt-0.5">
                                            {{ $item->plant->harga_rupiah }} · stok {{ $item->plant->stok }}
                                        </p>
                                    </div>

                                    <form method="POST" action="{{ route('carts.update', $item) }}" class="flex items-center gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <input type="number" name="jumlah" value="{{ $item->jumlah }}" min="1" max="{{ $item->plant->stok }}"
                                               class="w-20 px-2 py-1.5 rounded-lg border border-gray-300 text-sm text-center outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                                        <button type="submit" class="px-2.5 py-1.5 rounded-lg text-[11px] font-semibold text-primary bg-primary-light hover:bg-emerald-100">
                                            Ubah
                                        </button>
                                    </form>

                                    <div class="text-right sm:w-28">
                                        <p class="text-sm font-extrabold text-gray-900">{{ $item->subtotal_rupiah }}</p>
                                    </div>

                                    <form method="POST" action="{{ route('carts.destroy', $item) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 rounded-lg text-gray-400 hover:text-danger hover:bg-red-50" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>

                        <div class="px-5 py-4 bg-gray-50 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div class="text-sm">
                                <span class="text-gray-600">Subtotal toko ini:</span>
                                <span class="ml-2 font-extrabold text-gray-900">
                                    Rp {{ number_format($subtotal, 0, ',', '.') }}
                                </span>
                            </div>

                            <a href="{{ route('checkout.create', $store) }}"
                               class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg bg-emerald-600 text-white text-sm font-bold hover:bg-emerald-700 transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38a9.9 9.9 0 004.79 1.22h.01c5.46 0 9.91-4.45 9.91-9.91C21.96 6.45 17.5 2 12.04 2z"/>
                                </svg>
                                Checkout via WhatsApp
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex items-center justify-between">
                <span class="text-sm text-gray-600">Total seluruh keranjang</span>
                <span class="text-xl font-extrabold text-primary">Rp {{ number_format($totalSemua, 0, ',', '.') }}</span>
            </div>
        @endif
    </div>
@endsection
