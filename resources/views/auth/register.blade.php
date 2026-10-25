@extends('layouts.app')

@section('title', 'Daftar Akun — TanamKu')

@section('content')
    <div class="min-h-[calc(100vh-16rem)] flex items-center justify-center px-4 py-16">
        <div class="w-full max-w-lg">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
                <div class="text-center">
                    <h1 class="text-xl font-extrabold text-gray-900">Daftar Akun TanamKu</h1>
                    <p class="mt-1.5 text-sm text-gray-600">Pilih peran Anda, lalu lengkapi data.</p>
                </div>

                @include('partials.alert')

                @php $roleTerpilih = old('role', request('role', 'buyer')); @endphp

                <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4">
                    @csrf

                    {{-- Pilihan peran --}}
                    <div>
                        <span class="block text-xs font-semibold text-gray-700 mb-2">Saya mendaftar sebagai</span>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="cursor-pointer">
                                <input type="radio" name="role" value="buyer" class="peer sr-only" @checked($roleTerpilih === 'buyer')>
                                <div class="rounded-xl border-2 border-gray-200 p-4 text-center peer-checked:border-primary peer-checked:bg-primary-light transition-colors">
                                    <div class="text-sm font-bold text-gray-900">Pembeli</div>
                                    <div class="mt-1 text-[11px] text-gray-500 leading-snug">Cari tanaman, pesan lewat WhatsApp penjual.</div>
                                </div>
                            </label>

                            <label class="cursor-pointer">
                                <input type="radio" name="role" value="seller" class="peer sr-only" @checked($roleTerpilih === 'seller')>
                                <div class="rounded-xl border-2 border-gray-200 p-4 text-center peer-checked:border-primary peer-checked:bg-primary-light transition-colors">
                                    <div class="text-sm font-bold text-gray-900">Penjual</div>
                                    <div class="mt-1 text-[11px] text-gray-500 leading-snug">Buka toko, unggah produk, terima pesanan.</div>
                                </div>
                            </label>
                        </div>
                        @error('role')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="name" class="block text-xs font-semibold text-gray-700 mb-1.5">Nama Lengkap</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required maxlength="100" placeholder="Nama Anda"
                               class="w-full px-3 py-2.5 rounded-lg border text-sm outline-none transition
                                      @error('name') border-red-400 @else border-gray-300 focus:border-primary focus:ring-primary @enderror">
                        @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-semibold text-gray-700 mb-1.5">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required maxlength="150" placeholder="nama@email.com"
                               class="w-full px-3 py-2.5 rounded-lg border text-sm outline-none transition
                                      @error('email') border-red-400 @else border-gray-300 focus:border-primary focus:ring-primary @enderror">
                        @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="no_hp" class="block text-xs font-semibold text-gray-700 mb-1.5">Nomor HP / WhatsApp</label>
                        <input type="text" id="no_hp" name="no_hp" value="{{ old('no_hp') }}" required maxlength="20" placeholder="08xxxxxxxxxx"
                               class="w-full px-3 py-2.5 rounded-lg border text-sm outline-none transition
                                      @error('no_hp') border-red-400 @else border-gray-300 focus:border-primary focus:ring-primary @enderror">
                        @error('no_hp')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-xs font-semibold text-gray-700 mb-1.5">Password</label>
                            <input type="password" id="password" name="password" required minlength="8" autocomplete="new-password" placeholder="Min. 8 karakter"
                                   class="w-full px-3 py-2.5 rounded-lg border text-sm outline-none transition
                                          @error('password') border-red-400 @else border-gray-300 focus:border-primary focus:ring-primary @enderror">
                            @error('password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-xs font-semibold text-gray-700 mb-1.5">Ulangi Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8" autocomplete="new-password" placeholder="Ulangi password"
                                   class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary transition">
                        </div>
                    </div>

                    <p class="text-[11px] text-gray-500">
                        Jika memilih <strong>Penjual</strong>, Anda akan diminta melengkapi profil toko setelah ini.
                        Toko tampil di katalog setelah disetujui admin.
                    </p>

                    <button type="submit" class="w-full py-2.5 rounded-lg bg-primary text-white text-sm font-bold shadow-sm hover:bg-primary-hover transition-colors">
                        Buat Akun
                    </button>
                </form>

                <p class="mt-6 pt-5 border-t border-gray-100 text-xs text-gray-600 text-center">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="font-semibold text-primary hover:underline">Masuk di sini</a>
                </p>
            </div>
        </div>
    </div>
@endsection
