<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kriteria;
use App\Models\SubKriteria;
use Illuminate\Http\Request;

class SubKriteriaController extends Controller
{
    public function index(Request $request)
    {
        $kriterias    = Kriteria::orderBy('kriteria_id')->get();
        $kriteriaId   = $request->get('kriteria_id');

        $query = SubKriteria::with('kriteria')->orderBy('kriteria_id');

        if ($kriteriaId) {
            $query->where('kriteria_id', $kriteriaId);
        }

        $subKriterias = $query->get();

        return view('admin.subkriteria.index', compact('kriterias', 'subKriterias', 'kriteriaId'));
    }

    public function create()
    {
        $kriterias = Kriteria::orderBy('kriteria_id')->get();
        return view('admin.subkriteria.create', compact('kriterias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kriteria_id' => 'required|exists:kriterias,kriteria_id',
            'nama'        => 'required|string|max:100',
            'nilai'       => 'required|numeric',
        ]);

        SubKriteria::create([
            'kriteria_id' => $request->kriteria_id,
            'nama'        => $request->nama,
            'nilai'       => (float) $request->nilai,
        ]);

        return redirect()->route('admin.subkriteria.index')
            ->with('success', 'Sub Kriteria berhasil ditambahkan.');
    }

    public function show(SubKriteria $subkriteria)
    {
        $subkriteria->load('kriteria');
        return view('admin.subkriteria.show', compact('subkriteria'));
    }

    public function edit(SubKriteria $subkriteria)
    {
        $subkriteria->load('kriteria');
        $kriterias = Kriteria::orderBy('kriteria_id')->get();
        return view('admin.subkriteria.edit', compact('subkriteria', 'kriterias'));
    }

    public function update(Request $request, SubKriteria $subkriteria)
    {
        $request->validate([
            'kriteria_id' => 'required|exists:kriterias,kriteria_id',
            'nama'        => 'required|string|max:100',
            'nilai'       => 'required|numeric',
        ]);

        $subkriteria->update([
            'kriteria_id' => $request->kriteria_id,
            'nama'        => $request->nama,
            'nilai'       => (float) $request->nilai,
        ]);

        return redirect()->route('admin.subkriteria.index')
            ->with('success', 'Sub Kriteria berhasil diperbarui.');
    }

    public function destroy(SubKriteria $subkriteria)
    {
        $subkriteria->delete();

        return redirect()->route('admin.subkriteria.index')
            ->with('success', 'Sub Kriteria berhasil dihapus.');
    }
}