@props(['plant'])

<div class="group bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md hover:border-accent/40 transition-all duration-200 flex flex-col overflow-hidden">
    <div class="relative w-full aspect-square bg-gray-50 overflow-hidden">
        @if ($plant->foto_url)
            <img src="{{ $plant->foto_url }}" alt="{{ $plant->nama }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
        @else
            <div class="w-full h-full flex flex-col items-center justify-center bg-emerald-50/50 text-emerald-700/60 p-4 text-center">
                <svg class="w-14 h-14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                </svg>
                <span class="text-xs font-medium mt-2">{{ $plant->nama }}</span>
            </div>
        @endif

        <div class="absolute top-3 right-3">
            @if ($plant->stok <= 0)
                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-danger text-white shadow-sm">Habis</span>
            @elseif ($plant->stok <= 4)
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-500 text-white shadow-sm">
                    <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                    Sisa {{ $plant->stok }}
                </span>
            @else
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200 shadow-sm">Stok {{ $plant->stok }}</span>
            @endif
        </div>

        @if ($plant->category)
            <div class="absolute bottom-3 left-3">
                <span class="inline-block px-2.5 py-0.5 rounded-md text-[11px] font-medium bg-black/60 text-white backdrop-blur-sm">
                    {{ $plant->category->nama }}
                </span>
            </div>
        @endif
    </div>

    <div class="p-4 flex flex-col flex-grow justify-between">
        <div>
            <h3 class="text-base font-bold text-gray-900 group-hover:text-primary transition-colors line-clamp-1">
                <a href="{{ route('plants.show', $plant) }}">{{ $plant->nama }}</a>
            </h3>

            @if ($plant->store)
                <div class="mt-1 flex items-center justify-between text-[11px] text-gray-500">
                    <a href="{{ route('stores.show', $plant->store) }}"
                       class="inline-flex items-center gap-1 hover:text-primary truncate">
                        <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span class="truncate">{{ $plant->store->nama_toko }}</span>
                    </a>
                    @if ($plant->store->kota)
                        <span class="text-gray-400 shrink-0 ml-1">📍 {{ $plant->store->kota }}</span>
                    @endif
                </div>
            @endif

            @if ($plant->rating_rata_rata)
                <div class="mt-1.5 flex items-center gap-1 text-[11px] text-amber-500 font-bold">
                    <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    <span>{{ $plant->rating_rata_rata }}</span>
                    <span class="text-gray-400 font-normal">({{ $plant->total_ulasan }})</span>
                </div>
            @endif
        </div>

        <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between">
            <div>
                <span class="text-[11px] text-gray-400 block">Harga</span>
                <span class="text-base font-extrabold text-primary">{{ $plant->harga_rupiah }}</span>
            </div>

            <a href="{{ route('plants.show', $plant) }}"
               class="inline-flex items-center justify-center px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $plant->stok <= 0 ? 'bg-gray-100 text-gray-400 cursor-not-allowed pointer-events-none' : 'bg-primary text-white hover:bg-primary-hover shadow-sm hover:shadow' }}">
                {{ $plant->stok <= 0 ? 'Habis' : 'Lihat' }}
            </a>
        </div>
    </div>
</div>
