@extends('layouts.app')

@section('title', 'Produk Saya — TanamKu')

@section('content')
    <div class="flex">
        @include('partials.sidebar')

        <div class="flex-1 min-w-0 p-5 lg:p-8">
            @include('partials.alert')

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Produk Saya</h1>
                    <p class="mt-1 text-sm text-gray-600">{{ $plants->total() }} produk terdaftar di toko {{ $store->nama_toko }}.</p>
                </div>
                <a href="{{ route('seller.plants.create') }}"
                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-primary text-white text-sm font-semibold hover:bg-primary-hover">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Produk
                </a>
            </div>

            <form method="GET" action="{{ route('seller.plants.index') }}" class="mt-6 flex gap-3">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama produk..."
                       class="flex-grow px-3 py-2.5 rounded-lg border border-gray-300 text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                <button type="submit" class="px-5 py-2.5 rounded-lg bg-primary text-white text-sm font-semibold hover:bg-primary-hover">Cari</button>
                @if (request('q'))
                    <a href="{{ route('seller.plants.index') }}" class="px-5 py-2.5 rounded-lg text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200">Reset</a>
                @endif
            </form>

            <div class="mt-6 bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-left">
                            <tr class="text-[11px] font-semibold text-gray-600 uppercase tracking-wide">
                                <th class="px-5 py-3">Produk</th>
                                <th class="px-5 py-3">Kategori</th>
                                <th class="px-5 py-3 text-right">Harga</th>
                                <th class="px-5 py-3 text-center">Stok</th>
                                <th class="px-5 py-3 text-center">Status</th>
                                <th class="px-5 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($plants as $plant)
                                <tr class="hover:bg-gray-50/60">
                                    <td class="px-5 py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-11 h-11 rounded-lg bg-gray-50 border border-gray-100 overflow-hidden shrink-0 flex items-center justify-center">
                                                @if ($plant->foto)
                                                    <img src="{{ asset('storage/' . $plant->foto) }}" alt="" class="w-full h-full object-cover">
                                                @else
                                                    <svg class="w-5 h-5 text-emerald-700/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 21v-8m0 0C12 8 8 4 3 4c0 5 4 9 9 9zm0 0c0-5 4-9 9-9 0 5-4 9-9 9z"/>
                                                    </svg>
                                                @endif
                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-semibold text-gray-900 truncate">{{ $plant->nama }}</p>
                                                <p class="text-[11px] text-gray-500 font-mono truncate">{{ $plant->slug }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3 text-gray-600">{{ $plant->category->nama }}</td>
                                    <td class="px-5 py-3 text-right font-semibold text-gray-900 whitespace-nowrap">{{ $plant->harga_rupiah }}</td>
                                    <td class="px-5 py-3 text-center">
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold border
                                            @if ($plant->stok <= 0) bg-red-50 text-danger border-red-200
                                            @elseif ($plant->stok <= 4) bg-amber-50 text-amber-700 border-amber-200
                                            @else bg-emerald-50 text-emerald-700 border-emerald-200 @endif">
                                            {{ $plant->stok }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-center text-xs {{ $plant->is_active ? 'text-emerald-700' : 'text-gray-400' }}">
                                        {{ $plant->is_active ? 'Tampil' : 'Disembunyikan' }}
                                    </td>
                                    <td class="px-5 py-3">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('plants.show', $plant) }}" class="px-2.5 py-1.5 rounded-md text-[11px] font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200">Lihat</a>
                                            <a href="{{ route('seller.plants.edit', $plant) }}" class="px-2.5 py-1.5 rounded-md text-[11px] font-semibold text-primary bg-primary-light hover:bg-emerald-100">Edit</a>
                                            <form method="POST" action="{{ route('seller.plants.destroy', $plant) }}"
                                                  onsubmit="return confirm('Hapus produk &quot;{{ $plant->nama }}&quot;?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-2.5 py-1.5 rounded-md text-[11px] font-semibold text-danger bg-red-50 hover:bg-red-100">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-14 text-center">
                                        <p class="text-sm font-semibold text-gray-900">Belum ada produk</p>
                                        <p class="mt-1 text-xs text-gray-500">Tambahkan produk pertama Anda lewat tombol di atas.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6">{{ $plants->links() }}</div>
        </div>
    </div>
@endsection
