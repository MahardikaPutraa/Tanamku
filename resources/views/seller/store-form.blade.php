@extends('layouts.app')

@php $isEdit = (bool) $store; @endphp

@section('title', $isEdit ? 'Profil Toko' : 'Buka Toko')

@section('content')
    <div class="flex flex-col md:flex-row min-h-screen">
        @include('partials.sidebar')

        <div class="flex-1 min-w-0 p-4 sm:p-6 lg:p-8">
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
                <div class="mt-4 p-4 rounded-xl border text-xs flex items-center justify-between gap-3
                    {{ $store->status === 'aktif' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : ($store->status === 'menunggu' ? 'bg-amber-50 border-amber-200 text-amber-900' : 'bg-red-50 border-red-200 text-red-800') }}">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full {{ $store->status === 'aktif' ? 'bg-emerald-500' : ($store->status === 'menunggu' ? 'bg-amber-500' : 'bg-red-500') }}"></span>
                        <span>Status toko: <strong>{{ $store->status_label }}</strong>
                            @if ($store->status === 'menunggu')
                                — produk Anda belum tampil di katalog sampai disetujui admin.
                            @endif
                        </span>
                    </div>
                    @if ($store->status === 'aktif')
                        <a href="{{ route('stores.show', $store) }}" target="_blank" class="font-bold underline shrink-0">Lihat Toko Publik →</a>
                    @endif
                </div>
            @endif

            <form method="POST" action="{{ route('seller.store.update') }}" enctype="multipart/form-data"
                  class="mt-6 grid grid-cols-1 xl:grid-cols-3 gap-6">
                @csrf
                @method('PUT')

                <div class="xl:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 sm:p-7 space-y-5">
                    <div>
                        <label for="nama_toko" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Toko <span class="text-danger">*</span></label>
                        <input type="text" id="nama_toko" name="nama_toko" required maxlength="100"
                               value="{{ old('nama_toko', $store->nama_toko ?? '') }}" placeholder="Contoh: Kebun Hijau Jaya"
                               class="w-full px-3.5 py-2.5 rounded-xl border text-sm outline-none transition
                                      @error('nama_toko') border-red-400 focus:ring-2 focus:ring-red-100 @else border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/20 @enderror">
                        @error('nama_toko')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="no_wa" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nomor WhatsApp <span class="text-danger">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 text-sm">📱</span>
                                <input type="text" id="no_wa" name="no_wa" required maxlength="20" placeholder="081234567890"
                                       value="{{ old('no_wa', $store->no_wa ?? '') }}"
                                       class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border text-sm outline-none transition
                                              @error('no_wa') border-red-400 focus:ring-2 focus:ring-red-100 @else border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/20 @enderror">
                            </div>
                            @error('no_wa')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            <p class="mt-1 text-[11px] text-slate-500">Nomor tujuan pesanan otomatis via WhatsApp.</p>
                        </div>

                        <div>
                            <label for="kota" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Kota / Kabupaten <span class="text-danger">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 text-sm">📍</span>
                                <input type="text" id="kota" name="kota" required maxlength="80" placeholder="Contoh: Bandung"
                                       value="{{ old('kota', $store->kota ?? '') }}"
                                       class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border text-sm outline-none transition
                                              @error('kota') border-red-400 focus:ring-2 focus:ring-red-100 @else border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/20 @enderror">
                            </div>
                            @error('kota')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div>
                        <label for="alamat" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Alamat Lengkap Toko <span class="text-danger">*</span></label>
                        <textarea id="alamat" name="alamat" rows="2" required maxlength="500"
                                  placeholder="Jalan, nomor, kelurahan, kecamatan"
                                  class="w-full px-3.5 py-2.5 rounded-xl border text-sm outline-none resize-none transition
                                         @error('alamat') border-red-400 focus:ring-2 focus:ring-red-100 @else border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/20 @enderror">{{ old('alamat', $store->alamat ?? '') }}</textarea>
                        @error('alamat')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="deskripsi" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Deskripsi Profil Toko <span class="text-danger">*</span></label>
                        <textarea id="deskripsi" name="deskripsi" rows="4" required minlength="20" maxlength="1000"
                                  placeholder="Ceritakan jenis tanaman yang Anda jual, spesialisasi, dan keunggulan toko Anda..."
                                  class="w-full px-3.5 py-2.5 rounded-xl border text-sm outline-none resize-none transition
                                         @error('deskripsi') border-red-400 focus:ring-2 focus:ring-red-100 @else border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/20 @enderror">{{ old('deskripsi', $store->deskripsi ?? '') }}</textarea>
                        @error('deskripsi')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <span class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Metode Penyerahan yang Dilayani <span class="text-danger">*</span></span>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            @foreach (['ambil' => ['Ambil di Toko', 'Pembeli mengambil langsung'], 'kirim' => ['Dikirim', 'Kirim via kurir/ekspedisi'], 'keduanya' => ['Keduanya', 'Bisa kirim & ambil']] as $nilai => $detail)
                                <label class="cursor-pointer">
                                    <input type="radio" name="metode_serah" value="{{ $nilai }}" class="peer sr-only"
                                           @checked(old('metode_serah', $store->metode_serah ?? 'keduanya') === $nilai)>
                                    <div class="rounded-xl border-2 border-slate-200 p-3 text-center peer-checked:border-primary peer-checked:bg-primary-light transition-all">
                                        <span class="block text-xs font-bold text-slate-900">{{ $detail[0] }}</span>
                                        <span class="block text-[10px] text-slate-500 mt-0.5">{{ $detail[1] }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('metode_serah')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="space-y-5">
                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 sm:p-6">
                        <h2 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Logo Toko</h2>

                        <div id="logo-box" class="mt-3.5 w-full aspect-square rounded-xl bg-slate-50 border-2 border-dashed border-slate-200 overflow-hidden flex items-center justify-center transition-colors">
                            @if ($isEdit && $store->logo_url)
                                <img id="logo-img" src="{{ $store->logo_url }}" alt="" class="w-full h-full object-cover">
                            @else
                                <div class="text-center text-slate-400 px-4">
                                    <svg class="w-10 h-10 mx-auto text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M14 8h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <p class="mt-2 text-xs font-medium">Unggah logo toko</p>
                                    <p class="text-[10px] text-slate-400">PNG, JPG, WEBP (maks 2MB)</p>
                                </div>
                            @endif
                        </div>

                        <input type="file" id="logo" name="logo" accept="image/png,image/jpeg,image/webp"
                               onchange="pratinjauGambar(this, 'logo-box')"
                               class="mt-4 block w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary-light file:text-primary hover:file:bg-emerald-100 transition-colors">
                        <p id="logo-box-info" class="mt-2 text-[11px] text-slate-500"></p>
                        @error('logo')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 sm:p-6 space-y-3">
                        <button type="submit" class="w-full py-3 rounded-xl bg-primary text-white text-sm font-bold shadow-sm hover:bg-primary-hover hover:shadow transition-all">
                            {{ $isEdit ? 'Simpan Perubahan' : 'Daftarkan Toko' }}
                        </button>
                        @if ($isEdit)
                            <a href="{{ route('seller.dashboard') }}" class="block w-full py-2.5 rounded-xl text-center text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors">
                                Kembali ke Dashboard
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
