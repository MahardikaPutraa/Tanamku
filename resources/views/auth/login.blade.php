@extends('layouts.app')

@section('title', 'Masuk — TanamKu')

@section('content')
    <div class="min-h-[calc(100vh-16rem)] flex items-center justify-center px-4 py-16">
        <div class="w-full max-w-md">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
                <div class="text-center">
                    <div class="w-12 h-12 mx-auto rounded-xl bg-primary flex items-center justify-center text-white shadow-md shadow-primary/20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                    </div>
                    <h1 class="mt-4 text-xl font-extrabold text-gray-900">Masuk ke TanamKu</h1>
                    <p class="mt-1.5 text-sm text-gray-600">Masuk ke TanamKu untuk mengakses akun Anda.</p>
                </div>



                @include('partials.alert')

                <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
                    @csrf

                    <div>
                        <label for="email" class="block text-xs font-semibold text-gray-700 mb-1.5">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                               required autofocus autocomplete="username" placeholder="nama@email.com"
                               class="w-full px-3 py-2.5 rounded-lg border text-sm outline-none transition
                                      @error('email') border-red-400 focus:ring-red-200 @else border-gray-300 focus:border-primary focus:ring-primary @enderror">
                        @error('email')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-semibold text-gray-700 mb-1.5">Password</label>
                        <input type="password" id="password" name="password" required autocomplete="current-password" placeholder="••••••••"
                               class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary transition">
                    </div>

                    <label class="flex items-center gap-2 text-xs text-gray-600 select-none">
                        <input type="checkbox" name="remember" value="1" class="rounded border-gray-300 text-primary focus:ring-primary">
                        Ingat saya
                    </label>

                    <button type="submit" class="w-full py-2.5 rounded-lg bg-primary text-white text-sm font-bold shadow-sm hover:bg-primary-hover transition-colors">
                        Masuk
                    </button>
                </form>

                <p class="mt-6 pt-5 border-t border-gray-100 text-xs text-gray-600 text-center">
                    Belum punya akun?
                    <a href="{{ route('register') }}" class="font-semibold text-primary hover:underline">Daftar sekarang</a>
                </p>
            </div>

            <p class="mt-6 text-center text-xs text-gray-500">
                <a href="{{ route('home') }}" class="hover:text-primary font-medium">← Kembali ke beranda</a>
            </p>
        </div>
    </div>

    
@endsection
