<?php

namespace App\Http\Controllers;

use App\Mail\OtpVerifikasiEmail;
use App\Models\User;
use App\Services\FonnteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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

        // Jika aplikasi berjalan di production, blokir login admin melalui portal publik
        if (app()->environment('production') && $user->role === 'admin') {
            Auth::logout();
            return back()->withErrors(['email' => 'Akun admin tidak dapat masuk melalui portal publik.']);
        }

        return match ($user->role) {
            // Admin masih dapat login pada environment non‑production (dev / testing)
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
     * Registrasi publik untuk buyer & seller.
     * Catatan: Verifikasi nomor HP disimpan untuk pengembangan selanjutnya.
     * Saat ini menggunakan verifikasi email setelah login.
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
                ->with('success', 'Akun penjual berhasil dibuat! Lengkapi profil toko Anda. Jangan lupa verifikasi email akun Anda.');
        }

        return redirect()->route('plants.index')
            ->with('success', 'Akun berhasil dibuat! Selamat datang di TanamKu, ' . $user->name . '. Silakan verifikasi email Anda.');
    }

    /**
     * Tampilkan form input kode OTP WA.
     */
    public function showOtp()
    {
        if (! session()->has('pending_registration')) {
            return redirect()->route('register')
                ->with('error', 'Sesi pendaftaran tidak ditemukan. Silakan isi form pendaftaran terlebih dahulu.');
        }

        $pending = session('pending_registration');

        return view('auth.register-otp', compact('pending'));
    }

    /**
     * Verifikasi kode OTP SMS dan buat akun pengguna.
     */
    public function verifyOtp(Request $request)
    {
        if (! session()->has('pending_registration')) {
            return redirect()->route('register')
                ->with('error', 'Sesi pendaftaran telah kedaluwarsa. Silakan mendaftar kembali.');
        }

        $request->validate([
            'otp' => ['required', 'string'],
        ], [
            'otp.required' => 'Silakan masukkan 6 digit kode OTP SMS yang dikirimkan.',
        ]);

        $savedOtp = (string) session('sms_otp');
        $inputOtp = trim($request->otp);

        if ($inputOtp !== $savedOtp) {
            return back()->withErrors(['otp' => 'Kode OTP salah. Silakan periksa kembali atau tekan kirim ulang.']);
        }

        $data = session('pending_registration');

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'no_hp' => $data['no_hp'],
            'password' => $data['password'],
            'role' => $data['role'],
            'is_active' => true,
        ]);

        session()->forget(['pending_registration', 'sms_otp']);

        Auth::login($user);
        $request->session()->regenerate();

        if ($user->isSeller()) {
            return redirect()->route('seller.store.edit')
                ->with('success', 'Nomor HP berhasil diverifikasi! Akun penjual dibuat. Lengkapi profil toko Anda.');
        }

        return redirect()->route('plants.index')
            ->with('success', 'Nomor HP berhasil diverifikasi! Selamat datang di TanamKu, ' . $user->name . '!');
    }

    /**
     * Kirim ulang kode OTP SMS.
     */
    public function resendOtp()
    {
        if (! session()->has('pending_registration')) {
            return redirect()->route('register')
                ->with('error', 'Sesi pendaftaran telah kedaluwarsa. Silakan mendaftar kembali.');
        }

        $pending = session('pending_registration');
        $otp = (string) mt_rand(100000, 999999);
        session(['sms_otp' => $otp]);

        // Kirim ulang OTP via WhatsApp (Fonnte)
        $pesan = "Halo {$pending['name']}!\n\nKode OTP verifikasi TanamKu Anda (baru) adalah:\n\n*{$otp}*\n\nKode ini berlaku 10 menit.\n\n- Tim TanamKu 🌿";
        app(FonnteService::class)->kirimWa($pending['no_hp'], $pesan);

        return back()->with('info', "Kode OTP baru telah dikirim via WhatsApp ke {$pending['no_hp']}. Silakan cek WA Anda.");
    }

    /**
     * Tampilkan halaman status dan verifikasi email.
     */
    public function showEmailVerification()
    {
        $user = auth()->user();

        return view('auth.verify-email', compact('user'));
    }

    /**
     * Kirim kode OTP verifikasi email via SMTP nyata.
     */
    public function sendEmailVerificationOtp(Request $request)
    {
        $user = auth()->user();

        if ($user->hasVerifiedEmail()) {
            return back()->with('success', 'Email Anda sudah terverifikasi sebelumnya.');
        }

        $otp = (string) mt_rand(100000, 999999);
        session(['email_otp' => $otp]);

        try {
            Mail::to($user->email)->send(new OtpVerifikasiEmail($otp, $user->name, 'verifikasi_email'));
            return back()->with('info', "Kode verifikasi telah dikirim ke email {$user->email}. Silakan cek kotak masuk Anda.");
        } catch (\Throwable $e) {
            Log::error('[Email OTP] Gagal kirim email verifikasi: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengirim email verifikasi. Pastikan konfigurasi SMTP sudah benar di .env.');
        }
    }

    /**
     * Verifikasi kode OTP email dan set email_verified_at.
     */
    public function verifyEmailOtp(Request $request)
    {
        $user = auth()->user();

        if ($user->hasVerifiedEmail()) {
            return back()->with('success', 'Email Anda sudah terverifikasi sebelumnya.');
        }

        $request->validate([
            'otp' => ['required', 'string'],
        ], [
            'otp.required' => 'Silakan masukkan 6 digit kode verifikasi email.',
        ]);

        $savedOtp = (string) session('email_otp');
        $inputOtp = trim($request->otp);

        if (! $savedOtp || $inputOtp !== $savedOtp) {
            return back()->withErrors(['otp' => 'Kode verifikasi email salah atau belum dikirim. Silakan tekan "Kirim Kode Verifikasi".']);
        }

        $user->forceFill([
            'email_verified_at' => now(),
        ])->save();

        session()->forget('email_otp');

        return redirect()->route('verification.notice')
            ->with('success', 'Selamat! Email Anda berhasil diverifikasi. Akun Anda kini berstatus Verified.');
    }

    /**
     * Tampilkan form lupa password.
     */
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    /**
     * Step 1: Kirim kode OTP reset password via email nyata.
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
            'email.exists'   => 'Email tidak terdaftar dalam sistem TanamKu.',
        ]);

        $token = Str::random(60);
        $otp   = (string) mt_rand(100000, 999999);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token'      => $token . ':' . $otp,
                'created_at' => now(),
            ]
        );

        $user = User::where('email', $request->email)->firstOrFail();

        try {
            Mail::to($request->email)->send(new OtpVerifikasiEmail($otp, $user->name, 'reset_password'));
            return redirect()->route('password.otp.form', ['email' => $request->email])
                ->with('success', "Kode 6 digit telah dikirim ke {$request->email}. Silakan masukkan kodenya di bawah.");
        } catch (\Throwable $e) {
            Log::error('[Reset Password] Gagal kirim email: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengirim email reset password. Pastikan konfigurasi SMTP sudah benar di .env.');
        }
    }

    /**
     * Step 2 (Tampilan): Form input kode OTP dari email.
     */
    public function showResetOtp(Request $request)
    {
        $email = $request->query('email', session('reset_email'));
        if (! $email) {
            return redirect()->route('password.request')
                ->with('error', 'Silakan masukkan email Anda terlebih dahulu.');
        }
        return view('auth.reset-otp', compact('email'));
    }

    /**
     * Step 2 (Proses): Cek apakah kode OTP benar.
     * Jika benar, arahkan ke Step 3 (form password baru).
     */
    public function verifyResetOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'otp'   => ['required', 'string', 'size:6'],
        ], [
            'otp.required' => 'Kode verifikasi wajib diisi.',
            'otp.size'     => 'Kode verifikasi harus 6 digit.',
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (! $record) {
            return back()->withErrors(['otp' => 'Permintaan reset password tidak ditemukan. Silakan ajukan ulang.']);
        }

        $parts    = explode(':', $record->token, 2);
        $savedOtp = $parts[1] ?? null;

        if (! $savedOtp || trim($request->otp) !== $savedOtp) {
            return back()->withErrors(['otp' => 'Kode verifikasi salah. Silakan periksa kembali email Anda.']);
        }

        // Cek kedaluwarsa 60 menit
        $createdAt = \Carbon\Carbon::parse($record->created_at);
        if ($createdAt->diffInMinutes(now()) > 60) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['otp' => 'Kode verifikasi sudah kedaluwarsa. Silakan ajukan ulang.']);
        }

        // Simpan sesi bahwa email ini sudah lolos verifikasi OTP
        session(['reset_verified_email' => $request->email]);

        return redirect()->route('password.reset.form', ['email' => $request->email])
            ->with('success', 'Kode verifikasi benar! Silakan masukkan password baru Anda.');
    }

    /**
     * Step 3 (Tampilan): Form buat password baru (hanya bisa dibuka jika OTP sudah lolos).
     */
    public function showResetPassword(Request $request, string $token = '')
    {
        $email = $request->query('email', session('reset_verified_email'));
        if (! $email || session('reset_verified_email') !== $email) {
            return redirect()->route('password.request')
                ->with('error', 'Silakan verifikasi kode OTP terlebih dahulu.');
        }

        return view('auth.reset-password', compact('token', 'email'));
    }

    /**
     * Step 3 (Proses): Simpan password baru ke database.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.min'       => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        if (session('reset_verified_email') !== $request->email) {
            return redirect()->route('password.request')
                ->with('error', 'Sesi reset password tidak valid atau telah kedaluwarsa.');
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $user->forceFill([
            'password' => Hash::make($request->password),
        ])->save();

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();
        session()->forget('reset_verified_email');

        return redirect()->route('login')
            ->with('success', 'Password Anda berhasil diperbarui! Silakan masuk dengan password baru.');
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
