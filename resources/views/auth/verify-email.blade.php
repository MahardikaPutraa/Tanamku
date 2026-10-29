@extends('layouts.app')

@section('title', 'Verifikasi Email — TanamKu')

@section('content')
    <div class="min-h-[calc(100vh-16rem)] flex items-center justify-center px-4 py-16">
        <div class="w-full max-w-md">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
                <div class="text-center">
                    <div class="w-12 h-12 mx-auto rounded-xl {{ $user->hasVerifiedEmail() ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }} flex items-center justify-center shadow-sm">
                        @if ($user->hasVerifiedEmail())
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        @else
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        @endif
                    </div>
                    <h1 class="mt-4 text-xl font-extrabold text-gray-900">Status Verifikasi Email</h1>
                    <p class="mt-1 text-sm font-semibold text-gray-800">{{ $user->email }}</p>

                    <div class="mt-2">
                        @if ($user->hasVerifiedEmail())
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                ✓ Email Terverifikasi (Verified)
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                ⚠ Belum Verified
                            </span>
                        @endif
                    </div>
                </div>

                @include('partials.alert')

                @if ($user->hasVerifiedEmail())
                    <div class="mt-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-center">
                        <p class="text-xs text-emerald-900 leading-relaxed font-medium">
                            Akun Anda sudah memiliki email terverifikasi sejak:
                        </p>
                        <p class="text-xs font-bold text-emerald-800 mt-1">
                            {{ $user->email_verified_at->timezone('Asia/Jakarta')->translatedFormat('d F Y, H:i') }} WIB
                        </p>
                        <div class="mt-4">
                            <a href="{{ route('home') }}" class="inline-block px-4 py-2 rounded-lg bg-primary text-white text-xs font-bold hover:bg-primary-hover">
                                Kembali ke Beranda
                            </a>
                        </div>
                    </div>
                @else
                    <p class="mt-4 text-xs text-gray-600 text-center leading-relaxed">
                        Verifikasi email Anda untuk mengamankan akun dan menerima informasi pemesanan penting.
                    </p>

                    {{-- Form Kirim Kode --}}
                    <div class="mt-4 pt-4 border-t border-gray-100 text-center">
                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <button type="submit" class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-800 text-xs font-bold transition">
                                ✉ Kirim Kode Verifikasi ke Email
                            </button>
                        </form>
                    </div>

                    {{-- Form Cek Kode --}}
                    <form method="POST" action="{{ route('verification.check') }}" class="mt-5 space-y-4">
                        @csrf
                        <div>
                            <label for="otp" class="block text-xs font-semibold text-gray-700 mb-1.5 text-center">
                                Masukkan 6 Digit Kode Verifikasi Email
                            </label>
                            <input type="text" id="otp" name="otp" required maxlength="6"
                                   placeholder="123456"
                                   value="{{ old('otp') }}"
                                   class="w-full text-center tracking-[0.5em] text-xl font-mono py-3 px-4 rounded-xl border border-gray-300 text-gray-900 outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition
                                          @error('otp') border-red-400 focus:ring-red-200 @enderror">
                            @error('otp')
                                <p class="mt-1.5 text-xs text-red-600 text-center">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit"
                                class="w-full py-2.5 rounded-lg bg-primary text-white text-sm font-bold shadow-sm hover:bg-primary-hover transition-colors">
                            Verifikasi Email Sekarang
                        </button>
                    </form>
                @endif
            </div>

            <p class="mt-6 text-center text-xs text-gray-500">
                <a href="{{ route('home') }}" class="hover:text-primary font-medium">← Kembali ke beranda</a>
            </p>
        </div>
    </div>
@endsection

