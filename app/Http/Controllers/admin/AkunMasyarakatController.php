<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AkunMasyarakat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AkunMasyarakatController extends Controller
{
    /**
     * Tampilkan daftar akun.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');

        $akun = AkunMasyarakat::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('nik', 'like', "%{$search}%")
                        ->orWhere('role', 'like', "%{$search}%");
                });
            })
            ->orderBy('users_id', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.akunmasyarakat.index', compact('akun', 'search'));
    }

    /**
     * Tampilkan form tambah akun.
     */
    public function create()
    {
        return view('admin.akunmasyarakat.create');
    }

    /**
     * Simpan akun baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nik'      => ['required', 'digits:16', 'unique:users,nik'],
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'nik.required'       => 'NIK wajib diisi.',
            'nik.digits'         => 'NIK harus 16 digit.',
            'nik.unique'         => 'NIK sudah terdaftar.',
            'name.required'      => 'Nama wajib diisi.',
            'email.required'     => 'Email wajib diisi.',
            'email.unique'       => 'Email sudah terdaftar.',
            'password.required'  => 'Password wajib diisi.',
            'password.min'       => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        AkunMasyarakat::create([
            'nik'      => $validated['nik'],
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'masyarakat',
        ]);

        return redirect()
            ->route('admin.akunmasyarakat.index')
            ->with('success', 'Akun berhasil ditambahkan.');
    }

    /**
     * Tampilkan detail akun.
     */
    public function show(AkunMasyarakat $akunmasyarakat)
    {
        return view('admin.akunmasyarakat.show', compact('akunmasyarakat'));
    }

    /**
     * Tampilkan form edit akun.
     */
    public function edit(AkunMasyarakat $akunmasyarakat)
    {
        return view('admin.akunmasyarakat.edit', compact('akunmasyarakat'));
    }

    /**
     * Update akun.
     */
    public function update(Request $request, AkunMasyarakat $akunmasyarakat)
    {
        $validated = $request->validate([
            'nik' => [
                'required',
                'digits:16',
                Rule::unique('users', 'nik')->ignore($akunmasyarakat->users_id, 'users_id'),
            ],
            'name' => [
                'required',
                'string',
                'max:100',
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($akunmasyarakat->users_id, 'users_id'),
            ],
            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],
        ], [
            'nik.required'       => 'NIK wajib diisi.',
            'nik.digits'         => 'NIK harus 16 digit.',
            'nik.unique'         => 'NIK sudah terdaftar.',
            'name.required'      => 'Nama wajib diisi.',
            'email.required'     => 'Email wajib diisi.',
            'email.unique'       => 'Email sudah terdaftar.',
            'password.min'       => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $data = [
            'nik'   => $validated['nik'],
            'name'  => $validated['name'],
            'email' => $validated['email'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $akunmasyarakat->update($data);

        return redirect()
            ->route('admin.akunmasyarakat.index')
            ->with('success', 'Akun berhasil diperbarui.');
    }

    /**
     * Hapus akun.
     */
    public function destroy(AkunMasyarakat $akunmasyarakat)
    {
        $akunmasyarakat->delete();

        return redirect()
            ->route('admin.akunmasyarakat.index')
            ->with('success', 'Akun berhasil dihapus.');
    }
}