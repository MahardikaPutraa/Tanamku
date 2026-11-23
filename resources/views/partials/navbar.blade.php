@php
    $u = auth()->user();
    $keranjang = $u && $u->isBuyer() ? $u->cartCount() : 0;

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

<header class="bg-white/95 backdrop-blur-md sticky top-0 z-50 border-b border-slate-200/80 shadow-xs">
    {{-- Top Utility Bar --}}
    <div class="bg-slate-900 text-slate-300 text-[11px] py-1 px-4 sm:px-6 lg:px-8 hidden sm:block">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-4">
                <span class="inline-flex items-center gap-1.5 text-emerald-400 font-bold">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    Garansi 100% Tanaman Sampai Dalam Keadaan Segar
                </span>
                <span class="text-slate-600">|</span>
                <span>Pesan Langsung via WhatsApp Mitra Petani</span>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('plants.index') }}" class="hover:text-white transition-colors">Panduan Belanja</a>
                <span class="text-slate-600">|</span>
                @guest
                    <a href="{{ route('register', ['role' => 'seller']) }}" class="text-emerald-400 hover:text-emerald-300 font-bold transition-colors">Buka Toko Gratis</a>
                @else
                    @if (auth()->user()->isSeller())
                        <a href="{{ route('seller.dashboard') }}" class="text-emerald-400 hover:text-emerald-300 font-bold transition-colors">Panel Penjual</a>
                    @endif
                @endguest
            </div>
        </div>
    </div>

    {{-- Main Navbar --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between gap-4 h-16 sm:h-20">

            {{-- Brand Logo --}}
            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-primary to-emerald-700 flex items-center justify-center text-white shadow-md shadow-primary/25 group-hover:scale-105 group-hover:rotate-3 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                    </div>
                    <div>
                        <span class="text-2xl font-black tracking-tight text-primary">
                            Tanam<span class="text-emerald-500">Ku</span>
                        </span>
                        <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-widest leading-none">Marketplace</span>
                    </div>
                </a>

                {{-- Nav Links (Desktop) --}}
                <nav class="hidden md:flex items-center gap-1 ml-4 pl-4 border-l border-slate-200">
                    @if (! $u?->isAdmin())
                        <a href="{{ route('home') }}"
                           class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('home') ? 'text-primary bg-primary-light shadow-2xs' : 'text-slate-600 hover:text-primary hover:bg-slate-100/80' }}">
                            Beranda
                        </a>
                    @endif
                    <a href="{{ route('plants.index') }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('plants.*') ? 'text-primary bg-primary-light shadow-2xs' : 'text-slate-600 hover:text-primary hover:bg-slate-100/80' }}">
                        Katalog Tanaman
                    </a>

                    @foreach ($tautan as [$label, $url, $aktif])
                        <a href="{{ $url }}"
                           class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ request()->routeIs($aktif) ? 'text-primary bg-primary-light shadow-2xs' : 'text-slate-600 hover:text-primary hover:bg-slate-100/80' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </nav>
            </div>

            {{-- Global Search Bar (Marketplace Style) --}}
            <form method="GET" action="{{ route('plants.index') }}" class="hidden lg:block flex-1 max-w-md xl:max-w-lg mx-4">
                <div class="relative flex items-center">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 pointer-events-none">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/>
                        </svg>
                    </span>
                    <input type="text" name="q" value="{{ request('q') }}"
                           placeholder="Cari monstera, sukulen, aglaonema, kaktus..."
                           class="w-full pl-10 pr-24 py-2.5 rounded-2xl border border-slate-200 bg-slate-50 text-xs text-slate-800 outline-none transition focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 shadow-2xs">
                    <button type="submit" class="absolute right-1.5 px-3 py-1.5 rounded-xl bg-primary text-white text-xs font-bold hover:bg-primary-hover shadow-2xs transition-all">
                        Cari
                    </button>
                </div>
            </form>

            {{-- Right Actions: Cart & Profile --}}
            <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                @auth
                    @if ($u->isBuyer())
                        <a href="{{ route('carts.index') }}"
                           class="relative p-2.5 rounded-2xl border border-slate-200/80 hover:border-primary/40 hover:bg-primary-light/50 transition-all text-slate-700 hover:text-primary"
                           title="Keranjang Belanja">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            @if ($keranjang > 0)
                                <span class="absolute -top-1 -right-1 min-w-[1.25rem] h-[1.25rem] px-1 rounded-full bg-rose-600 text-white text-[10px] font-black flex items-center justify-center shadow-xs">
                                    {{ $keranjang }}
                                </span>
                            @endif
                        </a>
                    @endif

                    {{-- User Profile Pill --}}
                    <div class="flex items-center gap-2.5 pl-2">
                        <div class="w-9 h-9 rounded-2xl bg-gradient-to-br from-primary-light to-emerald-100 text-primary font-black text-xs flex items-center justify-center border border-primary/20 shadow-2xs uppercase">
                            {{ mb_substr($u->name, 0, 1) }}
                        </div>
                        <div class="hidden xl:block leading-tight text-left">
                            <p class="text-xs font-bold text-slate-900 max-w-[8rem] truncate">{{ $u->name }}</p>
                            <p class="text-[10px] font-semibold text-emerald-600">{{ $u->isAdmin() ? 'Super Admin' : ($u->isSeller() ? 'Penjual' : 'Pembeli') }}</p>
                        </div>

                        <form method="POST" action="{{ route('logout') }}" class="hidden sm:inline">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 rounded-xl text-xs font-bold text-slate-500 hover:text-rose-600 hover:bg-rose-50 transition-colors">
                                Keluar
                            </button>
                        </form>
                    </div>
                @else
                    <div class="flex items-center gap-2">
                        <a href="{{ route('login') }}"
                           class="px-4 py-2 rounded-xl text-xs font-bold text-slate-700 hover:text-primary hover:bg-slate-100 transition-colors">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}"
                           class="px-4 py-2 rounded-xl bg-primary text-white text-xs font-extrabold shadow-sm hover:bg-primary-hover hover:shadow-md transition-all active:scale-95">
                            Daftar
                        </a>
                    </div>
                @endauth
            </div>

        </div>

        {{-- Mobile Quick Search Bar (Marketplace Mobile Standard) --}}
        <div class="block lg:hidden pb-3 pt-0">
            <form method="GET" action="{{ route('plants.index') }}" class="relative flex items-center">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 pointer-events-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/>
                    </svg>
                </span>
                <input type="text" name="q" value="{{ request('q') }}"
                       placeholder="Cari monstera, sukulen, aglaonema..."
                       class="w-full pl-9 pr-16 py-2 rounded-xl border border-slate-200 bg-slate-50 text-xs text-slate-800 outline-none transition focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 shadow-2xs">
                <button type="submit" class="absolute right-1 px-3 py-1 rounded-lg bg-primary text-white text-[11px] font-bold shadow-2xs">
                    Cari
                </button>
            </form>
        </div>
    </div>
</header>
