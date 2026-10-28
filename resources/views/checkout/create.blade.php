@extends('layouts.app')

@section('title', 'Checkout — ' . $store->nama_toko)

@section('content')
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        @include('partials.alert')

        <nav class="text-xs text-gray-500 mb-5">
            <a href="{{ route('carts.index') }}" class="hover:text-primary">Keranjang</a>
            <span class="mx-1.5">/</span>
            <span class="text-gray-800 font-medium">Checkout</span>
        </nav>

        <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Checkout ke {{ $store->nama_toko }}</h1>
        <p class="mt-1 text-sm text-gray-600">
            Pesanan akan dicatat, lalu pesan WhatsApp dibuat otomatis untuk penjual.
        </p>

        {{-- ALUR CHECKOUT STEPPER --}}
        <div class="mt-6 p-4 rounded-xl bg-white border border-gray-100 shadow-xs">
            <div class="grid grid-cols-3 gap-2 text-center text-xs">
                <div class="flex items-center gap-2 justify-center text-emerald-700 font-semibold">
                    <span class="w-6 h-6 rounded-full bg-emerald-100 flex items-center justify-center text-xs">✓</span>
                    <span>1. Keranjang</span>
                </div>
                <div class="flex items-center gap-2 justify-center text-primary font-bold">
                    <span class="w-6 h-6 rounded-full bg-primary text-white flex items-center justify-center text-xs">2</span>
                    <span>2. Data Pengiriman</span>
                </div>
                <div class="flex items-center gap-2 justify-center text-gray-400 font-medium">
                    <span class="w-6 h-6 rounded-full bg-gray-100 flex items-center justify-center text-xs">3</span>
                    <span>3. Chat WhatsApp</span>
                </div>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 lg:grid-cols-5 gap-6">
            {{-- FORM --}}
            <div class="lg:col-span-3">
                <form method="POST" action="{{ route('checkout.store', $store) }}" class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4">
                    @csrf

                    <div>
                        <label for="nama_penerima" class="block text-xs font-semibold text-gray-700 mb-1.5">Nama Penerima</label>
                        <input type="text" id="nama_penerima" name="nama_penerima" required maxlength="100"
                               value="{{ old('nama_penerima', $user->name) }}"
                               class="w-full px-3 py-2.5 rounded-lg border text-sm outline-none transition
                                      @error('nama_penerima') border-red-400 @else border-gray-300 focus:border-primary focus:ring-primary @enderror">
                        @error('nama_penerima')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="no_hp" class="block text-xs font-semibold text-gray-700 mb-1.5">Nomor HP / WhatsApp</label>
                        <input type="text" id="no_hp" name="no_hp" required maxlength="20" placeholder="08xxxxxxxxxx"
                               value="{{ old('no_hp', $user->no_hp) }}"
                               class="w-full px-3 py-2.5 rounded-lg border text-sm outline-none transition
                                      @error('no_hp') border-red-400 @else border-gray-300 focus:border-primary focus:ring-primary @enderror">
                        @error('no_hp')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="alamat" class="block text-xs font-semibold text-gray-700 mb-1.5">Alamat</label>
                        <textarea id="alamat" name="alamat" rows="3" required maxlength="500"
                                  placeholder="Jalan, nomor rumah, kelurahan, kota"
                                  class="w-full px-3 py-2.5 rounded-lg border text-sm outline-none transition resize-none
                                         @error('alamat') border-red-400 @else border-gray-300 focus:border-primary focus:ring-primary @enderror">{{ old('alamat', $user->alamat) }}</textarea>
                        @error('alamat')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <span class="block text-xs font-semibold text-gray-700 mb-2">Metode penyerahan</span>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach (['kirim' => 'Dikirim', 'ambil' => 'Ambil di toko'] as $nilai => $label)
                                @php
                                    $tersedia = $store->metode_serah === 'keduanya' || $store->metode_serah === $nilai;
                                @endphp
                                <label class="cursor-pointer {{ $tersedia ? '' : 'opacity-40 pointer-events-none' }}">
                                    <input type="radio" name="metode_serah" value="{{ $nilai }}" class="peer sr-only"
                                           @checked(old('metode_serah', $store->metode_serah === 'ambil' ? 'ambil' : 'kirim') === $nilai)
                                           @disabled(! $tersedia)>
                                    <div class="rounded-xl border-2 border-gray-200 p-3 text-center text-sm font-semibold text-gray-800 peer-checked:border-primary peer-checked:bg-primary-light transition-colors">
                                        {{ $label }}
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('metode_serah')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        <p class="mt-2 text-[11px] text-gray-500">
                            Toko ini menyediakan: {{ $store->metode_serah_label }}.
                        </p>
                    </div>

                    <div>
                        <label for="catatan" class="block text-xs font-semibold text-gray-700 mb-1.5">
                            Catatan <span class="font-normal text-gray-400">(opsional)</span>
                        </label>
                        <textarea id="catatan" name="catatan" rows="2" maxlength="500" placeholder="Contoh: mohon dipacking kardus tebal"
                                  class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary resize-none">{{ old('catatan') }}</textarea>
                    </div>

                    <button type="submit"
                            class="w-full py-3 rounded-lg bg-emerald-600 text-white text-sm font-bold shadow-sm hover:bg-emerald-700 transition-colors">
                        Buat Pesanan &amp; Lanjut ke WhatsApp
                    </button>
                </form>
            </div>

            {{-- RINGKASAN --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <h2 class="text-sm font-bold text-gray-900">Ringkasan Pesanan</h2>

                    <div class="mt-4 space-y-3">
                        @foreach ($items as $item)
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $item->plant->nama }}</p>
                                    <p class="text-xs text-gray-500">{{ $item->jumlah }} × {{ $item->plant->harga_rupiah }}</p>
                                </div>
                                <p class="text-sm font-semibold text-gray-900 whitespace-nowrap">{{ $item->subtotal_rupiah }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
                        <span class="text-sm text-gray-600">Total</span>
                        <span class="text-lg font-extrabold text-primary">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>

                    <div class="mt-5 p-3 rounded-lg bg-sky-50 border border-sky-200 text-[11px] text-sky-900 leading-relaxed">
                        Pesanan dikirim ke WhatsApp <strong>{{ $store->nama_toko }}</strong>.
                        Pembayaran dan pengiriman diatur langsung antara Anda dan penjual — TanamKu tidak memproses pembayaran.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
