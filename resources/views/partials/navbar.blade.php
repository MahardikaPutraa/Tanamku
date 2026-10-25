@php
    $u = auth()->user();
    $keranjang = $u && $u->isBuyer() ? $u->cartCount() : 0;

    /*
     | Tautan navigasi sesuai peran.
     | Semua tautan panel (termasuk dashboard) ada di sini saja —
     | tidak diulang di sisi kanan agar tidak ada tombol ganda.
     */
    $tautan = [];

    if ($u) {
        if ($u->isBuyer()) {
            $tautan[] = ['Pesanan Saya', route('orders.mine'), 'orders.mine'];
        } elseif ($u->isSeller()) {
            $tautan[] = ['Dashboard', route('seller.dashboard'), 'seller.dashboard'];
            $tautan[] = ['Produk', route('seller.plants.index'), 'seller.plants.*'];
            $tautan[] = ['Pesanan', route('seller.orders.index'), 'seller.orders.*'];
            $tautan[] = [
                'Toko',
                $u->store ? route('stores.show', $u->store) : route('seller.store.edit'),
                'stores.show',
            ];
        } else {
            $tautan[] = ['Dashboard', route('admin.dashboard'), 'admin.dashboard'];
            $tautan[] = ['Toko', route('admin.stores.index'), 'admin.stores.*'];
            $tautan[] = ['Pengguna', route('admin.users.index'), 'admin.users.*'];
        }
    }
@endphp

