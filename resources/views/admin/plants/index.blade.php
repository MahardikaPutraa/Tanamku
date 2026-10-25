@extends('layouts.app')

@section('title', 'Moderasi Produk — TanamKu')

@section('content')
    <div class="flex">
        @include('partials.sidebar')

        <div class="flex-1 min-w-0 p-5 lg:p-8">
            @include('partials.alert')

            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Moderasi Produk</h1>
            <p class="mt-1 text-sm text-gray-600">{{ $plants->total() }} produk dari seluruh toko.</p>

            <form method="GET" action="{{ route('admin.plants.index') }}" class="mt-6 flex flex-col sm:flex-row gap-3">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama produk..."
                       class="flex-grow px-3 py-2.5 rounded-lg border border-gray-300 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary">

                <select name="toko" class="px-3 py-2.5 rounded-lg border border-gray-300 text-sm bg-white outline-none sm:w-52">
                    <option value="">Semua toko</option>
                    @foreach ($stores as $toko)
                        <option value="{{ $toko->id }}" @selected((string) request('toko') === (string) $toko->id)>{{ $toko->nama_toko }}</option>
                    @endforeach
                </select>

                <select name="status" class="px-3 py-2.5 rounded-lg border border-gray-300 text-sm bg-white outline-none sm:w-44">
                    <option value="">Semua status</option>
                    <option value="tampil" @selected(request('status') === 'tampil')>Tampil</option>
                    <option value="tersembunyi" @selected(request('status') === 'tersembunyi')>Disembunyikan</option>
                </select>

                <button type="submit" class="px-5 py-2.5 rounded-lg bg-primary text-white text-sm font-semibold hover:bg-primary-hover">Filter</button>

                @if (request()->hasAny(['q', 'toko', 'status']))
                    <a href="{{ route('admin.plants.index') }}" class="px-5 py-2.5 rounded-lg text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 text-center">Reset</a>
                @endif
            </form>

            <div class="mt-6 bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-left">
                            <tr class="text-[11px] font-semibold text-gray-600 uppercase tracking-wide">
                                <th class="px-5 py-3">Produk</th>
                                <th class="px-5 py-3">Toko</th>
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
                                            <div class="w-10 h-10 rounded-lg bg-gray-50 border border-gray-100 overflow-hidden shrink-0">
                                                @if ($plant->foto)
                                                    <img src="{{ asset('storage/' . $plant->foto) }}" alt="" class="w-full h-full object-cover">
                                                @endif
                                            </div>
                                            <a href="{{ route('plants.show', $plant) }}" target="_blank" class="font-semibold text-gray-900 hover:text-primary truncate">
                                                {{ $plant->nama }}
                                            </a>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3">
                                        <a href="{{ route('stores.show', $plant->store) }}" target="_blank" class="text-xs text-gray-700 hover:text-primary">
                                            {{ $plant->store->nama_toko }}
                                        </a>
                                        @if (! $plant->store->isAktif())
                                            <span class="block text-[10px] text-amber-600 font-semibold">toko {{ $plant->store->status }}</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 text-gray-600 text-xs">{{ $plant->category->nama }}</td>
                                    <td class="px-5 py-3 text-right font-semibold text-gray-900 whitespace-nowrap">{{ $plant->harga_rupiah }}</td>
                                    <td class="px-5 py-3 text-center text-xs">{{ $plant->stok }}</td>
                                    <td class="px-5 py-3 text-center">
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $plant->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-gray-100 text-gray-600 border-gray-200' }}">
                                            {{ $plant->is_active ? 'Tampil' : 'Disembunyikan' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3">
                                        <div class="flex items-center justify-end gap-2">
                                            <form method="POST" action="{{ route('admin.plants.toggle', $plant) }}">
                                                @csrf @method('PATCH')
                                                <button class="px-2.5 py-1.5 rounded-md text-[11px] font-semibold text-primary bg-primary-light hover:bg-emerald-100">
                                                    {{ $plant->is_active ? 'Sembunyikan' : 'Tampilkan' }}
                                                </button>
                                            </form>

                                            <form method="POST" action="{{ route('admin.plants.destroy', $plant) }}"
                                                  onsubmit="return confirm('Hapus produk &quot;{{ $plant->nama }}&quot;?');">
                                                @csrf @method('DELETE')
                                                <button class="px-2.5 py-1.5 rounded-md text-[11px] font-semibold text-danger bg-red-50 hover:bg-red-100">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="px-5 py-14 text-center text-sm text-gray-500">Tidak ada produk dengan filter ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6">{{ $plants->links() }}</div>
        </div>
    </div>
@endsection
