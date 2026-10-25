@extends('layouts.app')

@section('title', 'Semua Pesanan — TanamKu')

@section('content')
    <div class="flex">
        @include('partials.sidebar')

        <div class="flex-1 min-w-0 p-5 lg:p-8">
            @include('partials.alert')

            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Semua Pesanan</h1>
            <p class="mt-1 text-sm text-gray-600">{{ $orders->total() }} pesanan tercatat di platform.</p>

            <div class="mt-6 flex flex-wrap gap-2">
                <a href="{{ route('admin.orders.index') }}"
                   class="px-3.5 py-2 rounded-lg text-xs font-semibold border {{ ! request('status') ? 'bg-primary text-white border-primary' : 'bg-white text-gray-600 border-gray-200' }}">
                    Semua ({{ $hitung->sum() }})
                </a>
                @foreach (['baru', 'diproses', 'selesai', 'batal'] as $status)
                    <a href="{{ route('admin.orders.index', ['status' => $status]) }}"
                       class="px-3.5 py-2 rounded-lg text-xs font-semibold border {{ request('status') === $status ? 'bg-primary text-white border-primary' : 'bg-white text-gray-600 border-gray-200' }}">
                        {{ ucfirst($status) }} ({{ $hitung[$status] ?? 0 }})
                    </a>
                @endforeach
            </div>

            <form method="GET" action="{{ route('admin.orders.index') }}" class="mt-3 flex flex-col sm:flex-row gap-3">
                @if (request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif

                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari kode pesanan..."
                       class="flex-grow px-3 py-2.5 rounded-lg border border-gray-300 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary">

                <select name="toko" class="px-3 py-2.5 rounded-lg border border-gray-300 text-sm bg-white outline-none sm:w-52">
                    <option value="">Semua toko</option>
                    @foreach ($stores as $toko)
                        <option value="{{ $toko->id }}" @selected((string) request('toko') === (string) $toko->id)>{{ $toko->nama_toko }}</option>
                    @endforeach
                </select>

                <button type="submit" class="px-5 py-2.5 rounded-lg bg-primary text-white text-sm font-semibold hover:bg-primary-hover">Filter</button>
            </form>

            <div class="mt-6 bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-left">
                            <tr class="text-[11px] font-semibold text-gray-600 uppercase tracking-wide">
                                <th class="px-5 py-3">Kode</th>
                                <th class="px-5 py-3">Toko</th>
                                <th class="px-5 py-3">Pembeli</th>
                                <th class="px-5 py-3 text-right">Total</th>
                                <th class="px-5 py-3 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($orders as $order)
                                <tr class="hover:bg-gray-50/60">
                                    <td class="px-5 py-3">
                                        <a href="{{ route('orders.show', $order) }}" class="font-mono text-xs font-semibold text-gray-800 hover:text-primary">
                                            {{ $order->kode_pesanan }}
                                        </a>
                                        <p class="text-[11px] text-gray-500">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                                    </td>
                                    <td class="px-5 py-3 text-gray-700 text-xs">{{ $order->store->nama_toko }}</td>
                                    <td class="px-5 py-3 text-gray-700 text-xs">
                                        {{ $order->nama_penerima }}
                                        <span class="block text-[11px] text-gray-400">{{ $order->buyer->email ?? 'tamu' }}</span>
                                    </td>
                                    <td class="px-5 py-3 text-right font-semibold text-gray-900 whitespace-nowrap">{{ $order->total_harga_rupiah }}</td>
                                    <td class="px-5 py-3 text-center">
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $order->status_badge }}">{{ $order->status_label }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-5 py-14 text-center text-sm text-gray-500">Tidak ada pesanan dengan filter ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6">{{ $orders->links() }}</div>
        </div>
    </div>
@endsection
