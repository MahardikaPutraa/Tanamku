<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Login satu pintu, lalu diarahkan sesuai peran.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'Email atau password salah.'])
                ->onlyInput('email');
        }

        if (! Auth::user()->is_active) {
            Auth::logout();

            return back()
                ->withErrors(['email' => 'Akun Anda sedang dinonaktifkan oleh admin.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();
        $user = Auth::user();

        return match ($user->role) {
            'admin' => redirect()->intended(route('admin.dashboard'))
                ->with('success', 'Selamat datang, ' . $user->name . '.'),
            'seller' => $user->store
                ? redirect()->intended(route('seller.dashboard'))
                    ->with('success', 'Selamat datang, ' . $user->name . '.')
                : redirect()->route('seller.store.edit')
                    ->with('success', 'Lengkapi profil toko Anda terlebih dahulu.'),
            default => redirect($this->tujuanPembeli())
                ->with('success', 'Selamat datang, ' . $user->name . '.'),
        };
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Registrasi publik hanya untuk buyer & seller.
     * Peran admin tidak pernah bisa dipilih dari form.
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'no_hp' => ['required', 'string', 'max:20', 'regex:/^[0-9\+\-\s]{8,20}$/'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:buyer,seller'],
        ], [
            'email.unique' => 'Email ini sudah terdaftar. Silakan masuk.',
            'no_hp.regex' => 'Nomor HP hanya boleh berisi angka, spasi, tanda + atau -.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'role.in' => 'Peran yang dipilih tidak valid.',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'no_hp' => $data['no_hp'],
            'password' => $data['password'],
            'role' => $data['role'],
            'is_active' => true,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        if ($user->isSeller()) {
            return redirect()->route('seller.store.edit')
                ->with('success', 'Akun penjual dibuat. Lengkapi profil toko agar bisa mulai berjualan.');
        }

        return redirect()->route('plants.index')
            ->with('success', 'Akun berhasil dibuat. Selamat berbelanja, ' . $user->name . '!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Anda telah keluar.');
    }

    /**
     * Jangan arahkan pembeli kembali ke endpoint POST (mis. /checkout).
     */
    private function tujuanPembeli(): string
    {
        $tujuan = session()->pull('url.intended', route('home'));

        return (str_contains($tujuan, '/checkout') || str_contains($tujuan, '/keranjang'))
            ? route('carts.index')
            : $tujuan;
    }
}
