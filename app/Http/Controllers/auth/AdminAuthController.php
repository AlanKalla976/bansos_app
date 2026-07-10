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
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
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
                     ->where('role', 'admin')
                     ->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Email atau password salah.']);
        }

        // PENTING: selalu sebutkan guard secara eksplisit
        Auth::guard('admin')->login($admin, $request->boolean('remember'));

        return redirect()->route('admin.dashboard')
                         ->with('success', 'Login berhasil! Selamat datang, ' . $admin->name . '.');
    }

    public function logout(Request $request)
    {
        // PENTING: hanya logout guard admin, JANGAN panggil Auth::logout() tanpa guard
        Auth::guard('admin')->logout();

        // JANGAN pakai $request->session()->invalidate() di sini kalau mau guard lain
        // (misal web) tetap login, karena invalidate() akan menghapus SELURUH session,
        // termasuk punya guard lain dalam satu browser/tab yang sama.
        // Cukup regenerate token CSRF saja:
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')
                         ->with('success', 'Anda berhasil logout.');
    }
}