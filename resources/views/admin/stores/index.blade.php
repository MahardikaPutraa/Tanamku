@extends('layouts.app')

@section('title', 'Kelola Toko — TanamKu')

@section('content')
    <div class="flex">
        @include('partials.sidebar')

        <div class="flex-1 min-w-0 p-5 lg:p-8">
            @include('partials.alert')

            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Kelola Toko</h1>
            <p class="mt-1 text-sm text-gray-600">{{ $stores->total() }} toko terdaftar. Toko berstatus <strong>aktif</strong> yang produknya tampil di katalog.</p>

            <div class="mt-6 flex flex-wrap gap-2">
                <a href="{{ route('admin.stores.index') }}"
                   class="px-3.5 py-2 rounded-lg text-xs font-semibold border {{ ! request('status') ? 'bg-primary text-white border-primary' : 'bg-white text-gray-600 border-gray-200' }}">
                    Semua ({{ $hitung->sum() }})
                </a>
                @foreach (['menunggu' => 'Menunggu', 'aktif' => 'Aktif', 'nonaktif' => 'Nonaktif'] as $status => $label)
                    <a href="{{ route('admin.stores.index', ['status' => $status]) }}"
                       class="px-3.5 py-2 rounded-lg text-xs font-semibold border {{ request('status') === $status ? 'bg-primary text-white border-primary' : 'bg-white text-gray-600 border-gray-200' }}">
                        {{ $label }} ({{ $hitung[$status] ?? 0 }})
                    </a>
                @endforeach
            </div>

            <form method="GET" action="{{ route('admin.stores.index') }}" class="mt-3 flex gap-3">
                @if (request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama toko..."
                       class="flex-grow px-3 py-2.5 rounded-lg border border-gray-300 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                <button type="submit" class="px-5 py-2.5 rounded-lg bg-primary text-white text-sm font-semibold hover:bg-primary-hover">Cari</button>
            </form>

            <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-5">
                @forelse ($stores as $store)
                    @php
                        $warna = match ($store->status) {
                            'aktif' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'nonaktif' => 'bg-red-50 text-danger border-red-200',
                            default => 'bg-amber-50 text-amber-800 border-amber-200',
                        };
                    @endphp

                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-11 h-11 rounded-lg bg-primary-light overflow-hidden flex items-center justify-center text-primary font-bold shrink-0">
                                    @if ($store->logo)
                                        <img src="{{ asset('storage/' . $store->logo) }}" alt="" class="w-full h-full object-cover">
                                    @else
                                        {{ mb_substr($store->nama_toko, 0, 1) }}
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-gray-900 truncate">{{ $store->nama_toko }}</p>
                                    <p class="text-[11px] text-gray-500">{{ $store->kota }} · {{ $store->plants_count }} produk</p>
                                </div>
                            </div>

                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold border shrink-0 {{ $warna }}">
                                {{ $store->status_label }}
                            </span>
                        </div>

                        <p class="mt-3 text-xs text-gray-600 line-clamp-2">{{ $store->deskripsi }}</p>
                        <p class="mt-2 text-[11px] text-gray-500">
                            Pemilik: {{ $store->user->name ?? '—' }} · WA {{ $store->no_wa }}
                        </p>

                        <div class="mt-4 pt-4 border-t border-gray-100 flex flex-wrap gap-2">
                            @if ($store->status !== 'aktif')
                                <form method="POST" action="{{ route('admin.stores.status', $store) }}">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="aktif">
                                    <button class="px-3 py-1.5 rounded-lg text-[11px] font-bold text-white bg-primary hover:bg-primary-hover">Setujui</button>
                                </form>
                            @endif

                            @if ($store->status !== 'nonaktif')
                                <form method="POST" action="{{ route('admin.stores.status', $store) }}">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="nonaktif">
                                    <button class="px-3 py-1.5 rounded-lg text-[11px] font-semibold text-danger bg-red-50 hover:bg-red-100">Nonaktifkan</button>
                                </form>
                            @endif

                            @if ($store->status !== 'menunggu')
                                <form method="POST" action="{{ route('admin.stores.status', $store) }}">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="menunggu">
                                    <button class="px-3 py-1.5 rounded-lg text-[11px] font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200">Kembalikan ke Menunggu</button>
                                </form>
                            @endif

                            <a href="{{ route('stores.show', $store) }}" target="_blank"
                               class="px-3 py-1.5 rounded-lg text-[11px] font-semibold text-primary bg-primary-light hover:bg-emerald-100 ml-auto">
                                Lihat toko
                            </a>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 py-10 text-center lg:col-span-2">Belum ada toko dengan filter ini.</p>
                @endforelse
            </div>

            <div class="mt-6">{{ $stores->links() }}</div>
        </div>
    </div>
@endsection
