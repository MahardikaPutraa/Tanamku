@extends('layouts.app')

@php $isEdit = isset($plant); @endphp

@section('title', ($isEdit ? 'Edit Produk' : 'Tambah Produk') . ' — TanamKu')

@section('content')
    <div class="flex flex-col md:flex-row min-h-screen">
        @include('partials.sidebar')

        <div class="flex-1 min-w-0 p-4 sm:p-6 lg:p-8">
            @include('partials.alert')

            <nav class="text-xs text-gray-500 mb-4">
                <a href="{{ route('seller.dashboard') }}" class="hover:text-primary">Dashboard</a>
                <span class="mx-1.5">/</span>
                <a href="{{ route('seller.plants.index') }}" class="hover:text-primary">Produk Saya</a>
                <span class="mx-1.5">/</span>
                <span class="text-gray-800 font-medium">{{ $isEdit ? 'Edit' : 'Tambah' }}</span>
            </nav>

            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">
                {{ $isEdit ? 'Edit Produk' : 'Tambah Produk Baru' }}
            </h1>
            <p class="mt-1 text-sm text-gray-600">
                {{ $isEdit ? 'Perbarui informasi "' . $plant->nama . '".' : 'Produk akan tampil di katalog setelah toko disetujui admin.' }}
            </p>

            <form method="POST"
                  action="{{ $isEdit ? route('seller.plants.update', $plant) : route('seller.plants.store') }}"
                  enctype="multipart/form-data"
                  class="mt-6 grid grid-cols-1 xl:grid-cols-3 gap-6">
                @csrf
                @if ($isEdit)
                    @method('PUT')
                @endif

                <div class="xl:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 sm:p-7 space-y-5">
                    <div>
                        <label for="nama" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Tanaman <span class="text-danger">*</span></label>
                        <input type="text" id="nama" name="nama" required maxlength="120"
                               value="{{ old('nama', $plant->nama ?? '') }}" placeholder="Contoh: Monstera Deliciosa"
                               class="w-full px-3.5 py-2.5 rounded-xl border text-sm outline-none transition
                                      @error('nama') border-red-400 focus:ring-2 focus:ring-red-100 @else border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/20 @enderror">
                        @error('nama')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="category_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Kategori <span class="text-danger">*</span></label>
                            <select id="category_id" name="category_id" required
                                    class="w-full px-3.5 py-2.5 rounded-xl border text-sm bg-white outline-none transition
                                           @error('category_id') border-red-400 focus:ring-2 focus:ring-red-100 @else border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/20 @enderror">
                                <option value="">— Pilih kategori —</option>
                                @foreach ($categories as $kategori)
                                    <option value="{{ $kategori->id }}" @selected((string) old('category_id', $plant->category_id ?? '') === (string) $kategori->id)>
                                        {{ $kategori->nama }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="kondisi" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Kondisi <span class="text-danger">*</span></label>
                            <select id="kondisi" name="kondisi" required
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm bg-white outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20">
                                <option value="sehat" @selected(old('kondisi', $plant->kondisi ?? 'sehat') === 'sehat')>✓ Sehat Terawat</option>
                                <option value="perlu_perhatian" @selected(old('kondisi', $plant->kondisi ?? '') === 'perlu_perhatian')>⚠ Perlu Perhatian</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="harga" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Harga (Rp) <span class="text-danger">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 text-xs font-bold">Rp</span>
                                <input type="number" id="harga" name="harga" required min="1" step="1"
                                       inputmode="numeric"
                                       value="{{ old('harga', $plant->harga ?? '') }}" placeholder="85000"
                                       class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border text-sm outline-none transition
                                              @error('harga') border-red-400 focus:ring-2 focus:ring-red-100 @else border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/20 @enderror">
                            </div>
                            @error('harga')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="stok" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Stok Unit <span class="text-danger">*</span></label>
                            <input type="number" id="stok" name="stok" required min="0"
                                   value="{{ old('stok', $plant->stok ?? 0) }}"
                                   class="w-full px-3.5 py-2.5 rounded-xl border text-sm outline-none transition
                                          @error('stok') border-red-400 focus:ring-2 focus:ring-red-100 @else border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/20 @enderror">
                            @error('stok')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div>
                        <label for="deskripsi" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Deskripsi Tanaman <span class="text-danger">*</span></label>
                        <textarea id="deskripsi" name="deskripsi" rows="4" required minlength="20"
                                  placeholder="Jelaskan karakter tanaman, ukuran, bentuk daun, dan keunggulannya..."
                                  class="w-full px-3.5 py-2.5 rounded-xl border text-sm outline-none resize-none transition
                                         @error('deskripsi') border-red-400 focus:ring-2 focus:ring-red-100 @else border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/20 @enderror">{{ old('deskripsi', $plant->deskripsi ?? '') }}</textarea>
                        @error('deskripsi')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="perawatan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Panduan Cara Merawat</label>
                        <textarea id="perawatan" name="perawatan" rows="5"
                                  placeholder="Cahaya: Terang tidak langsung&#10;Air: 1 kali seminggu&#10;Media: Tanah porous&#10;Suhu: 18-30 °C"
                                  class="w-full px-3.5 py-2.5 rounded-xl border text-sm outline-none resize-none font-mono text-xs transition
                                         @error('perawatan') border-red-400 focus:ring-2 focus:ring-red-100 @else border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/20 @enderror">{{ old('perawatan', $plant->perawatan ?? '') }}</textarea>
                        @error('perawatan')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        <p class="mt-1.5 text-[11px] text-slate-500">
                            Format: satu poin per baris dengan format <code class="bg-slate-100 text-slate-700 px-1 py-0.5 rounded font-mono">Label: penjelasan</code>.
                        </p>
                    </div>
                </div>

                <div class="space-y-5">
                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 sm:p-6">
                        <h2 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Foto Produk</h2>

                        <div id="foto-box" class="mt-3.5 w-full aspect-square rounded-xl bg-slate-50 border-2 border-dashed border-slate-200 overflow-hidden flex items-center justify-center transition-colors">
                            @if ($isEdit && $plant->foto)
                                <img id="foto-img" src="{{ asset('storage/' . $plant->foto) }}" alt="" class="w-full h-full object-cover">
                            @else
                                <div class="text-center text-slate-400 px-4">
                                    <svg class="w-10 h-10 mx-auto text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M14 8h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <p class="mt-2 text-xs font-medium">Unggah foto tanaman</p>
                                    <p class="text-[10px] text-slate-400">JPG, PNG, atau WEBP (maks 2MB)</p>
                                </div>
                            @endif
                        </div>

                        <input type="file" id="foto" name="foto" accept="image/png,image/jpeg,image/webp"
                               onchange="pratinjauGambar(this, 'foto-box')"
                               class="mt-4 block w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary-light file:text-primary hover:file:bg-emerald-100 transition-colors">
                        <p id="foto-box-info" class="mt-2 text-[11px] text-slate-500"></p>
                        @error('foto')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 sm:p-6">
                        <h2 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Opsi Publikasi</h2>
                        <label class="mt-3.5 flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" name="is_featured" value="1"
                                   @checked(old('is_featured', $plant->is_featured ?? false))
                                   class="mt-0.5 rounded border-slate-300 text-primary focus:ring-primary">
                            <span>
                                <span class="block text-xs font-semibold text-slate-800">Ajukan sebagai produk unggulan</span>
                                <span class="block text-[11px] text-slate-500 mt-0.5">Berpeluang tampil di bagian "Pilihan Minggu Ini" pada beranda.</span>
                            </span>
                        </label>
                    </div>

                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 sm:p-6 space-y-3">
                        <button type="submit" class="w-full py-3 rounded-xl bg-primary text-white text-sm font-bold shadow-sm hover:bg-primary-hover hover:shadow transition-all">
                            {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Produk' }}
                        </button>
                        <a href="{{ route('seller.plants.index') }}" class="block w-full py-2.5 rounded-xl text-center text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors">
                            Batal
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    @include('partials.upload-preview')
@endpush
