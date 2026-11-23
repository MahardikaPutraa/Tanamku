@extends('layouts.app')

@section('title', 'Kelola Pengguna — TanamKu')

@section('content')
    <div class="flex flex-col md:flex-row min-h-screen">
        @include('partials.sidebar')

        <div class="flex-1 min-w-0 p-4 sm:p-6 lg:p-8">
            @include('partials.alert')

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Kelola Pengguna</h1>
                    <p class="mt-1 text-sm text-slate-500">{{ $users->total() }} akun terdaftar dalam sistem TanamKu.</p>
                </div>
            </div>

            {{-- Role Filter Tabs --}}
            <div class="mt-6 flex flex-wrap gap-2">
                <a href="{{ route('admin.users.index') }}"
                   class="px-4 py-2 rounded-xl text-xs font-bold border transition-all {{ ! request('role') ? 'bg-primary text-white border-primary shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:border-slate-300' }}">
                    Semua ({{ $hitung->sum() }})
                </a>
                @foreach (['admin' => 'Admin Platform', 'seller' => 'Penjual Mitra', 'buyer' => 'Pembeli'] as $role => $label)
                    <a href="{{ route('admin.users.index', ['role' => $role]) }}"
                       class="px-4 py-2 rounded-xl text-xs font-bold border transition-all {{ request('role') === $role ? 'bg-primary text-white border-primary shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:border-slate-300' }}">
                        {{ $label }} ({{ $hitung[$role] ?? 0 }})
                    </a>
                @endforeach
            </div>

            {{-- Form Pencarian --}}
            <form method="GET" action="{{ route('admin.users.index') }}" class="mt-4 flex flex-col sm:flex-row gap-2.5">
                @if (request('role'))
                    <input type="hidden" name="role" value="{{ request('role') }}">
                @endif
                <div class="relative flex-grow">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama atau alamat email pengguna..."
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 bg-white transition">
                </div>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary text-white text-xs font-bold shadow-xs hover:bg-primary-hover hover:shadow transition-all">Cari</button>
                @if (request('q'))
                    <a href="{{ route('admin.users.index', request()->only('role')) }}" class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors text-center">Reset</a>
                @endif
            </form>

            {{-- Tabel Pengguna --}}
            <div class="mt-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm min-w-[650px]">
                        <thead class="bg-slate-50/80 text-left border-b border-slate-100">
                            <tr class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                                <th class="px-5 py-3.5">Nama & Akun</th>
                                <th class="px-5 py-3.5">Kontak WhatsApp</th>
                                <th class="px-5 py-3.5 text-center">Peran</th>
                                <th class="px-5 py-3.5 text-center">Status</th>
                                <th class="px-5 py-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($users as $user)
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl bg-slate-100 border border-slate-200/80 flex items-center justify-center font-bold text-xs text-slate-700 shrink-0">
                                                {{ mb_substr($user->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-900 text-xs">{{ $user->name }}</p>
                                                <p class="text-[11px] text-slate-500">
                                                    {{ $user->email }}
                                                    @if ($user->isSeller() && $user->store)
                                                        · <span class="text-primary font-bold">🏪 {{ $user->store->nama_toko }}</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5 text-xs text-slate-600 font-mono">
                                        {{ $user->no_hp ?? '—' }}
                                    </td>
                                    <td class="px-5 py-3.5 text-center">
                                        @if ($user->id === auth()->id())
                                            <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-bold bg-primary/10 text-primary border border-primary/20">Admin (Anda)</span>
                                        @else
                                            <form method="POST" action="{{ route('admin.users.role', $user) }}" class="inline-flex">
                                                @csrf
                                                @method('PATCH')
                                                <select name="role" onchange="this.form.submit()"
                                                        class="px-2.5 py-1 rounded-lg border border-slate-200 text-xs font-semibold bg-white text-slate-700 outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition cursor-pointer">
                                                    <option value="buyer" @selected($user->role === 'buyer')>Pembeli</option>
                                                    <option value="seller" @selected($user->role === 'seller')>Penjual</option>
                                                    <option value="admin" @selected($user->role === 'admin')>Admin</option>
                                                </select>
                                            </form>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-center">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold border {{ $user->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-red-50 text-red-700 border-red-200' }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $user->is_active ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                                            {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 text-right">
                                        @if ($user->id !== auth()->id())
                                            <form method="POST" action="{{ route('admin.users.status', $user) }}" class="inline-block">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $user->is_active ? 'text-red-600 bg-red-50 hover:bg-red-100 border border-red-200' : 'text-primary bg-primary/10 hover:bg-primary/20 border border-primary/20' }}">
                                                    {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs text-slate-400 font-medium">Terkunci</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($users->hasPages())
                    <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/50">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
