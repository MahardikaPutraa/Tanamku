@extends('layouts.app')

@php $isEdit = (bool) $store; @endphp

@section('title', $isEdit ? 'Profil Toko' : 'Buka Toko')

@section('content')
    <div class="flex">
        @include('partials.sidebar')

        <div class="flex-1 min-w-0 p-5 lg:p-8">
            @include('partials.alert')

            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">
                {{ $isEdit ? 'Profil Toko' : 'Buka Toko Anda' }}
            </h1>
            <p class="mt-1 text-sm text-gray-600">
                {{ $isEdit
                    ? 'Perbarui informasi toko yang tampil di halaman publik.'
                    : 'Lengkapi data toko. Setelah disimpan, admin akan memeriksa sebelum toko tampil di katalog.' }}
            </p>

            @if ($isEdit)
                <div class="mt-4 p-3 rounded-lg border text-xs
                    {{ $store->status === 'aktif' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : ($store->status === 'menunggu' ? 'bg-amber-50 border-amber-200 text-amber-900' : 'bg-red-50 border-red-200 text-red-800') }}">
                    Status toko: <strong>{{ $store->status_label }}</strong>
                    @if ($store->status === 'menunggu')
                        — produk Anda belum tampil di katalog sampai disetujui admin.
                    @endif
                </div>
            @endif

            <form method="POST" action="{{ route('seller.store.update') }}" enctype="multipart/form-data"
                  class="mt-6 grid grid-cols-1 xl:grid-cols-3 gap-6">
                @csrf
                @method('PUT')

                <div class="xl:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
                    <div>
                        <label for="nama_toko" class="block text-xs font-semibold text-gray-700 mb-1.5">Nama Toko <span class="text-danger">*</span></label>
                        <input type="text" id="nama_toko" name="nama_toko" required maxlength="100"
                               value="{{ old('nama_toko', $store->nama_toko ?? '') }}" placeholder="Contoh: Kebun Hijau Jaya"
                               class="w-full px-3 py-2.5 rounded-lg border text-sm outline-none
                                      @error('nama_toko') border-red-400 @else border-gray-300 focus:border-primary focus:ring-primary @enderror">
                        @error('nama_toko')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="no_wa" class="block text-xs font-semibold text-gray-700 mb-1.5">Nomor WhatsApp <span class="text-danger">*</span></label>
                            <input type="text" id="no_wa" name="no_wa" required maxlength="20" placeholder="081234567890"
                                   value="{{ old('no_wa', $store->no_wa ?? '') }}"
                                   class="w-full px-3 py-2.5 rounded-lg border text-sm outline-none
                                          @error('no_wa') border-red-400 @else border-gray-300 focus:border-primary focus:ring-primary @enderror">
                            @error('no_wa')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            <p class="mt-1.5 text-[11px] text-gray-500">Pesanan pembeli akan diarahkan ke nomor ini.</p>
                        </div>

                        <div>
                            <label for="kota" class="block text-xs font-semibold text-gray-700 mb-1.5">Kota / Kabupaten <span class="text-danger">*</span></label>
                            <input type="text" id="kota" name="kota" required maxlength="80" placeholder="Jakarta Barat"
                                   value="{{ old('kota', $store->kota ?? '') }}"
                                   class="w-full px-3 py-2.5 rounded-lg border text-sm outline-none
                                          @error('kota') border-red-400 @else border-gray-300 focus:border-primary focus:ring-primary @enderror">
                            @error('kota')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div>
                        <label for="alamat" class="block text-xs font-semibold text-gray-700 mb-1.5">Alamat Toko <span class="text-danger">*</span></label>
                        <textarea id="alamat" name="alamat" rows="2" required maxlength="500"
                                  placeholder="Jalan, nomor, kelurahan, kecamatan"
                                  class="w-full px-3 py-2.5 rounded-lg border text-sm outline-none resize-none
                                         @error('alamat') border-red-400 @else border-gray-300 focus:border-primary focus:ring-primary @enderror">{{ old('alamat', $store->alamat ?? '') }}</textarea>
                        @error('alamat')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="deskripsi" class="block text-xs font-semibold text-gray-700 mb-1.5">Deskripsi Toko <span class="text-danger">*</span></label>
                        <textarea id="deskripsi" name="deskripsi" rows="4" required minlength="20" maxlength="1000"
                                  placeholder="Ceritakan jenis tanaman yang Anda jual dan keunggulan toko."
                                  class="w-full px-3 py-2.5 rounded-lg border text-sm outline-none resize-none
                                         @error('deskripsi') border-red-400 @else border-gray-300 focus:border-primary focus:ring-primary @enderror">{{ old('deskripsi', $store->deskripsi ?? '') }}</textarea>
                        @error('deskripsi')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <span class="block text-xs font-semibold text-gray-700 mb-2">Metode Penyerahan <span class="text-danger">*</span></span>
                        <div class="grid grid-cols-3 gap-3">
                            @foreach (['ambil' => 'Ambil di toko', 'kirim' => 'Dikirim', 'keduanya' => 'Keduanya'] as $nilai => $label)
                                <label class="cursor-pointer">
                                    <input type="radio" name="metode_serah" value="{{ $nilai }}" class="peer sr-only"
                                           @checked(old('metode_serah', $store->metode_serah ?? 'keduanya') === $nilai)>
                                    <div class="rounded-xl border-2 border-gray-200 p-3 text-center text-xs font-semibold text-gray-800 peer-checked:border-primary peer-checked:bg-primary-light transition-colors">
                                        {{ $label }}
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('metode_serah')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="space-y-5">
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                        <h2 class="text-sm font-bold text-gray-900">Logo Toko</h2>

                        <div id="logo-box" class="mt-4 w-full aspect-square rounded-lg bg-gray-50 border border-dashed border-gray-300 overflow-hidden flex items-center justify-center">
                            @if ($isEdit && $store->logo)
                                <img id="logo-img" src="{{ asset('storage/' . $store->logo) }}" alt="" class="w-full h-full object-cover">
                            @else
                                <div class="text-center text-gray-400 px-4">
                                    <svg class="w-10 h-10 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M14 8h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <p class="mt-2 text-[11px]">Belum ada logo</p>
                                </div>
                            @endif
                        </div>

                        <input type="file" id="logo" name="logo" accept="image/png,image/jpeg,image/webp"
                               onchange="pratinjauGambar(this, 'logo-box')"
                               class="mt-4 block w-full text-xs text-gray-600 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary-light file:text-primary hover:file:bg-emerald-100">
                        <p id="logo-box-info" class="mt-2 text-[11px] text-gray-500"></p>
                        @error('logo')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        <p class="mt-2 text-[11px] text-gray-500">Opsional. Maksimal 2 MB.</p>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-3">
                        <button type="submit" class="w-full py-2.5 rounded-lg bg-primary text-white text-sm font-bold shadow-sm hover:bg-primary-hover">
                            {{ $isEdit ? 'Simpan Perubahan' : 'Daftarkan Toko' }}
                        </button>
                        @if ($isEdit)
                            <a href="{{ route('seller.dashboard') }}" class="block w-full py-2.5 rounded-lg text-center text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200">
                                Batal
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    @include('partials.upload-preview')
@endpush
