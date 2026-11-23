@extends('layouts.app')

@section('title', 'Moderasi Produk — TanamKu')

@section('content')
    <div class="flex flex-col md:flex-row min-h-screen">
        @include('partials.sidebar')

        <div class="flex-1 min-w-0 p-4 sm:p-6 lg:p-8">
            @include('partials.alert')

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Moderasi Produk</h1>
                    <p class="mt-1 text-sm text-slate-500">{{ $plants->total() }} produk terdaftar dari seluruh toko mitra.</p>
                </div>
            </div>

            <form method="GET" action="{{ route('admin.plants.index') }}" class="mt-6 flex flex-col sm:flex-row gap-3">
                <div class="relative flex-grow">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama tanaman..."
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20 bg-white">
                </div>

                <select name="toko" class="px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm bg-white outline-none transition sm:w-56 focus:border-primary focus:ring-2 focus:ring-primary/20">
                    <option value="">Semua Toko Mitra</option>
                    @foreach ($stores as $toko)
                        <option value="{{ $toko->id }}" @selected((string) request('toko') === (string) $toko->id)>{{ $toko->nama_toko }}</option>
                    @endforeach
                </select>

                <select name="status" class="px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm bg-white outline-none transition sm:w-44 focus:border-primary focus:ring-2 focus:ring-primary/20">
                    <option value="">Semua Status</option>
                    <option value="tampil" @selected(request('status') === 'tampil')>Tampil di Katalog</option>
                    <option value="tersembunyi" @selected(request('status') === 'tersembunyi')>Disembunyikan</option>
                </select>

                <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary text-white text-xs font-bold shadow-xs hover:bg-primary-hover hover:shadow transition-all">Filter</button>

                @if (request()->hasAny(['q', 'toko', 'status']))
                    <a href="{{ route('admin.plants.index') }}" class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors text-center">Reset</a>
                @endif
            </form>

            <div class="mt-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm min-w-[700px]">
                        <thead class="bg-slate-50/80 text-left border-b border-slate-100">
                            <tr class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                                <th class="px-5 py-3.5">Produk Tanaman</th>
                                <th class="px-5 py-3.5">Toko Mitra</th>
                                <th class="px-5 py-3.5">Kategori</th>
                                <th class="px-5 py-3.5 text-right">Harga Satuan</th>
                                <th class="px-5 py-3.5 text-center">Stok</th>
                                <th class="px-5 py-3.5 text-center">Status</th>
                                <th class="px-5 py-3.5 text-right">Aksi Moderasi</th>
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
                                            <a href="{{ route('plants.show', $plant) }}" target="_blank" class="font-bold text-slate-900 text-xs hover:text-primary transition-colors truncate max-w-xs">
                                                {{ $plant->nama }}
                                            </a>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <a href="{{ route('stores.show', $plant->store) }}" target="_blank" class="text-xs font-semibold text-slate-800 hover:text-primary transition-colors">
                                            {{ $plant->store->nama_toko }}
                                        </a>
                                        @if (! $plant->store->isAktif())
                                            <span class="block text-[10px] text-amber-600 font-bold">toko {{ $plant->store->status }}</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-slate-600 text-xs font-medium">{{ $plant->category->nama }}</td>
                                    <td class="px-5 py-3.5 text-right font-extrabold text-slate-900 whitespace-nowrap">{{ $plant->harga_rupiah }}</td>
                                    <td class="px-5 py-3.5 text-center text-xs font-bold text-slate-700">{{ $plant->stok }}</td>
                                    <td class="px-5 py-3.5 text-center">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold border {{ $plant->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-500 border-slate-200' }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $plant->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                            {{ $plant->is_active ? 'Tampil' : 'Disembunyikan' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center justify-end gap-2">
                                            <form method="POST" action="{{ route('admin.plants.toggle', $plant) }}">
                                                @csrf @method('PATCH')
                                                <button class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $plant->is_active ? 'text-amber-800 bg-amber-50 hover:bg-amber-100 border border-amber-200' : 'text-primary bg-primary/10 hover:bg-primary/20 border border-primary/20' }}">
                                                    {{ $plant->is_active ? 'Sembunyikan' : 'Tampilkan' }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="px-5 py-12 text-center text-sm text-slate-400">Tidak ada produk ditemukan.</td></tr>
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
