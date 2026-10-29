@extends('layouts.app')

@section('title', 'Pesanan ' . $order->kode_pesanan . ' — TanamKu')

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        @include('partials.alert')

        <nav class="text-xs text-gray-500 mb-5">
            @if (auth()->user()->isBuyer())
                <a href="{{ route('orders.mine') }}" class="hover:text-primary">Pesanan Saya</a>
            @elseif (auth()->user()->isSeller())
                <a href="{{ route('seller.orders.index') }}" class="hover:text-primary">Pesanan Masuk</a>
            @else
                <a href="{{ route('admin.orders.index') }}" class="hover:text-primary">Semua Pesanan</a>
            @endif
            <span class="mx-1.5">/</span>
            <span class="text-gray-800 font-medium">{{ $order->kode_pesanan }}</span>
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Pesanan {{ $order->kode_pesanan }}</h1>
                <p class="mt-1 text-sm text-gray-600">{{ $order->created_at->format('d F Y, H:i') }}</p>
            </div>
            <span class="self-start px-3 py-1.5 rounded-full text-xs font-bold border {{ $order->status_badge }}">
                {{ $order->status_label }}
            </span>
        </div>

        {{-- Aksi WhatsApp (untuk pembeli) — tidak ditampilkan bila pesanan dibatalkan --}}
        @if (auth()->user()->isBuyer())
            @if ($order->status === 'batal')
                <div class="mt-6 bg-red-50 border border-red-200 rounded-xl p-5">
                    <p class="text-sm font-bold text-red-900">Pesanan ini dibatalkan</p>
                    <p class="mt-1 text-xs text-red-800">
                        Karena pesanan sudah dibatalkan, tidak ada lagi yang perlu dikirim ke penjual.
                        Silakan buat pesanan baru bila masih ingin membeli.
                    </p>
                    <a href="{{ route('plants.index') }}"
                       class="mt-4 inline-flex px-4 py-2 rounded-lg bg-white border border-red-300 text-xs font-semibold text-red-800 hover:bg-red-100">
                        Kembali ke katalog
                    </a>
                </div>
            @else
                <div class="mt-6 bg-emerald-50 border border-emerald-200 rounded-xl p-5">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <p class="text-sm font-bold text-emerald-900">Lanjutkan ke WhatsApp penjual</p>
                            <p class="mt-1 text-xs text-emerald-800">
                                Pesan sudah terisi otomatis. Periksa lalu tekan Kirim di WhatsApp.
                            </p>
                        </div>
                        <a href="{{ $waUrl }}" target="_blank" rel="noopener"
                           class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg bg-emerald-600 text-white text-sm font-bold hover:bg-emerald-700 shrink-0">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38a9.9 9.9 0 004.79 1.22h.01c5.46 0 9.91-4.45 9.91-9.91C21.96 6.45 17.5 2 12.04 2z"/>
                            </svg>
                            Buka WhatsApp
                        </a>
                    </div>

                    <details class="mt-4">
                        <summary class="text-xs font-semibold text-emerald-900 cursor-pointer">Lihat isi pesan</summary>
                        <pre class="mt-2 p-3 rounded-lg bg-white border border-emerald-200 text-[11px] text-gray-700 whitespace-pre-wrap font-mono">{{ $waPesan }}</pre>
                    </details>
                </div>
            @endif

            {{-- Tombol Konfirmasi Selesai oleh Pembeli --}}
            @if ($order->status === 'diproses')
                <div class="mt-4 bg-sky-50 border border-sky-200 rounded-xl p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <p class="text-sm font-bold text-sky-900">Tanaman sudah Anda terima?</p>
                        <p class="mt-1 text-xs text-sky-700">
                            Jika Anda sudah menerima pesanan dengan baik, silakan konfirmasi untuk menyelesaikan pesanan dan memberi ulasan.
                        </p>
                    </div>
                    <form method="POST" action="{{ route('orders.complete', $order) }}" class="shrink-0">
                        @csrf
                        @method('PATCH')
                        <button type="submit" onclick="return confirm('Konfirmasi bahwa pesanan ini sudah Anda terima dengan baik?')"
                                class="w-full sm:w-auto px-5 py-2.5 rounded-lg bg-primary text-white text-sm font-bold hover:bg-primary-hover transition-colors shadow-sm">
                            ✓ Pesanan Diterima & Selesai
                        </button>
                    </form>
                </div>
            @endif
        @endif

        <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Item --}}
            <div class="lg:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-bold text-gray-900">Produk</h2>
                </div>

                <div class="divide-y divide-gray-100">
                    @foreach ($order->items as $item)
                        <div class="px-5 py-4 flex items-center gap-4">
                            <div class="w-14 h-14 rounded-lg bg-gray-50 border border-gray-100 overflow-hidden shrink-0">
                                @if ($item->plant?->foto_url)
                                    <img src="{{ $item->plant->foto_url }}" alt="" class="w-full h-full object-cover">
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $item->nama_produk }}</p>
                                <p class="text-xs text-gray-500">{{ $item->jumlah }} × {{ $item->harga_satuan_rupiah }}</p>
                            </div>
                            <p class="text-sm font-bold text-gray-900 whitespace-nowrap">{{ $item->subtotal_rupiah }}</p>
                        </div>
                    @endforeach
                </div>

                <div class="px-5 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                    <span class="text-sm text-gray-600">Total</span>
                    <span class="text-lg font-extrabold text-primary">{{ $order->total_harga_rupiah }}</span>
                </div>
            </div>

            {{-- Ulasan Produk (Bila Pesanan Selesai & User adalah Pembeli) --}}
            @if ($order->status === 'selesai' && auth()->user()->isBuyer())
                <div class="lg:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-6">
                    <div>
                        <h2 class="text-base font-extrabold text-gray-900 tracking-tight">Ulasan Tanaman</h2>
                        <p class="mt-1 text-xs text-gray-500">Beri ulasan dan rating untuk tanaman yang telah Anda beli.</p>
                    </div>

                    <div class="space-y-6 divide-y divide-gray-100">
                        @foreach ($order->items as $item)
                            @php
                                $ulasan = $order->reviews->firstWhere('plant_id', $item->plant_id);
                            @endphp
                            <div class="pt-4 first:pt-0">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-10 h-10 rounded-lg bg-gray-50 border border-gray-100 overflow-hidden shrink-0">
                                        @if ($item->plant?->foto_url)
                                            <img src="{{ $item->plant->foto_url }}" alt="" class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-gray-900 truncate">{{ $item->nama_produk }}</p>
                                        <p class="text-xs text-gray-500">{{ $item->subtotal_rupiah }}</p>
                                    </div>
                                </div>

                                @if ($ulasan)
                                    <div class="p-4 rounded-lg bg-emerald-50/60 border border-emerald-100">
                                        <div class="flex items-center justify-between mb-1.5">
                                            <div class="flex items-center text-amber-400">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <svg class="w-4 h-4 {{ $i <= $ulasan->rating ? 'fill-current' : 'text-gray-200 fill-current' }}" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                @endfor
                                                <span class="text-xs font-bold text-gray-800 ml-2">Rating: {{ $ulasan->rating }} / 5</span>
                                            </div>
                                            <span class="text-[11px] font-semibold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full">✓ Sudah Diulas</span>
                                        </div>
                                        @if ($ulasan->komentar)
                                            <p class="text-xs text-gray-700 mt-1 italic">"{{ $ulasan->komentar }}"</p>
                                        @endif
                                    </div>
                                @elseif ($item->plant_id)
                                    <form method="POST" action="{{ route('reviews.store', $order) }}" class="p-4 rounded-lg bg-gray-50 border border-gray-100 space-y-3">
                                        @csrf
                                        <input type="hidden" name="plant_id" value="{{ $item->plant_id }}">

                                        <div>
                                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Rating Bintang</label>
                                            <div class="flex items-center gap-3">
                                                @foreach ([5 => '⭐⭐⭐⭐⭐ (Sangat Puas)', 4 => '⭐⭐⭐⭐ (Puas)', 3 => '⭐⭐⭐ (Cukup)', 2 => '⭐⭐ (Kurang)', 1 => '⭐ (Kecewa)'] as $val => $label)
                                                    <label class="inline-flex items-center text-xs text-gray-700 cursor-pointer">
                                                        <input type="radio" name="rating" value="{{ $val }}" @checked($loop->first) class="text-primary focus:ring-primary mr-1">
                                                        {{ $val }} ★
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-semibold text-gray-700 mb-1">Ulasan Anda (Opsional)</label>
                                            <textarea name="komentar" rows="2" placeholder="Bagikan kondisi tanaman saat sampai, kesegaran daun, dsb..."
                                                      class="w-full px-3 py-2 rounded-lg border border-gray-300 text-xs focus:border-primary focus:ring-1 focus:ring-primary outline-none"></textarea>
                                        </div>

                                        <button type="submit" class="px-4 py-2 rounded-lg bg-primary text-white text-xs font-bold hover:bg-primary-hover transition-colors shadow-sm">
                                            Kirim Ulasan
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Info --}}
            <div class="space-y-4">
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                    <h2 class="text-sm font-bold text-gray-900">Penjual</h2>
                    <p class="mt-2 text-sm font-semibold text-gray-800">{{ $order->store->nama_toko }}</p>
                    <p class="text-xs text-gray-500">{{ $order->store->kota }}</p>
                    <p class="mt-1 text-xs text-gray-500">WA: {{ $order->store->no_wa }}</p>
                    <a href="{{ route('stores.show', $order->store) }}" class="mt-3 inline-block text-xs font-semibold text-primary hover:underline">
                        Lihat toko →
                    </a>
                </div>

                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                    <h2 class="text-sm font-bold text-gray-900">
                        {{ $order->metode_serah === 'ambil' ? 'Pengambilan di Toko' : 'Pengiriman' }}
                    </h2>
                    <dl class="mt-2 space-y-1.5 text-xs">
                        <div class="flex justify-between gap-3">
                            <dt class="text-gray-500">{{ $order->metode_serah === 'ambil' ? 'Pengambil' : 'Penerima' }}</dt>
                            <dd class="text-gray-900 font-medium text-right">{{ $order->nama_penerima }}</dd>
                        </div>
                        <div class="flex justify-between gap-3">
                            <dt class="text-gray-500">No HP</dt>
                            <dd class="text-gray-900 font-medium text-right">{{ $order->no_hp }}</dd>
                        </div>
                        <div class="flex justify-between gap-3">
                            <dt class="text-gray-500">Metode</dt>
                            <dd class="text-gray-900 font-medium text-right">{{ $order->metode_serah_label }}</dd>
                        </div>
                        @if ($order->metode_serah === 'ambil')
                            <div class="pt-2 border-t border-gray-100">
                                <dt class="text-gray-500 font-semibold text-emerald-800">Alamat Toko (Lokasi Pengambilan)</dt>
                                <dd class="mt-1 text-gray-900 font-medium">{{ $order->store->nama_toko }}</dd>
                                <dd class="text-gray-600 mt-0.5">{{ $order->store->alamat }}, {{ $order->store->kota }}</dd>
                                <dd class="text-[11px] text-gray-500 mt-1">WA: {{ $order->store->no_wa }}</dd>
                            </div>
                        @else
                            <div>
                                <dt class="text-gray-500">Alamat Pengiriman</dt>
                                <dd class="mt-1 text-gray-900">{{ $order->alamat }}</dd>
                            </div>
                        @endif
                        @if ($order->catatan)
                            <div class="pt-1.5">
                                <dt class="text-gray-500">Catatan</dt>
                                <dd class="mt-1 text-gray-900">{{ $order->catatan }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
    </div>
@endsection
