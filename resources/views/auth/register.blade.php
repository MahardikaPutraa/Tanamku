@extends('layouts.app')

@section('title', 'Daftar Akun — TanamKu')

@section('content')
    <div class="min-h-[calc(100vh-16rem)] flex items-center justify-center px-4 py-16">
        <div class="w-full max-w-lg">
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-lg p-6 sm:p-8">
                <div class="text-center">
                    <div class="w-12 h-12 mx-auto rounded-2xl bg-gradient-to-br from-primary to-emerald-700 flex items-center justify-center text-white shadow-md shadow-primary/25">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                    </div>
                    <h1 class="mt-4 text-2xl font-extrabold text-slate-900 tracking-tight">Daftar Akun TanamKu</h1>
                    <p class="mt-1.5 text-xs text-slate-500">Pilih tipe akun Anda dan lengkapi data diri di bawah.</p>
                </div>

                @include('partials.alert')

                @php $roleTerpilih = old('role', request('role', 'buyer')); @endphp

                <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4">
                    @csrf

                    {{-- Pilihan peran --}}
                    <div>
                        <span class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Saya Mendaftar Sebagai</span>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="cursor-pointer">
                                <input type="radio" name="role" value="buyer" class="peer sr-only" @checked($roleTerpilih === 'buyer')>
                                <div class="rounded-2xl border-2 border-slate-200 p-4 text-center peer-checked:border-primary peer-checked:bg-primary-light/50 transition-all shadow-2xs">
                                    <div class="text-sm font-extrabold text-slate-900 flex items-center justify-center gap-1.5">
                                        <span>🛍️</span>
                                        <span>Pembeli</span>
                                    </div>
                                    <div class="mt-1 text-[11px] text-slate-500 leading-snug">Jelajahi tanaman, checkout langsung ke WhatsApp penjual.</div>
                                </div>
                            </label>

                            <label class="cursor-pointer">
                                <input type="radio" name="role" value="seller" class="peer sr-only" @checked($roleTerpilih === 'seller')>
                                <div class="rounded-2xl border-2 border-slate-200 p-4 text-center peer-checked:border-primary peer-checked:bg-primary-light/50 transition-all shadow-2xs">
                                    <div class="text-sm font-extrabold text-slate-900 flex items-center justify-center gap-1.5">
                                        <span>🏪</span>
                                        <span>Penjual</span>
                                    </div>
                                    <div class="mt-1 text-[11px] text-slate-500 leading-snug">Buka toko, pajang tanaman hias, kelola pesanan masuk.</div>
                                </div>
                            </label>
                        </div>
                        @error('role')
                            <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required maxlength="100" placeholder="Contoh: Budi Pratama"
                               class="w-full px-3.5 py-2.5 rounded-xl border text-sm outline-none transition
                                      @error('name') border-red-400 focus:ring-2 focus:ring-red-100 @else border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/20 @enderror">
                        @error('name')<p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Alamat Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required maxlength="150" placeholder="nama@email.com"
                               class="w-full px-3.5 py-2.5 rounded-xl border text-sm outline-none transition
                                      @error('email') border-red-400 focus:ring-2 focus:ring-red-100 @else border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/20 @enderror">
                        @error('email')<p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="no_hp" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nomor HP / WhatsApp Aktif</label>
                        <input type="text" id="no_hp" name="no_hp" value="{{ old('no_hp') }}" required maxlength="20" placeholder="08xxxxxxxxxx"
                               class="w-full px-3.5 py-2.5 rounded-xl border text-sm outline-none transition
                                      @error('no_hp') border-red-400 focus:ring-2 focus:ring-red-100 @else border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/20 @enderror">
                        @error('no_hp')<p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Password</label>
                            <input type="password" id="password" name="password" required minlength="8" autocomplete="new-password" placeholder="Min. 8 karakter"
                                   class="w-full px-3.5 py-2.5 rounded-xl border text-sm outline-none transition
                                          @error('password') border-red-400 focus:ring-2 focus:ring-red-100 @else border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/20 @enderror">
                            @error('password')<p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Ulangi Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8" autocomplete="new-password" placeholder="Ketik ulang password"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition">
                        </div>
                    </div>

                    <button type="submit" class="w-full py-3 rounded-xl bg-primary text-white text-xs font-bold shadow-md shadow-primary/20 hover:bg-primary-hover hover:shadow-lg transition-all mt-2">
                        Daftar Akun Baru
                    </button>
                </form>

                <div class="mt-6 pt-5 border-t border-slate-100 text-xs text-slate-500 text-center">
                    Sudah punya akun TanamKu?
                    <a href="{{ route('login') }}" class="font-bold text-primary hover:underline ml-1">Masuk sekarang</a>
                </div>
            </div>

            <p class="mt-6 text-center text-xs text-slate-500">
                <a href="{{ route('home') }}" class="hover:text-primary font-semibold transition-colors">← Kembali ke Beranda Katalog</a>
            </p>
        </div>
    </div>
@endsection
