@extends('layouts.app')

@section('title', 'Verifikasi Kode Reset Password — TanamKu')

@section('content')
    <div class="min-h-[calc(100vh-16rem)] flex items-center justify-center px-4 py-16">
        <div class="w-full max-w-md">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
                <div class="text-center">
                    <div class="w-12 h-12 mx-auto rounded-xl bg-primary-light text-primary flex items-center justify-center shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h1 class="mt-4 text-xl font-extrabold text-gray-900">Verifikasi Kode</h1>
                    <p class="mt-1.5 text-xs text-gray-600 leading-relaxed">
                        Kami telah mengirimkan 6 digit kode verifikasi ke email:
                    </p>
                    <p class="mt-1 text-sm font-bold text-gray-800">
                        {{ $email }}
                    </p>
                </div>

                @include('partials.alert')

                <form method="POST" action="{{ route('password.otp.verify') }}" class="mt-6 space-y-4">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email }}">

                    <div>
                        <label for="otp" class="block text-xs font-semibold text-gray-700 mb-1.5 text-center">
                            Masukkan 6 Digit Kode dari Email
                        </label>
                        <input type="text" id="otp" name="otp" value="{{ old('otp') }}"
                               required maxlength="6" minlength="6" inputmode="numeric" autofocus
                               autocomplete="one-time-code" placeholder="1 2 3 4 5 6"
                               class="w-full text-center tracking-[0.5em] text-2xl font-mono py-3 px-4 rounded-xl border border-gray-300 text-gray-900 outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition
                                      @error('otp') border-red-400 focus:ring-red-200 @enderror">
                        @error('otp')
                            <p class="mt-1.5 text-xs text-red-600 text-center">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full py-3 rounded-lg bg-primary text-white text-sm font-bold shadow-sm hover:bg-primary-hover transition-colors">
                        Verifikasi Kode
                    </button>
                </form>

                <div class="mt-5 pt-4 border-t border-gray-100 flex items-center justify-between text-xs">
                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf
                        <input type="hidden" name="email" value="{{ $email }}">
                        <button type="submit" class="font-semibold text-primary hover:underline">
                            ↻ Kirim Ulang Kode
                        </button>
                    </form>

                    <a href="{{ route('password.request') }}" class="text-gray-500 hover:text-gray-800">
                        Ganti Email
                    </a>
                </div>
            </div>

            <p class="mt-6 text-center text-xs text-gray-500">
                <a href="{{ route('login') }}" class="hover:text-primary font-medium">← Kembali ke Login</a>
            </p>
        </div>
    </div>
@endsection

