@extends('layouts.app')

@section('title', 'Kelola Kategori — TanamKu')

@section('content')
    <div class="flex">
        @include('partials.sidebar')

        <div class="flex-1 min-w-0 p-5 lg:p-8">
            @include('partials.alert')

            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Kelola Kategori</h1>
            <p class="mt-1 text-sm text-gray-600">{{ $categories->count() }} kategori dipakai untuk mengelompokkan produk.</p>

            <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Tambah --}}
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                    <h2 class="text-sm font-bold text-gray-900">Tambah Kategori</h2>

                    <form method="POST" action="{{ route('admin.categories.store') }}" class="mt-4 space-y-3">
                        @csrf
                        <div>
                            <label for="nama" class="block text-xs font-semibold text-gray-700 mb-1.5">Nama <span class="text-danger">*</span></label>
                            <input type="text" id="nama" name="nama" required maxlength="80" placeholder="Contoh: Tanaman Air"
                                   class="w-full px-3 py-2.5 rounded-lg border text-sm outline-none
                                          @error('nama') border-red-400 @else border-gray-300 focus:border-primary focus:ring-primary @enderror">
                            @error('nama')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="deskripsi" class="block text-xs font-semibold text-gray-700 mb-1.5">Deskripsi</label>
                            <textarea id="deskripsi" name="deskripsi" rows="2" maxlength="300" placeholder="Opsional"
                                      class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary resize-none"></textarea>
                        </div>

                        <button type="submit" class="w-full py-2.5 rounded-lg bg-primary text-white text-sm font-bold hover:bg-primary-hover">
                            Simpan Kategori
                        </button>
                    </form>
                </div>

                {{-- Daftar --}}
                <table class="min-w-full border border-gray-200">
    <thead class="bg-gray-50">
        <tr>
            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Nama</th>
            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Slug</th>
            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Produk</th>
            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($categories as $kategori)
            <tr class="border-t">
                <td class="px-4 py-2">{{ $kategori->nama }}</td>
                <td class="px-4 py-2">{{ $kategori->slug }}</td>
                <td class="px-4 py-2">{{ $kategori->plants_count ?? 0 }}</td>
                <td class="px-4 py-2">
                    <form method="POST" action="{{ route('admin.categories.update', $kategori) }}" class="flex flex-col sm:flex-row sm:items-center gap-3">
                        @csrf
                        @method('PUT')
                        <input type="text" name="nama" value="{{ $kategori->nama }}" required maxlength="80"
                               class="w-full px-2.5 py-1.5 rounded-md border border-gray-300 text-sm font-semibold outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                        <button type="submit" class="px-2.5 py-1.5 rounded-md text-[11px] font-semibold text-primary bg-primary-light hover:bg-emerald-100">Simpan</button>
                    </form>
                    <form method="POST" action="{{ route('admin.categories.destroy', $kategori) }}" class="mt-2"
                          onsubmit="return confirm('Hapus kategori &quot;{{ $kategori->nama }}&quot;?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-2.5 py-1.5 rounded-md text-[11px] font-semibold text-danger bg-red-50 hover:bg-red-100">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="px-4 py-2 text-center text-gray-500">Belum ada kategori</td>
            </tr>
        @endforelse
    </tbody>
</table>
                    <div class="px-5 py-4 border-b border-gray-100">
                        <h2 class="text-sm font-bold text-gray-900">Daftar Kategori</h2>
                    </div>

                    <div class="divide-y divide-gray-100">
                        @forelse ($categories as $kategori)
                            <div class="px-5 py-4">
                                <form method="POST" action="{{ route('admin.categories.update', $kategori) }}" class="flex flex-col sm:flex-row sm:items-center gap-3">
                                    @csrf
                                    @method('PUT')

                                    <div class="flex-1 min-w-0">
                                        <input type="text" name="nama" value="{{ $kategori->nama }}" required maxlength="80"
                                               class="w-full px-2.5 py-1.5 rounded-md border border-gray-300 text-sm font-semibold outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                                        <p class="mt-1 text-[11px] text-gray-500">
                                            slug: <span class="font-mono">{{ $kategori->slug }}</span> ·
                                            {{ $kategori->plants_count }} produk
                                        </p>
                                    </div>

                                    <div class="flex items-center gap-2 shrink-0">
                                        <button type="submit" class="px-2.5 py-1.5 rounded-md text-[11px] font-semibold text-primary bg-primary-light hover:bg-emerald-100">
                                            Simpan
                                        </button>
                                    </div>
                                </form>

                                <form method="POST" action="{{ route('admin.categories.destroy', $kategori) }}" class="mt-2"
                                      onsubmit="return confirm('Hapus kategori &quot;{{ $kategori->nama }}&quot;?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1.5 rounded-md text-[11px] font-semibold text-danger bg-red-50 hover:bg-red-100">
                                        Hapus kategori
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
