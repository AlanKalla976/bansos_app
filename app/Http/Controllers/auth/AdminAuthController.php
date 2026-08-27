<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $admin = User::where('email', $request->email)
                     ->whereIn('role', ['admin', 'petugas', 'lurah'])
                     ->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Email atau password salah.']);
        }

        // Tentukan guard secara dinamis berdasarkan role user
        $guard = $admin->role; 
        Auth::guard($guard)->login($admin, $request->boolean('remember'));

        if ($guard === 'petugas') {
            return redirect()->route('admin.petugas.dashboard')
                             ->with('success', 'Login berhasil! Selamat datang, ' . $admin->name . '.');
        } elseif ($guard === 'lurah') {
            return redirect()->route('admin.lurah.dashboard')
                             ->with('success', 'Login berhasil! Selamat datang, ' . $admin->name . '.');
        }

        return redirect()->route('admin.dashboard')
                         ->with('success', 'Login berhasil! Selamat datang, ' . $admin->name . '.');
    }

    public function logout(Request $request)
    {
        // Logout guard yang sedang aktif
        foreach (['admin', 'petugas', 'lurah'] as $g) {
            if (Auth::guard($g)->check()) {
                Auth::guard($g)->logout();
            }
        }

        $request->session()->regenerateToken();

        return redirect()->route('admin.login')
                         ->with('success', 'Anda berhasil logout.');
    }
}