@extends('layouts.app')

@section('title', 'Buat Password Baru — TanamKu')

@section('content')
    <div class="min-h-[calc(100vh-16rem)] flex items-center justify-center px-4 py-16">
        <div class="w-full max-w-md">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
                <div class="text-center">
                    <div class="w-12 h-12 mx-auto rounded-xl bg-primary-light text-primary flex items-center justify-center shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h1 class="mt-4 text-xl font-extrabold text-gray-900">Buat Password Baru</h1>
                    <p class="mt-1.5 text-xs text-gray-600 leading-relaxed">
                        Kode berhasil diverifikasi untuk <strong class="text-gray-800">{{ $email }}</strong>.<br>Silakan buat password baru Anda.
                    </p>
                </div>

                @include('partials.alert')

                <form method="POST" action="{{ route('password.update') }}" class="mt-6 space-y-4">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email }}">

                    {{-- Password Baru --}}
                    <div>
                        <label for="password" class="block text-xs font-semibold text-gray-700 mb-1.5">Password Baru</label>
                        <input type="password" id="password" name="password" required minlength="8" autofocus
                               autocomplete="new-password" placeholder="Minimal 8 karakter"
                               class="w-full px-3 py-2.5 rounded-lg border text-sm outline-none transition
                                      @error('password') border-red-400 focus:ring-red-200 @else border-gray-300 focus:border-primary focus:ring-primary @enderror">
                        @error('password')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Konfirmasi Password --}}
                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold text-gray-700 mb-1.5">Ulangi Password Baru</label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                               required minlength="8" autocomplete="new-password" placeholder="Ulangi password baru"
                               class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary transition">
                    </div>

                    <button type="submit" class="w-full py-2.5 rounded-lg bg-primary text-white text-sm font-bold shadow-sm hover:bg-primary-hover transition-colors">
                        Simpan Password Baru
                    </button>
                </form>

                <p class="mt-6 pt-5 border-t border-gray-100 text-xs text-gray-600 text-center">
                    Batal reset password?
                    <a href="{{ route('login') }}" class="font-semibold text-primary hover:underline">Kembali ke login</a>
                </p>
            </div>

            <p class="mt-6 text-center text-xs text-gray-500">
                <a href="{{ route('home') }}" class="hover:text-primary font-medium">← Kembali ke beranda</a>
            </p>
        </div>
    </div>
@endsection
