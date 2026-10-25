@php
    $u = auth()->user();
@endphp

<footer class="bg-white border-t border-gray-200 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            {{-- Identitas platform --}}
            <div class="md:col-span-2 space-y-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-primary flex items-center justify-center text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                    </div>
                    <span class="text-xl font-bold tracking-tight text-primary">Tanam<span class="text-emerald-600">Ku</span></span>
                </div>

                <p class="text-gray-600 text-sm max-w-md leading-relaxed">
                    TanamKu adalah <strong class="text-gray-800">platform marketplace</strong> yang mempertemukan
                    pembeli dengan penjual tanaman hias dari berbagai kota. Kami menyediakan katalog, panduan
                    perawatan, dan sarana pemesanan — bukan menjual tanaman secara langsung.
                </p>

                <p class="text-xs text-gray-500 italic">
                    "Tanaman sehat, rumah lebih hidup."
                </p>
            </div>

            {{-- Navigasi --}}
            <div>
                <h4 class="text-sm font-bold text-gray-900 tracking-wider uppercase mb-4">Jelajahi</h4>
                <ul class="space-y-2.5 text-sm">
                    <li>
                        <a href="{{ route('home') }}" class="text-gray-600 hover:text-primary transition-colors">Beranda</a>
                    </li>
                    <li>
                        <a href="{{ route('plants.index') }}" class="text-gray-600 hover:text-primary transition-colors">Katalog Tanaman</a>
                    </li>
                    <li>
                        <a href="{{ route('register', ['role' => 'seller']) }}" class="text-gray-600 hover:text-primary transition-colors">Buka Toko</a>
                    </li>
                </ul>
            </div>

            {{-- Akun --}}
            <div>
                <h4 class="text-sm font-bold text-gray-900 tracking-wider uppercase mb-4">Akun</h4>
                <ul class="space-y-2.5 text-sm">
                    @auth
                        @if ($u->isBuyer())
                            <li><a href="{{ route('carts.index') }}" class="text-gray-600 hover:text-primary transition-colors">Keranjang</a></li>
                            <li><a href="{{ route('orders.mine') }}" class="text-gray-600 hover:text-primary transition-colors">Pesanan Saya</a></li>
                        @elseif ($u->isSeller())
                            <li><a href="{{ route('seller.dashboard') }}" class="text-gray-600 hover:text-primary transition-colors">Dashboard Toko</a></li>
                            <li><a href="{{ route('seller.orders.index') }}" class="text-gray-600 hover:text-primary transition-colors">Pesanan Masuk</a></li>
                        @else
                            <li><a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-primary transition-colors">Dashboard Admin</a></li>
                            <li><a href="{{ route('admin.stores.index') }}" class="text-gray-600 hover:text-primary transition-colors">Verifikasi Toko</a></li>
                        @endif
                        <li><span class="text-gray-500">{{ $u->name }} · {{ $u->role_label }}</span></li>
                    @else
                        <li><a href="{{ route('login') }}" class="text-gray-600 hover:text-primary transition-colors">Masuk</a></li>
                        <li><a href="{{ route('register') }}" class="text-gray-600 hover:text-primary transition-colors">Daftar sebagai Pembeli</a></li>
                        <li><a href="{{ route('register', ['role' => 'seller']) }}" class="text-gray-600 hover:text-primary transition-colors">Daftar sebagai Penjual</a></li>
                    @endauth
                </ul>
            </div>
        </div>

        {{-- Keterangan pihak ketiga --}}
        <div class="mt-10 rounded-xl bg-gray-50 border border-gray-200 px-5 py-4">
            <p class="text-xs text-gray-600 leading-relaxed">
                <strong class="text-gray-800">TanamKu adalah pihak ketiga.</strong>
                Kami tidak memiliki produk yang dijual, tidak memproses pembayaran, dan tidak menangani pengiriman.
                Harga, ketersediaan stok, serta panduan perawatan dikelola oleh masing-masing penjual. Pembayaran dan
                pengiriman disepakati langsung antara pembeli dan penjual melalui WhatsApp.
            </p>
        </div>

        <div class="border-t border-gray-100 mt-8 pt-6 flex flex-col sm:flex-row justify-between items-center gap-2 text-xs text-gray-500">
            <p>© {{ date('Y') }} TanamKu — Platform marketplace tanaman hias.</p>
            <p class="font-medium text-gray-600">Tugas UTS Pemrograman Web Enterprise · Teknik Informatika S1</p>
        </div>
    </div>
</footer>
