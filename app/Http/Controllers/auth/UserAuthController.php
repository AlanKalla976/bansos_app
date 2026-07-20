<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserAuthController extends Controller
{
    // ── Login ──────────────────────────────────────────────

    public function showLogin()
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('user.dashboard');
        }
        return view('auth.user.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ], [
            'login.required'    => 'Email atau NIK wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $loginInput = trim($request->login);

        // Deteksi otomatis: kalau inputnya berupa 16 digit angka, anggap sebagai NIK.
        // Selain itu (mengandung huruf/simbol seperti '@'), anggap sebagai email.
        $isNik = ctype_digit($loginInput) && strlen($loginInput) === 16;

        $query = User::where('role', 'masyarakat');

        if ($isNik) {
            $query->where('nik', $loginInput);
        } else {
            $query->where('email', $loginInput);
        }

        $user = $query->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()
                ->withInput($request->only('login'))
                ->withErrors(['login' => 'Email/NIK atau password salah.']);
        }

        // PENTING: selalu sebutkan guard secara eksplisit
        Auth::guard('web')->login($user, $request->boolean('remember'));

        return redirect()->route('user.dashboard')
                         ->with('success', 'Login berhasil! Selamat datang, ' . $user->name . '.');
    }

    // ── Register ───────────────────────────────────────────

    public function showRegister()
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('user.dashboard');
        }
        return view('auth.user.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nik'                => 'required|digits:16|unique:users,nik',
            'name'               => 'required|string|max:100',
            'email'              => 'required|email|unique:users,email',
            'password'           => 'required|string|min:6|confirmed',
        ], [
            'nik.required'       => 'NIK wajib diisi.',
            'nik.digits'         => 'NIK harus tepat 16 digit.',
            'nik.unique'         => 'NIK sudah terdaftar.',
            'name.required'      => 'Nama lengkap wajib diisi.',
            'email.required'     => 'Email wajib diisi.',
            'email.email'        => 'Format email tidak valid.',
            'email.unique'       => 'Email sudah terdaftar.',
            'password.required'  => 'Password wajib diisi.',
            'password.min'       => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = User::create([
            'nik'      => $request->nik,
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'masyarakat',
        ]);

        // Tidak auto-login, arahkan ke halaman login
        return redirect()->route('user.login')
                         ->with('success', 'Registrasi berhasil! Silakan login dengan akun Anda, ' . $user->name . '.');
    }

    // ── Logout ─────────────────────────────────────────────

    public function logout(Request $request)
    {
        // PENTING: hanya logout guard web, JANGAN panggil Auth::logout() tanpa guard
        Auth::guard('web')->logout();

        // Jangan invalidate() total kalau ingin guard admin tetap aman
        $request->session()->regenerateToken();

        return redirect()->route('user.login')
                         ->with('success', 'Anda berhasil logout.');
    }
}