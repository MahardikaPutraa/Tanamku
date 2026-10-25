@extends('layouts.app')

@section('title', 'Kelola Pengguna — TanamKu')

@section('content')
    <div class="flex">
        @include('partials.sidebar')

        <div class="flex-1 min-w-0 p-5 lg:p-8">
            @include('partials.alert')

            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Kelola Pengguna</h1>
            <p class="mt-1 text-sm text-gray-600">{{ $users->total() }} akun terdaftar.</p>

            <div class="mt-6 flex flex-wrap gap-2">
                <a href="{{ route('admin.users.index') }}"
                   class="px-3.5 py-2 rounded-lg text-xs font-semibold border {{ ! request('role') ? 'bg-primary text-white border-primary' : 'bg-white text-gray-600 border-gray-200' }}">
                    Semua ({{ $hitung->sum() }})
                </a>
                @foreach (['admin' => 'Admin', 'seller' => 'Penjual', 'buyer' => 'Pembeli'] as $role => $label)
                    <a href="{{ route('admin.users.index', ['role' => $role]) }}"
                       class="px-3.5 py-2 rounded-lg text-xs font-semibold border {{ request('role') === $role ? 'bg-primary text-white border-primary' : 'bg-white text-gray-600 border-gray-200' }}">
                        {{ $label }} ({{ $hitung[$role] ?? 0 }})
                    </a>
                @endforeach
            </div>

            <form method="GET" action="{{ route('admin.users.index') }}" class="mt-3 flex gap-3">
                @if (request('role'))
                    <input type="hidden" name="role" value="{{ request('role') }}">
                @endif
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama atau email..."
                       class="flex-grow px-3 py-2.5 rounded-lg border border-gray-300 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                <button type="submit" class="px-5 py-2.5 rounded-lg bg-primary text-white text-sm font-semibold hover:bg-primary-hover">Cari</button>
            </form>

            <div class="mt-6 bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-left">
                            <tr class="text-[11px] font-semibold text-gray-600 uppercase tracking-wide">
                                <th class="px-5 py-3">Nama</th>
                                <th class="px-5 py-3">Kontak</th>
                                <th class="px-5 py-3 text-center">Peran</th>
                                <th class="px-5 py-3 text-center">Status</th>
                                <th class="px-5 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($users as $user)
                                <tr class="hover:bg-gray-50/60">
                                    <td class="px-5 py-3">
                                        <p class="font-semibold text-gray-900">{{ $user->name }}</p>
                                        <p class="text-[11px] text-gray-500">
                                            {{ $user->email }}
                                            @if ($user->isSeller() && $user->store)
                                                · <span class="text-gray-600">{{ $user->store->nama_toko }}</span>
                                            @endif
                                        </p>
                                    </td>
                                    <td class="px-5 py-3 text-xs text-gray-600">
                                        {{ $user->no_hp ?? '—' }}
                                    </td>
                                    <td class="px-5 py-3 text-center">
                                        @if ($user->id === auth()->id())
                                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-primary-light text-primary border border-primary/20">Admin (Anda)</span>
                                        @else
                                            <form method="POST" action="{{ route('admin.users.role', $user) }}" class="inline-flex">
                                                @csrf
                                                @method('PATCH')
                                                <select name="role" onchange="this.form.submit()"
                                                        class="px-2 py-1 rounded-md border border-gray-300 text-[11px] bg-white outline-none focus:border-primary">
                                                    <option value="buyer" @selected($user->role === 'buyer')>Pembeli</option>
                                                    <option value="seller" @selected($user->role === 'seller')>Penjual</option>
                                                    <option value="admin" @selected($user->role === 'admin')>Admin</option>
                                                </select>
                                            </form>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 text-center">
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $user->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-red-50 text-danger border-red-200' }}">
                                            {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-right">
                                        @if ($user->id !== auth()->id())
                                            <form method="POST" action="{{ route('admin.users.status', $user) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="px-2.5 py-1.5 rounded-md text-[11px] font-semibold {{ $user->is_active ? 'text-danger bg-red-50 hover:bg-red-100' : 'text-emerald-700 bg-emerald-50 hover:bg-emerald-100' }}">
                                                    {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-[11px] text-gray-400">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6">{{ $users->links() }}</div>
        </div>
    </div>
@endsection
