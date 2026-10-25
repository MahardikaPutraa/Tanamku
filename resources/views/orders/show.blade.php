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
                                @if ($item->plant?->foto)
                                    <img src="{{ asset('storage/' . $item->plant->foto) }}" alt="" class="w-full h-full object-cover">
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
                    <h2 class="text-sm font-bold text-gray-900">Pengiriman</h2>
                    <dl class="mt-2 space-y-1.5 text-xs">
                        <div class="flex justify-between gap-3">
                            <dt class="text-gray-500">Penerima</dt>
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
                        <div>
                            <dt class="text-gray-500">Alamat</dt>
                            <dd class="mt-1 text-gray-900">{{ $order->alamat }}</dd>
                        </div>
                        @if ($order->catatan)
                            <div>
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
