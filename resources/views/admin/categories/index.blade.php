@extends('layouts.app')

@section('title', 'Kelola Kategori — TanamKu')

@section('content')
    <div class="flex flex-col md:flex-row min-h-screen">
        @include('partials.sidebar')

        <div class="flex-1 min-w-0 p-4 sm:p-6 lg:p-8">
            @include('partials.alert')

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Kelola Kategori</h1>
                    <p class="mt-1 text-sm text-slate-500">{{ $categories->count() }} kategori aktif untuk mengelompokkan produk tanaman.</p>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Form Tambah Kategori --}}
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 sm:p-6 h-fit">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="w-7 h-7 rounded-lg bg-primary/10 text-primary flex items-center justify-center text-sm font-bold">＋</span>
                        <h2 class="text-sm font-bold text-slate-900">Tambah Kategori Baru</h2>
                    </div>

                    <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label for="nama" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Nama Kategori <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="nama" name="nama" required maxlength="80" placeholder="Contoh: Tanaman Air"
                                   class="w-full px-3.5 py-2.5 rounded-xl border text-sm outline-none transition
                                          @error('nama') border-red-400 focus:ring-2 focus:ring-red-100 @else border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/20 @enderror">
                            @error('nama')<p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="deskripsi" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Deskripsi</label>
                            <textarea id="deskripsi" name="deskripsi" rows="3" maxlength="300" placeholder="Keterangan singkat kategori (opsional)..."
                                      class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 resize-none transition"></textarea>
                        </div>

                        <button type="submit" class="w-full py-2.5 rounded-xl bg-primary text-white text-xs font-bold shadow-xs hover:bg-primary-hover hover:shadow transition-all">
                            Simpan Kategori
                        </button>
                    </form>
                </div>

                {{-- Tabel Daftar Kategori --}}
                <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                        <h2 class="text-sm font-bold text-slate-900">Daftar Kategori Terdaftar</h2>
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600">
                            {{ $categories->count() }} Data
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm min-w-[550px]">
                            <thead class="bg-slate-50/80 text-left border-b border-slate-100">
                                <tr class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                                    <th class="px-5 py-3.5">Nama & Slug</th>
                                    <th class="px-5 py-3.5 text-center">Jumlah Produk</th>
                                    <th class="px-5 py-3.5 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($categories as $kategori)
                                    <tr class="hover:bg-slate-50/60 transition-colors">
                                        <td class="px-5 py-3.5">
                                            <form id="edit-cat-{{ $kategori->id }}" method="POST" action="{{ route('admin.categories.update', $kategori) }}">
                                                @csrf
                                                @method('PUT')
                                                <input type="text" name="nama" value="{{ $kategori->nama }}" required maxlength="80"
                                                       class="w-full max-w-xs px-3 py-1.5 rounded-lg border border-slate-200 text-sm font-bold text-slate-900 outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition">
                                            </form>
                                            <p class="mt-1 text-[11px] text-slate-400">
                                                slug: <span class="font-mono text-slate-600 font-semibold">{{ $kategori->slug }}</span>
                                            </p>
                                        </td>
                                        <td class="px-5 py-3.5 text-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                                                {{ $kategori->plants_count ?? 0 }} produk
                                            </span>
                                        </td>
                                        <td class="px-5 py-3.5 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <button type="submit" form="edit-cat-{{ $kategori->id }}"
                                                        class="px-3 py-1.5 rounded-lg text-xs font-bold text-primary bg-primary/10 hover:bg-primary/20 border border-primary/20 transition-colors">
                                                    Simpan
                                                </button>
                                                <form method="POST" action="{{ route('admin.categories.destroy', $kategori) }}"
                                                      onsubmit="return confirm('Hapus kategori &quot;{{ $kategori->nama }}&quot;?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 transition-colors">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-5 py-10 text-center text-slate-400 text-sm">Belum ada kategori yang ditambahkan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
