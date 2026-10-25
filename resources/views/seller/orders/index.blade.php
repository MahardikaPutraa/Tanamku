@extends('layouts.app')

@section('title', 'Pesanan Masuk — TanamKu')

@php
    $alur = [
        'baru' => ['diproses', 'batal'],
        'diproses' => ['selesai', 'batal'],
        'selesai' => [],
        'batal' => [],
    ];
@endphp

@section('content')
    <div class="flex">
        @include('partials.sidebar')

        <div class="flex-1 min-w-0 p-5 lg:p-8">
            @include('partials.alert')

            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Pesanan Masuk</h1>
            <p class="mt-1 text-sm text-gray-600">{{ $orders->total() }} pesanan untuk toko {{ $store->nama_toko }}.</p>

            <div class="mt-6 flex flex-wrap gap-2">
                <a href="{{ route('seller.orders.index') }}"
                   class="px-3.5 py-2 rounded-lg text-xs font-semibold border transition-colors {{ ! request('status') ? 'bg-primary text-white border-primary' : 'bg-white text-gray-600 border-gray-200 hover:border-gray-300' }}">
                    Semua ({{ $hitung->sum() }})
                </a>
                @foreach (['baru', 'diproses', 'selesai', 'batal'] as $status)
                    <a href="{{ route('seller.orders.index', ['status' => $status]) }}"
                       class="px-3.5 py-2 rounded-lg text-xs font-semibold border transition-colors {{ request('status') === $status ? 'bg-primary text-white border-primary' : 'bg-white text-gray-600 border-gray-200 hover:border-gray-300' }}">
                        {{ ucfirst($status) }} ({{ $hitung[$status] ?? 0 }})
                    </a>
                @endforeach
            </div>

            <div class="mt-6 bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-left">
                            <tr class="text-[11px] font-semibold text-gray-600 uppercase tracking-wide">
                                <th class="px-5 py-3">Kode</th>
                                <th class="px-5 py-3">Pembeli</th>
                                <th class="px-5 py-3">Produk</th>
                                <th class="px-5 py-3 text-right">Total</th>
                                <th class="px-5 py-3 text-center">Status</th>
                                <th class="px-5 py-3 text-right">Ubah Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($orders as $order)
                                <tr class="hover:bg-gray-50/60 align-top">
                                    <td class="px-5 py-3">
                                        <a href="{{ route('orders.show', $order) }}" class="font-mono text-xs font-semibold text-gray-800 hover:text-primary">
                                            {{ $order->kode_pesanan }}
                                        </a>
                                        <p class="text-[11px] text-gray-500 mt-0.5">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                                    </td>
                                    <td class="px-5 py-3">
                                        <p class="font-medium text-gray-900">{{ $order->nama_penerima }}</p>
                                        <p class="text-[11px] text-gray-500">{{ $order->no_hp }}</p>
                                        <p class="text-[11px] text-gray-400 max-w-[13rem] truncate" title="{{ $order->alamat }}">{{ $order->alamat }}</p>
                                    </td>
                                    <td class="px-5 py-3 text-gray-700">
                                        @foreach ($order->items as $item)
                                            <p class="text-xs">{{ $item->jumlah }}× {{ $item->nama_produk }}</p>
                                        @endforeach
                                    </td>
                                    <td class="px-5 py-3 text-right font-semibold text-gray-900 whitespace-nowrap">{{ $order->total_harga_rupiah }}</td>
                                    <td class="px-5 py-3 text-center">
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $order->status_badge }}">{{ $order->status_label }}</span>
                                    </td>
                                    <td class="px-5 py-3">
                                        @php $lanjut = $alur[$order->status] ?? []; @endphp
                                        @if (empty($lanjut))
                                            <p class="text-[11px] text-gray-400 text-right">Final</p>
                                        @else
                                            <form method="POST" action="{{ route('seller.orders.status', $order) }}" class="flex items-center justify-end gap-2">
                                                @csrf
                                                @method('PATCH')
                                                <select name="status" class="px-2 py-1.5 rounded-md border border-gray-300 text-[11px] bg-white outline-none focus:border-primary">
                                                    @foreach ($lanjut as $next)
                                                        <option value="{{ $next }}">{{ ucfirst($next) }}</option>
                                                    @endforeach
                                                </select>
                                                <button type="submit" class="px-2.5 py-1.5 rounded-md text-[11px] font-semibold text-primary bg-primary-light hover:bg-emerald-100">Ubah</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-5 py-14 text-center text-sm text-gray-500">Belum ada pesanan dengan filter ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6">{{ $orders->links() }}</div>
        </div>
    </div>
@endsection
