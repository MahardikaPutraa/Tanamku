<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with('store')
            ->when($request->filled('role'), fn ($q) => $q->where('role', $request->role))
            ->when($request->filled('q'), function ($q) use ($request) {
                $q->where(fn ($w) => $w
                    ->where('name', 'like', '%' . $request->q . '%')
                    ->orWhere('email', 'like', '%' . $request->q . '%'));
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $hitung = User::selectRaw('role, count(*) as jumlah')->groupBy('role')->pluck('jumlah', 'role');

        return view('admin.users.index', compact('users', 'hitung'));
    }

    /**
     * Ubah peran akun. Admin tidak bisa mengubah perannya sendiri
     * agar tidak kehilangan akses tanpa sengaja.
     */
    public function updateRole(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa mengubah peran akun sendiri.');
        }

        $data = $request->validate([
            'role' => ['required', 'in:admin,seller,buyer'],
        ]);

        $user->update(['role' => $data['role']]);

        return back()->with('success', 'Peran ' . $user->name . ' diubah menjadi ' . $user->role_label . '.');
    }

    /**
     * Aktifkan / nonaktifkan akun.
     */
    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menonaktifkan akun sendiri.');
        }

        $user->update(['is_active' => ! $user->is_active]);

        return back()->with('success', $user->name . ' sekarang ' . ($user->is_active ? 'aktif' : 'nonaktif') . '.');
    }
}