<nav class="bg-white/90 backdrop-blur-md sticky top-0 z-50 border-b border-gray-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between gap-4 h-16">

            {{-- ================= KIRI: logo + tautan ================= --}}
            <div class="flex items-center gap-2 min-w-0">
                <a href="{{ route('home') }}" class="flex items-center gap-2 group shrink-0">
                    <div class="w-10 h-10 rounded-xl bg-primary flex items-center justify-center text-white shadow-md shadow-primary/20 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                    </div>
                    <span class="text-xl font-bold tracking-tight text-primary hidden sm:inline">
                        Tanam<span class="text-emerald-600 font-extrabold">Ku</span>
                    </span>
                </a>

                <div class="hidden md:flex items-center gap-1 ml-4">
                    @if (! $u?->isAdmin())
                        <a href="{{ route('home') }}"
                           class="px-3 py-2 rounded-lg text-sm font-semibold transition-colors {{ request()->routeIs('home') ? 'text-primary bg-primary-light' : 'text-gray-600 hover:text-primary hover:bg-gray-50' }}">
                            Beranda
                        </a>
                    @endif
                    <a href="{{ route('plants.index') }}"
                       class="px-3 py-2 rounded-lg text-sm font-semibold transition-colors {{ request()->routeIs('plants.*') ? 'text-primary bg-primary-light' : 'text-gray-600 hover:text-primary hover:bg-gray-50' }}">
                        Katalog
                    </a>

                    @foreach ($tautan as [$label, $url, $aktif])
                        <a href="{{ $url }}"
                           class="px-3 py-2 rounded-lg text-sm font-semibold transition-colors whitespace-nowrap {{ request()->routeIs($aktif) ? 'text-primary bg-primary-light' : 'text-gray-600 hover:text-primary hover:bg-gray-50' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- ================= TENGAH: pencarian ================= --}}
            <form method="GET" action="{{ route('plants.index') }}" class="hidden lg:block flex-1 max-w-xs xl:max-w-sm">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 pointer-events-none">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/>
                        </svg>
                    </span>
                    <input type="text" name="q" value="{{ request('q') }}"
                           placeholder="Cari tanaman…"
                           class="w-full pl-9 pr-3 py-2 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-800 outline-none transition focus:bg-white focus:border-primary focus:ring-1 focus:ring-primary">
                </div>
            </form>

            {{-- ================= KANAN: keranjang + akun ================= --}}
            <div class="hidden md:flex items-center gap-2 shrink-0">
                @auth
                    @if ($u->isBuyer())
                        <a href="{{ route('carts.index') }}"
                           class="relative p-2 rounded-lg transition-colors {{ request()->routeIs('carts.*') ? 'text-primary bg-primary-light' : 'text-gray-600 hover:text-primary hover:bg-gray-50' }}"
                           title="Keranjang">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            @if ($keranjang > 0)
                                <span class="absolute -top-0.5 -right-0.5 min-w-[1.15rem] h-[1.15rem] px-1 rounded-full bg-danger text-white text-[10px] font-bold flex items-center justify-center">
                                    {{ $keranjang }}
                                </span>
                            @endif
                        </a>
                    @endif

                    <div class="flex items-center gap-2 pl-2 border-l border-gray-200">
                        <div class="w-8 h-8 rounded-full bg-primary-light text-primary text-xs font-bold flex items-center justify-center uppercase">
                            {{ mb_substr($u->name, 0, 1) }}
                        </div>
                        <div class="leading-tight hidden xl:block">
                            <p class="text-xs font-semibold text-gray-800 max-w-[7rem] truncate">{{ $u->name }}</p>
                            <p class="text-[10px] text-gray-500">{{ $u->role_label }}</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="p-2 rounded-lg text-gray-500 hover:text-danger hover:bg-red-50 transition-colors" title="Keluar">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                        </button>
                    </form>
                @endauth

                @guest
                    <a href="{{ route('login') }}" class="px-3.5 py-2 rounded-lg text-sm font-semibold text-gray-700 hover:text-primary hover:bg-gray-50 transition-colors">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold text-white bg-primary hover:bg-primary-hover shadow-sm transition-colors">
                        Daftar
                    </a>
                @endguest
            </div>

            {{-- Tombol mobile --}}
            <div class="flex items-center md:hidden">
                <button type="button" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')"
                        class="p-2 rounded-lg text-gray-600 hover:text-primary hover:bg-gray-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- ==================== MENU MOBILE ==================== --}}
    <div id="mobile-menu" class="hidden md:hidden border-t border-gray-100 bg-white px-4 pt-3 pb-4 space-y-1">
        <form method="GET" action="{{ route('plants.index') }}" class="pb-2">
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 pointer-events-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/>
                    </svg>
                </span>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari tanaman…"
                       class="w-full pl-9 pr-3 py-2 rounded-lg border border-gray-200 bg-gray-50 text-sm outline-none focus:bg-white focus:border-primary">
            </div>
        </form>

        @if (! $u?->isAdmin())
            <a href="{{ route('home') }}" class="block px-3 py-2 rounded-lg text-base font-medium {{ request()->routeIs('home') ? 'text-primary bg-primary-light' : 'text-gray-700 hover:bg-gray-50' }}">Beranda</a>
        @endif
        <a href="{{ route('plants.index') }}" class="block px-3 py-2 rounded-lg text-base font-medium {{ request()->routeIs('plants.*') ? 'text-primary bg-primary-light' : 'text-gray-700 hover:bg-gray-50' }}">Katalog</a>

        @auth
            @foreach ($tautan as [$label, $url, $aktif])
                <a href="{{ $url }}"
                   class="block px-3 py-2 rounded-lg text-base font-medium {{ request()->routeIs($aktif) ? 'text-primary bg-primary-light' : 'text-gray-700 hover:bg-gray-50' }}">
                    {{ $label }}
                </a>
            @endforeach

            @if ($u->isBuyer())
                <a href="{{ route('carts.index') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-gray-700 hover:bg-gray-50">
                    Keranjang {{ $keranjang > 0 ? '(' . $keranjang . ')' : '' }}
                </a>
            @endif

            <div class="pt-3 mt-1 border-t border-gray-100">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 rounded-lg text-base font-medium text-danger hover:bg-red-50">
                        Keluar ({{ $u->name }})
                    </button>
                </form>
            </div>
        @else
            <div class="pt-3 border-t border-gray-100 flex gap-2">
                <a href="{{ route('login') }}" class="flex-1 text-center px-4 py-2 rounded-lg text-sm font-semibold text-gray-700 bg-gray-100">Masuk</a>
                <a href="{{ route('register') }}" class="flex-1 text-center px-4 py-2 rounded-lg text-sm font-semibold text-white bg-primary">Daftar</a>
            </div>
        @endguest
    </div>
</nav>
