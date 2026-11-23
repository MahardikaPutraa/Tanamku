@props(['plant'])

<div class="group bg-white rounded-xl sm:rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-xl hover:border-emerald-500/50 hover:-translate-y-1.5 transition-all duration-300 flex flex-col overflow-hidden relative">
    {{-- Gambar Produk --}}
    <div class="relative w-full aspect-square bg-slate-50 overflow-hidden">
        @if ($plant->foto_url)
            <img src="{{ $plant->foto_url }}" alt="{{ $plant->nama }}"
                 class="w-full h-full object-cover group-hover:scale-108 transition-transform duration-500 ease-out" loading="lazy">
            <div class="absolute inset-0 bg-gradient-to-t from-black/35 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
        @else
            <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-emerald-50/60 to-teal-50/40 text-emerald-700/60 p-3 sm:p-4 text-center">
                <span class="text-4xl sm:text-5xl group-hover:scale-110 transition-transform duration-300">🌿</span>
                <span class="text-[11px] sm:text-xs font-semibold text-slate-500 mt-2 line-clamp-1">{{ $plant->nama }}</span>
            </div>
        @endif

        {{-- Badge Promo / Tag Atas Kiri --}}
        <div class="absolute top-2.5 left-2.5 sm:top-3 sm:left-3 z-10 flex flex-col gap-1">
            <span class="px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-extrabold bg-emerald-600 text-white shadow-xs tracking-wider uppercase">
                100% Segar
            </span>
        </div>

        {{-- Badge Stok Atas Kanan --}}
        <div class="absolute top-2.5 right-2.5 sm:top-3 sm:right-3 z-10">
            @if ($plant->stok <= 0)
                <span class="px-2.5 py-0.5 rounded-full text-[10px] sm:text-xs font-extrabold bg-rose-600 text-white shadow-xs">Habis</span>
            @elseif ($plant->stok <= 4)
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] sm:text-xs font-extrabold bg-amber-500 text-white shadow-xs">
                    <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                    Sisa {{ $plant->stok }}
                </span>
            @else
                <span class="px-2 py-0.5 rounded-full text-[10px] sm:text-xs font-bold bg-slate-900/70 text-white backdrop-blur-md shadow-xs">
                    Stok {{ $plant->stok }}
                </span>
            @endif
        </div>

        {{-- Badge Kategori Bawah Kiri --}}
        @if ($plant->category)
            <div class="absolute bottom-2.5 left-2.5 sm:bottom-3 sm:left-3 z-10">
                <span class="inline-block px-2.5 py-0.5 rounded-lg text-[10px] sm:text-[11px] font-bold bg-white/90 text-slate-800 backdrop-blur-md border border-white/40 shadow-xs">
                    {{ $plant->category->nama }}
                </span>
            </div>
        @endif
    </div>

    {{-- Detail Informasi Produk --}}
    <div class="p-3.5 sm:p-4 flex flex-col flex-grow justify-between bg-white">
        <div>
            <h3 class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-primary transition-colors line-clamp-2 leading-snug">
                <a href="{{ route('plants.show', $plant) }}">{{ $plant->nama }}</a>
            </h3>

            {{-- Toko & Kota --}}
            @if ($plant->store)
                <div class="mt-1.5 flex items-center justify-between text-[11px] text-slate-500">
                    <a href="{{ route('stores.show', $plant->store) }}"
                       class="inline-flex items-center gap-1 hover:text-primary truncate font-semibold">
                        <span class="truncate">{{ $plant->store->nama_toko }}</span>
                        <span class="text-emerald-600 font-extrabold text-[10px]" title="Toko Mitra Terverifikasi">✓</span>
                    </a>
                    @if ($plant->store->kota)
                        <span class="text-slate-400 shrink-0 ml-1 text-[10px] hidden sm:inline">📍 {{ $plant->store->kota }}</span>
                    @endif
                </div>
            @endif

            {{-- Rating & Terjual --}}
            <div class="mt-1.5 flex items-center gap-1.5 text-[11px]">
                @if ($plant->rating_rata_rata)
                    <div class="flex items-center text-amber-500 font-bold">
                        <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        <span class="ml-1 text-slate-800">{{ $plant->rating_rata_rata }}</span>
                    </div>
                    <span class="text-slate-300">·</span>
                    <span class="text-slate-500 font-medium">({{ $plant->total_ulasan }} ulasan)</span>
                @else
                    <span class="text-amber-500 font-bold text-[10px]">★ Baru</span>
                    <span class="text-slate-300">·</span>
                    <span class="text-slate-400 text-[10px]">Terkurasi</span>
                @endif
            </div>
        </div>

        {{-- Harga & Tombol Aksi --}}
        <div class="mt-3.5 pt-3 border-t border-slate-100 flex items-center justify-between gap-1.5">
            <div class="min-w-0">
                <span class="text-[10px] text-slate-400 block font-semibold uppercase tracking-wider leading-none">Harga</span>
                <span class="text-sm sm:text-base font-black text-slate-900 truncate block mt-0.5 tracking-tight">{{ $plant->harga_rupiah }}</span>
            </div>

            <a href="{{ route('plants.show', $plant) }}"
               class="inline-flex items-center justify-center px-3.5 py-1.5 sm:px-4 sm:py-2 rounded-xl text-[11px] sm:text-xs font-extrabold shrink-0 transition-all active:scale-95 {{ $plant->stok <= 0 ? 'bg-slate-100 text-slate-400 cursor-not-allowed pointer-events-none' : 'bg-primary text-white hover:bg-primary-hover shadow-xs hover:shadow-md shadow-primary/20' }}">
                {{ $plant->stok <= 0 ? 'Habis' : 'Lihat' }}
            </a>
        </div>
    </div>
</div>
