@extends('layouts.app')

@section('title', 'Lupa Password — TanamKu')

@section('content')
    <div class="min-h-[calc(100vh-16rem)] flex items-center justify-center px-4 py-16">
        <div class="w-full max-w-md">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
                <div class="text-center">
                    <div class="w-12 h-12 mx-auto rounded-xl bg-primary-light text-primary flex items-center justify-center shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                    </div>
                    <h1 class="mt-4 text-xl font-extrabold text-gray-900">Lupa Password?</h1>
                    <p class="mt-1.5 text-xs text-gray-600 leading-relaxed">
                        Masukkan email yang terdaftar. Kami akan mengirimkan kode verifikasi 6 digit ke email Anda.
                    </p>
                </div>

                @include('partials.alert')

                <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-4">
                    @csrf

                    <div>
                        <label for="email" class="block text-xs font-semibold text-gray-700 mb-1.5">Email Terdaftar</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                               required autofocus placeholder="nama@email.com"
                               class="w-full px-3 py-2.5 rounded-lg border text-sm outline-none transition
                                      @error('email') border-red-400 focus:ring-red-200 @else border-gray-300 focus:border-primary focus:ring-primary @enderror">
                        @error('email')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full py-2.5 rounded-lg bg-primary text-white text-sm font-bold shadow-sm hover:bg-primary-hover transition-colors">
                        Kirim Kode Verifikasi ke Email
                    </button>
                </form>

                <p class="mt-6 pt-5 border-t border-gray-100 text-xs text-gray-600 text-center">
                    Ingat password Anda?
                    <a href="{{ route('login') }}" class="font-semibold text-primary hover:underline">Masuk kembali</a>
                </p>
            </div>

            <p class="mt-6 text-center text-xs text-gray-500">
                <a href="{{ route('home') }}" class="hover:text-primary font-medium">← Kembali ke beranda</a>
            </p>
        </div>
    </div>
@endsection
