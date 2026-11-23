@extends('layouts.app')

@section('title', 'Pesanan ' . $order->kode_pesanan . ' — TanamKu')

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        @include('partials.alert')

        <nav class="text-xs text-slate-400 mb-5 font-medium flex items-center gap-1.5">
            @if (auth()->user()->isBuyer())
                <a href="{{ route('orders.mine') }}" class="hover:text-primary transition-colors">Pesanan Saya</a>
            @elseif (auth()->user()->isSeller())
                <a href="{{ route('seller.orders.index') }}" class="hover:text-primary transition-colors">Pesanan Masuk</a>
            @else
                <a href="{{ route('admin.orders.index') }}" class="hover:text-primary transition-colors">Semua Pesanan</a>
            @endif
            <span>/</span>
            <span class="text-slate-700 font-bold font-mono">{{ $order->kode_pesanan }}</span>
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Pesanan {{ $order->kode_pesanan }}</h1>
                <p class="mt-1 text-sm text-slate-500">{{ $order->created_at->format('d F Y, H:i') }}</p>
            </div>
            <span class="self-start px-3.5 py-1.5 rounded-full text-xs font-extrabold border {{ $order->status_badge }} shadow-2xs">
                {{ $order->status_label }}
            </span>
        </div>

        {{-- Aksi WhatsApp (untuk pembeli) — tidak ditampilkan bila pesanan dibatalkan --}}
        @if (auth()->user()->isBuyer())
            @if ($order->status === 'batal')
                <div class="mt-6 bg-red-50/80 border border-red-200/90 rounded-2xl p-5 shadow-xs">
                    <p class="text-sm font-bold text-red-900">Pesanan ini dibatalkan</p>
                    <p class="mt-1 text-xs text-red-800 leading-relaxed">
                        Karena pesanan sudah dibatalkan, tidak ada lagi yang perlu dikirim ke penjual.
                        Silakan buat pesanan baru bila masih ingin membeli tanaman.
                    </p>
                    <a href="{{ route('plants.index') }}"
                       class="mt-4 inline-flex px-4 py-2 rounded-xl bg-white border border-red-300 text-xs font-bold text-red-800 hover:bg-red-50 transition-colors shadow-2xs">
                        Kembali ke Katalog
                    </a>
                </div>
            @else
                <div class="mt-6 bg-gradient-to-br from-emerald-50 to-teal-50/50 border border-emerald-200/90 rounded-2xl p-5 sm:p-6 shadow-xs">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                <p class="text-sm font-bold text-emerald-950">Lanjutkan ke WhatsApp Penjual</p>
                            </div>
                            <p class="mt-1 text-xs text-emerald-800 leading-relaxed">
                                Pesan rincian pesanan sudah tersusun otomatis. Klik tombol di samping untuk langsung terhubung dengan toko.
                            </p>
                        </div>
                        <a href="{{ $waUrl }}" target="_blank" rel="noopener"
                           class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-emerald-600 text-white text-sm font-extrabold hover:bg-emerald-700 hover:scale-[1.02] active:scale-95 shrink-0 shadow-md shadow-emerald-600/20 transition-all">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38a9.9 9.9 0 004.79 1.22h.01c5.46 0 9.91-4.45 9.91-9.91C21.96 6.45 17.5 2 12.04 2z"/>
                            </svg>
                            <span>Buka WhatsApp</span>
                        </a>
                    </div>

                    <details class="mt-4 pt-3 border-t border-emerald-200/60">
                        <summary class="text-xs font-bold text-emerald-900 cursor-pointer hover:underline">Lihat draf pesan otomatis</summary>
                        <pre class="mt-2.5 p-3.5 rounded-xl bg-white/90 border border-emerald-200 text-[11px] text-slate-700 whitespace-pre-wrap font-mono shadow-2xs leading-relaxed">{{ $waPesan }}</pre>
                    </details>
                </div>
            @endif

            {{-- Tombol Konfirmasi Selesai oleh Pembeli --}}
            @if ($order->status === 'diproses')
                <div class="mt-4 bg-sky-50/80 border border-sky-200/90 rounded-2xl p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 shadow-xs">
                    <div>
                        <p class="text-sm font-bold text-sky-950">Tanaman sudah Anda terima dengan baik?</p>
                        <p class="mt-1 text-xs text-sky-700 leading-relaxed">
                            Jika Anda sudah menerima pesanan dengan baik, silakan konfirmasi untuk menyelesaikan transaksi dan memberi rating & ulasan.
                        </p>
                    </div>
                    <form method="POST" action="{{ route('orders.complete', $order) }}" class="shrink-0">
                        @csrf
                        @method('PATCH')
                        <button type="submit" onclick="return confirm('Konfirmasi bahwa pesanan ini sudah Anda terima dengan baik?')"
                                class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-primary text-white text-xs font-bold hover:bg-primary-hover transition-all shadow-xs hover:shadow">
                            ✓ Pesanan Diterima & Selesai
                        </button>
                    </form>
                </div>
            @endif
        @endif

        <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Item Produk --}}
            <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-sm font-bold text-slate-900">Rincian Produk</h2>
                    <span class="text-xs text-slate-500 font-semibold">{{ $order->items->count() }} item</span>
                </div>

                <div class="divide-y divide-slate-100">
                    @foreach ($order->items as $item)
                        <div class="px-5 py-4 flex items-center gap-4 hover:bg-slate-50/50 transition-colors">
                            <div class="w-14 h-14 rounded-xl bg-slate-50 border border-slate-200/80 overflow-hidden shrink-0 flex items-center justify-center">
                                @if ($item->plant?->foto_url)
                                    <img src="{{ $item->plant->foto_url }}" alt="" class="w-full h-full object-cover">
                                @else
                                    <span class="text-2xl">🌿</span>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold text-slate-900 truncate">{{ $item->nama_produk }}</p>
                                <p class="text-[11px] text-slate-500 mt-0.5">{{ $item->jumlah }} × {{ $item->harga_satuan_rupiah }}</p>
                            </div>
                            <p class="text-sm font-extrabold text-slate-900 whitespace-nowrap">{{ $item->subtotal_rupiah }}</p>
                        </div>
                    @endforeach
                </div>

                <div class="px-5 py-4 bg-slate-50/80 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Total Pembayaran</span>
                    <span class="text-lg font-extrabold text-primary">{{ $order->total_harga_rupiah }}</span>
                </div>
            </div>

            {{-- Ulasan Produk (Bila Pesanan Selesai & User adalah Pembeli) --}}
            @if ($order->status === 'selesai' && auth()->user()->isBuyer())
                <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 sm:p-6 space-y-6">
                    <div class="pb-3 border-b border-slate-100">
                        <h2 class="text-base font-extrabold text-slate-900 tracking-tight">Ulasan Tanaman</h2>
                        <p class="mt-1 text-xs text-slate-500">Beri ulasan dan rating untuk tanaman yang telah Anda beli.</p>
                    </div>

                    <div class="space-y-6 divide-y divide-slate-100">
                        @foreach ($order->items as $item)
                            @php
                                $ulasan = $order->reviews->firstWhere('plant_id', $item->plant_id);
                            @endphp
                            <div class="pt-4 first:pt-0">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-10 h-10 rounded-xl bg-slate-100 border border-slate-200/80 overflow-hidden shrink-0 flex items-center justify-center">
                                        @if ($item->plant?->foto_url)
                                            <img src="{{ $item->plant->foto_url }}" alt="" class="w-full h-full object-cover">
                                        @else
                                            <span class="text-base">🌿</span>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-slate-900 truncate">{{ $item->nama_produk }}</p>
                                        <p class="text-[11px] text-slate-500 font-semibold">{{ $item->subtotal_rupiah }}</p>
                                    </div>
                                </div>

                                @if ($ulasan)
                                    <div class="p-4 rounded-xl bg-emerald-50/70 border border-emerald-200/80">
                                        <div class="flex items-center justify-between mb-1.5">
                                            <div class="flex items-center text-amber-400">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <svg class="w-4 h-4 {{ $i <= $ulasan->rating ? 'fill-current' : 'text-slate-200 fill-current' }}" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                @endfor
                                                <span class="text-xs font-bold text-slate-800 ml-2">Rating: {{ $ulasan->rating }} / 5</span>
                                            </div>
                                            <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2.5 py-0.5 rounded-full border border-emerald-200">✓ Sudah Diulas</span>
                                        </div>
                                        @if ($ulasan->komentar)
                                            <p class="text-xs text-slate-700 mt-2 italic bg-white/70 p-2.5 rounded-lg border border-emerald-100">"{{ $ulasan->komentar }}"</p>
                                        @endif
                                    </div>
                                @elseif ($item->plant_id)
                                    <form method="POST" action="{{ route('reviews.store', $order) }}" class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-3">
                                        @csrf
                                        <input type="hidden" name="plant_id" value="{{ $item->plant_id }}">

                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Rating Bintang</label>
                                            <div class="flex flex-wrap items-center gap-3">
                                                @foreach ([5 => '⭐⭐⭐⭐⭐ (Sangat Puas)', 4 => '⭐⭐⭐⭐ (Puas)', 3 => '⭐⭐⭐ (Cukup)', 2 => '⭐⭐ (Kurang)', 1 => '⭐ (Kecewa)'] as $val => $label)
                                                    <label class="inline-flex items-center text-xs text-slate-700 cursor-pointer font-medium">
                                                        <input type="radio" name="rating" value="{{ $val }}" @checked($loop->first) class="text-primary focus:ring-primary mr-1.5">
                                                        {{ $val }} ★
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Ulasan Anda (Opsional)</label>
                                            <textarea name="komentar" rows="2" placeholder="Bagikan kondisi kesegaran tanaman, packing aman, dsb..."
                                                      class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition bg-white resize-none"></textarea>
                                        </div>

                                        <button type="submit" class="px-4 py-2 rounded-xl bg-primary text-white text-xs font-bold hover:bg-primary-hover shadow-2xs transition-all">
                                            Kirim Ulasan
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Info Toko & Pengiriman --}}
            <div class="space-y-4">
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5">
                    <div class="flex items-center gap-2 mb-3 pb-2 border-b border-slate-100">
                        <span class="w-2 h-2 rounded-full bg-primary"></span>
                        <h2 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Toko Penjual</h2>
                    </div>
                    <p class="text-sm font-bold text-slate-900">{{ $order->store->nama_toko }}</p>
                    <p class="text-xs text-slate-500 mt-0.5">📍 {{ $order->store->kota }}</p>
                    <p class="text-xs text-slate-500 font-mono mt-1">WA: {{ $order->store->no_wa }}</p>
                    <a href="{{ route('stores.show', $order->store) }}" class="mt-3.5 inline-flex items-center gap-1 text-xs font-bold text-primary hover:underline">
                        Kunjungi Halaman Toko →
                    </a>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5">
                    <div class="flex items-center gap-2 mb-3 pb-2 border-b border-slate-100">
                        <span class="w-2 h-2 rounded-full bg-primary"></span>
                        <h2 class="text-xs font-bold text-slate-500 uppercase tracking-wider">
                            {{ $order->metode_serah === 'ambil' ? 'Pengambilan di Toko' : 'Informasi Pengiriman' }}
                        </h2>
                    </div>
                    <dl class="space-y-2 text-xs">
                        <div class="flex justify-between gap-3">
                            <dt class="text-slate-400 font-medium">{{ $order->metode_serah === 'ambil' ? 'Nama Pengambil' : 'Penerima' }}</dt>
                            <dd class="text-slate-900 font-bold text-right">{{ $order->nama_penerima }}</dd>
                        </div>
                        <div class="flex justify-between gap-3">
                            <dt class="text-slate-400 font-medium">No. WhatsApp</dt>
                            <dd class="text-slate-900 font-mono text-right">{{ $order->no_hp }}</dd>
                        </div>
                        <div class="flex justify-between gap-3">
                            <dt class="text-slate-400 font-medium">Metode Penyerahan</dt>
                            <dd class="text-slate-900 font-bold text-right">{{ $order->metode_serah_label }}</dd>
                        </div>
                        @if ($order->metode_serah === 'ambil')
                            <div class="pt-2 border-t border-slate-100">
                                <dt class="font-bold text-emerald-800">Alamat Toko (Lokasi Pengambilan):</dt>
                                <dd class="mt-1 text-slate-900 font-bold">{{ $order->store->nama_toko }}</dd>
                                <dd class="text-slate-600 mt-0.5 leading-relaxed">{{ $order->store->alamat }}, {{ $order->store->kota }}</dd>
                            </div>
                        @else
                            <div class="pt-2 border-t border-slate-100">
                                <dt class="text-slate-400 font-medium">Alamat Tujuan Pengiriman:</dt>
                                <dd class="mt-1 text-slate-900 leading-relaxed font-medium">{{ $order->alamat }}</dd>
                            </div>
                        @endif
                        @if ($order->catatan)
                            <div class="pt-2 border-t border-slate-100">
                                <dt class="text-slate-400 font-medium">Catatan Khusus:</dt>
                                <dd class="mt-1 text-slate-800 italic bg-slate-50 p-2 rounded-lg border border-slate-100 leading-relaxed">"{{ $order->catatan }}"</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
    </div>
@endsection
