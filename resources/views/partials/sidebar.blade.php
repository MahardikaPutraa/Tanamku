@php
    $u = auth()->user();
    $tanpaNavbar = request()->routeIs('admin.*');
    $menu = [];

    if ($u->isAdmin()) {
        $menu = [
            ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'aktif' => 'admin.dashboard'],
            ['label' => 'Toko', 'route' => 'admin.stores.index', 'aktif' => 'admin.stores.*'],
            ['label' => 'Produk', 'route' => 'admin.plants.index', 'aktif' => 'admin.plants.*'],
            ['label' => 'Pengguna', 'route' => 'admin.users.index', 'aktif' => 'admin.users.*'],
            ['label' => 'Kategori', 'route' => 'admin.categories.index', 'aktif' => 'admin.categories.*'],
            ['label' => 'Pesanan', 'route' => 'admin.orders.index', 'aktif' => 'admin.orders.*'],
        ];
    } elseif ($u->isSeller()) {
        $menu = [
            ['label' => 'Dashboard', 'route' => 'seller.dashboard', 'aktif' => 'seller.dashboard'],
            ['label' => 'Produk Saya', 'route' => 'seller.plants.index', 'aktif' => 'seller.plants.*'],
            ['label' => 'Pesanan Masuk', 'route' => 'seller.orders.index', 'aktif' => 'seller.orders.*'],
            ['label' => 'Profil Toko', 'route' => 'seller.store.edit', 'aktif' => 'seller.store.*'],
        ];
    }
@endphp

<aside class="w-full md:w-64 bg-white border-r border-gray-200 shrink-0 {{ $tanpaNavbar ? 'min-h-screen' : 'min-h-[calc(100vh-4rem)]' }} p-4 flex flex-col justify-between">
    <div class="space-y-6">
        @if ($tanpaNavbar)
            {{-- Brand: pengganti navbar atas di panel admin --}}
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-1 py-1 group">
                <div class="w-9 h-9 rounded-xl bg-primary flex items-center justify-center text-white shadow-sm shadow-primary/20 group-hover:scale-105 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                </div>
                <div class="leading-tight">
                    <p class="text-base font-bold tracking-tight text-primary">Tanam<span class="text-emerald-600">Ku</span></p>
                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Panel Admin</p>
                </div>
            </a>
        @endif

        <div class="px-3 py-2 bg-primary-light/60 rounded-xl border border-primary/10">
            <div class="text-xs font-semibold text-primary uppercase tracking-wider">
                {{ $u->isAdmin() ? 'Panel Admin' : 'Panel Penjual' }}
            </div>
            <div class="text-sm font-bold text-gray-800 truncate mt-0.5">
                {{ $u->isSeller() ? ($u->store->nama_toko ?? $u->name) : $u->name }}
            </div>
            <div class="text-xs text-gray-500 truncate">{{ $u->email }}</div>

            @if ($u->isSeller() && $u->store)
                @php
                    $warna = match ($u->store->status) {
                        'aktif' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                        'nonaktif' => 'bg-red-100 text-red-800 border-red-200',
                        default => 'bg-amber-100 text-amber-800 border-amber-200',
                    };
                @endphp
                <div class="mt-2">
                    <span class="inline-block px-2 py-0.5 rounded-full text-[11px] font-bold border {{ $warna }}">
                        {{ $u->store->status_label }}
                    </span>
                </div>
            @endif
        </div>

        <nav class="space-y-1.5">
            @foreach ($menu as $item)
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs($item['aktif']) ? 'bg-primary text-white shadow-sm' : 'text-gray-700 hover:bg-gray-100 hover:text-primary' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs($item['aktif']) ? 'bg-white' : 'bg-gray-300' }}"></span>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>
    </div>

    <div class="pt-6 border-t border-gray-200 space-y-2">
        <a href="{{ route('home') }}"
           class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-semibold text-gray-600 hover:text-primary hover:bg-gray-100 transition-colors">
            ← Kembali ke toko publik
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-semibold text-danger hover:bg-red-50 transition-colors">
                Keluar
            </button>
        </form>
    </div>
</aside>
