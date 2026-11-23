@php
    $u = auth()->user();
    $tanpaNavbar = request()->routeIs('admin.*');
    $menu = [];

    if ($u->isAdmin()) {
        $menu = [
            [
                'label' => 'Dashboard',
                'route' => 'admin.dashboard',
                'aktif' => 'admin.dashboard',
                'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>'
            ],
            [
                'label' => 'Toko Mitra',
                'route' => 'admin.stores.index',
                'aktif' => 'admin.stores.*',
                'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>'
            ],
            [
                'label' => 'Moderasi Produk',
                'route' => 'admin.plants.index',
                'aktif' => 'admin.plants.*',
                'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>'
            ],
            [
                'label' => 'Pengguna',
                'route' => 'admin.users.index',
                'aktif' => 'admin.users.*',
                'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>'
            ],
            [
                'label' => 'Kategori',
                'route' => 'admin.categories.index',
                'aktif' => 'admin.categories.*',
                'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>'
            ],
            [
                'label' => 'Pesanan',
                'route' => 'admin.orders.index',
                'aktif' => 'admin.orders.*',
                'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>'
            ],
        ];
    } elseif ($u->isSeller()) {
        $menu = [
            [
                'label' => 'Dashboard',
                'route' => 'seller.dashboard',
                'aktif' => 'seller.dashboard',
                'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>'
            ],
            [
                'label' => 'Produk Saya',
                'route' => 'seller.plants.index',
                'aktif' => 'seller.plants.*',
                'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>'
            ],
            [
                'label' => 'Pesanan Masuk',
                'route' => 'seller.orders.index',
                'aktif' => 'seller.orders.*',
                'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>'
            ],
            [
                'label' => 'Profil Toko',
                'route' => 'seller.store.edit',
                'aktif' => 'seller.store.*',
                'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>'
            ],
        ];
    }
@endphp

<aside class="w-full md:w-64 bg-white/95 backdrop-blur-md border-b md:border-b-0 md:border-r border-slate-200/80 shrink-0 {{ $tanpaNavbar ? 'md:min-h-screen' : 'md:min-h-[calc(100vh-4rem)]' }} flex flex-col justify-between transition-all">
    {{-- Header Bar Mobile (Tampil di layar < md) --}}
    <div class="flex items-center justify-between p-3.5 sm:p-4 border-b border-slate-100 md:hidden bg-white">
        <a href="{{ $u->isAdmin() ? route('admin.dashboard') : route('seller.dashboard') }}" class="flex items-center gap-2.5 group">
            <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-primary to-emerald-700 flex items-center justify-center text-white shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                </svg>
            </div>
            <div class="leading-tight">
                <p class="text-sm font-extrabold tracking-tight text-primary">Tanam<span class="text-emerald-500">Ku</span></p>
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">{{ $u->isAdmin() ? 'Panel Admin' : 'Panel Penjual' }}</p>
            </div>
        </a>

        <button type="button" onclick="document.getElementById('sidebar-menu-mobile').classList.toggle('hidden')"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 focus:outline-none transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
            <span>Menu</span>
        </button>
    </div>

    {{-- Konten Menu Sidebar (Di Desktop selalu tampil, di Mobile collapsible via toggle) --}}
    <div id="sidebar-menu-mobile" class="hidden md:flex flex-col justify-between flex-1 p-4 space-y-6">
        <div class="space-y-5">
            @if ($tanpaNavbar)
                {{-- Brand: pengganti navbar atas di panel admin (Desktop) --}}
                <a href="{{ route('admin.dashboard') }}" class="hidden md:flex items-center gap-2.5 px-2 py-1.5 group">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-primary to-emerald-700 flex items-center justify-center text-white shadow-sm shadow-primary/20 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                    </div>
                    <div class="leading-tight">
                        <p class="text-base font-extrabold tracking-tight text-primary">Tanam<span class="text-emerald-500">Ku</span></p>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Panel Admin</p>
                    </div>
                </a>
            @endif

            {{-- Kartu Info Pengguna / Toko --}}
            <div class="p-3.5 bg-gradient-to-br from-slate-50 to-emerald-50/40 rounded-2xl border border-slate-200/80 shadow-2xs">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-primary/10 text-primary border border-primary/20">
                        {{ $u->isAdmin() ? 'Super Admin' : 'Mitra Penjual' }}
                    </span>
                    @if ($u->isSeller() && $u->store)
                        @php
                            $badgeCls = match ($u->store->status) {
                                'aktif' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                'nonaktif' => 'bg-red-50 text-red-700 border-red-200',
                                default => 'bg-amber-50 text-amber-700 border-amber-200',
                            };
                        @endphp
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $badgeCls }}">
                            {{ $u->store->status_label }}
                        </span>
                    @endif
                </div>

                <div class="text-sm font-extrabold text-slate-900 truncate">
                    {{ $u->isSeller() ? ($u->store->nama_toko ?? $u->name) : $u->name }}
                </div>
                <div class="text-[11px] text-slate-500 truncate mt-0.5">{{ $u->email }}</div>
            </div>

            {{-- Menu Navigasi --}}
            <nav class="space-y-1">
                @foreach ($menu as $item)
                    @php $isAktif = request()->routeIs($item['aktif']); @endphp
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all duration-150 {{ $isAktif ? 'bg-primary text-white shadow-xs shadow-primary/25' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80' }}">
                        <span class="{{ $isAktif ? 'text-white' : 'text-slate-400 group-hover:text-slate-600' }}">
                            {!! $item['icon'] !!}
                        </span>
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>
        </div>

        {{-- Footer Sidebar --}}
        <div class="pt-5 border-t border-slate-200/80 space-y-2">
            <a href="{{ route('home') }}"
               class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:text-primary hover:bg-primary-light/50 transition-colors">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>Ke Beranda Publik</span>
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-bold text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors">
                    <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </div>
</aside>
