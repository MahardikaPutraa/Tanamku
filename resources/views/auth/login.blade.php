@extends('layouts.app')

@section('title', 'Masuk — TanamKu')

@section('content')
    <div class="min-h-[calc(100vh-16rem)] flex items-center justify-center px-4 py-16">
        <div class="w-full max-w-md">
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-lg p-6 sm:p-8">
                <div class="text-center">
                    <div class="w-12 h-12 mx-auto rounded-2xl bg-gradient-to-br from-primary to-emerald-700 flex items-center justify-center text-white shadow-md shadow-primary/25">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                    </div>
                    <h1 class="mt-4 text-2xl font-extrabold text-slate-900 tracking-tight">Masuk ke TanamKu</h1>
                    <p class="mt-1.5 text-xs text-slate-500">Kelola toko atau pantau pesanan tanaman Anda dengan mudah.</p>
                </div>

                @include('partials.alert')

                <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
                    @csrf

                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Alamat Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                               required autofocus autocomplete="username" placeholder="nama@email.com"
                               class="w-full px-3.5 py-2.5 rounded-xl border text-sm outline-none transition
                                      @error('email') border-red-400 focus:ring-2 focus:ring-red-100 @else border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/20 @enderror">
                        @error('email')
                            <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Password</label>
                            <a href="{{ route('password.request') }}" class="text-xs font-bold text-primary hover:underline">
                                Lupa Password?
                            </a>
                        </div>
                        <input type="password" id="password" name="password" required autocomplete="current-password" placeholder="••••••••"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition">
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center gap-2 text-xs text-slate-600 select-none cursor-pointer">
                            <input type="checkbox" name="remember" value="1" class="rounded border-slate-300 text-primary focus:ring-primary">
                            <span>Ingat sesi masuk saya</span>
                        </label>
                    </div>

                    <button type="submit" class="w-full py-3 rounded-xl bg-primary text-white text-xs font-bold shadow-md shadow-primary/20 hover:bg-primary-hover hover:shadow-lg transition-all">
                        Masuk ke Akun
                    </button>
                </form>

                <div class="mt-6 pt-5 border-t border-slate-100 text-xs text-slate-500 text-center">
                    Belum punya akun TanamKu?
                    <a href="{{ route('register') }}" class="font-bold text-primary hover:underline ml-1">Daftar sekarang</a>
                </div>
            </div>

            <p class="mt-6 text-center text-xs text-slate-500">
                <a href="{{ route('home') }}" class="hover:text-primary font-semibold transition-colors">← Kembali ke Beranda Katalog</a>
            </p>
        </div>
    </div>
@endsection
