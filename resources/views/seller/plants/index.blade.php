@extends('layouts.app')

@section('title', 'Produk Saya — TanamKu')

@section('content')
    <div class="flex flex-col md:flex-row min-h-screen">
        @include('partials.sidebar')

        <div class="flex-1 min-w-0 p-4 sm:p-6 lg:p-8">
            @include('partials.alert')

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Produk Saya</h1>
                    <p class="mt-1 text-sm text-slate-500">{{ $plants->total() }} produk terdaftar di toko <strong>{{ $store->nama_toko }}</strong>.</p>
                </div>
                <a href="{{ route('seller.plants.create') }}"
                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-primary text-white text-xs font-bold shadow-xs hover:bg-primary-hover hover:shadow transition-all shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Tambah Produk Baru</span>
                </a>
            </div>

            <form method="GET" action="{{ route('seller.plants.index') }}" class="mt-6 flex flex-col sm:flex-row gap-2.5">
                <div class="relative flex-grow">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama tanaman..."
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition bg-white">
                </div>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary text-white text-xs font-bold shadow-xs hover:bg-primary-hover hover:shadow transition-all">Cari</button>
                @if (request('q'))
                    <a href="{{ route('seller.plants.index') }}" class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors text-center">Reset</a>
                @endif
            </form>

            <div class="mt-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm min-w-[650px]">
                        <thead class="bg-slate-50/80 text-left border-b border-slate-100">
                            <tr class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                                <th class="px-5 py-3.5">Produk Tanaman</th>
                                <th class="px-5 py-3.5">Kategori</th>
                                <th class="px-5 py-3.5 text-right">Harga Satuan</th>
                                <th class="px-5 py-3.5 text-center">Stok</th>
                                <th class="px-5 py-3.5 text-center">Status</th>
                                <th class="px-5 py-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($plants as $plant)
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-12 h-12 rounded-xl bg-slate-100 border border-slate-200/80 overflow-hidden shrink-0 flex items-center justify-center">
                                                @if ($plant->foto)
                                                    <img src="{{ asset('storage/' . $plant->foto) }}" alt="" class="w-full h-full object-cover">
                                                @else
                                                    <span class="text-xl">🌿</span>
                                                @endif
                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-bold text-slate-900 text-xs truncate">{{ $plant->nama }}</p>
                                                <p class="text-[11px] text-slate-400 font-mono truncate mt-0.5">{{ $plant->slug }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5 text-slate-600 text-xs font-medium">{{ $plant->category->nama }}</td>
                                    <td class="px-5 py-3.5 text-right font-extrabold text-slate-900 whitespace-nowrap">{{ $plant->harga_rupiah }}</td>
                                    <td class="px-5 py-3.5 text-center">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold border
                                            @if ($plant->stok <= 0) bg-red-50 text-red-700 border-red-200
                                            @elseif ($plant->stok <= 4) bg-amber-50 text-amber-700 border-amber-200
                                            @else bg-emerald-50 text-emerald-700 border-emerald-200 @endif">
                                            {{ $plant->stok }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 text-center">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold border {{ $plant->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-500 border-slate-200' }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $plant->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                            {{ $plant->is_active ? 'Tampil' : 'Disembunyikan' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a href="{{ route('plants.show', $plant) }}" class="px-2.5 py-1.5 rounded-lg text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors">
                                                Lihat
                                            </a>
                                            <a href="{{ route('seller.plants.edit', $plant) }}" class="px-2.5 py-1.5 rounded-lg text-xs font-bold text-primary bg-primary/10 hover:bg-primary/20 border border-primary/20 transition-colors">
                                                Edit
                                            </a>
                                            <form method="POST" action="{{ route('seller.plants.destroy', $plant) }}"
                                                  onsubmit="return confirm('Hapus produk &quot;{{ $plant->nama }}&quot;?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-2.5 py-1.5 rounded-lg text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 transition-colors">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-5 py-12 text-center text-sm text-slate-400">Belum ada produk di toko Anda.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($plants->hasPages())
                    <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/50">
                        {{ $plants->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
