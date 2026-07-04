<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BantuanSosial;
use Illuminate\Http\Request;

class BantuanSosialController extends Controller
{
    public function index()
    {
        $bantuans = BantuanSosial::latest()->paginate(10);
        return view('admin.bantuansosial.index', compact('bantuans'));
    }

    public function create()
    {
        return view('admin.bantuansosial.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_bantuan' => 'required|string|max:100',
            'deskripsi'    => 'nullable|string',
        ]);

        BantuanSosial::create($request->only('nama_bantuan', 'deskripsi'));

        return redirect()->route('admin.bantuansosial.index')
                         ->with('success', 'Data bantuan sosial berhasil ditambahkan.');
    }

    public function edit(BantuanSosial $bantuansosial)
    {
        return view('admin.bantuansosial.edit', compact('bantuansosial'));
    }

    public function update(Request $request, BantuanSosial $bantuansosial)
    {
        $request->validate([
            'nama_bantuan' => 'required|string|max:100',
            'deskripsi'    => 'nullable|string',
        ]);

        $bantuansosial->update($request->only('nama_bantuan', 'deskripsi'));

        return redirect()->route('admin.bantuansosial.index')
                         ->with('success', 'Data bantuan sosial berhasil diperbarui.');
    }

    public function destroy(BantuanSosial $bantuansosial)
    {
        $bantuansosial->delete();

        return redirect()->route('admin.bantuansosial.index')
                         ->with('success', 'Data bantuan sosial berhasil dihapus.');
    }
}