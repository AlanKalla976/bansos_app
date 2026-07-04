<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kriteria;
use App\Models\Penilaian;
use App\Models\Pengajuan;
use App\Services\MooraService;
use Illuminate\Http\Request;

class PenilaianController extends Controller
{
    public function __construct(protected MooraService $mooraService) {}

    // ── Daftar pengajuan Diverifikasi ──
    public function index()
    {
        $pengajuans = Pengajuan::with(['bantuanSosial'])
            ->where('status', 'Diverifikasi')
            ->latest()
            ->get();

        // Cek mana yang sudah dinilai
        $sudahDinilai = Penilaian::distinct()
            ->pluck('pengajuan_id')
            ->toArray();

        return view('admin.penilaian.index', compact('pengajuans', 'sudahDinilai'));
    }

    // ── Form penilaian baru ──
    public function create(Request $request)
    {
        $pengajuan = Pengajuan::with('bantuanSosial')->findOrFail($request->pengajuan_id);
        $kriterias = Kriteria::with('subKriterias')->orderBy('kriteria_id')->get();

        return view('admin.penilaian.create', compact('pengajuan', 'kriterias'));
    }

    // ── Simpan penilaian ──
    public function store(Request $request)
    {
        $request->validate([
            'pengajuan_id'               => 'required|exists:pengajuans,id',
            'penilaian'                  => 'required|array',
            'penilaian.*.kriteria_id'    => 'required|exists:kriterias,kriteria_id',
            'penilaian.*.subkriteria_id' => 'required|exists:sub_kriterias,subkriteria_id',
            'penilaian.*.nilai'          => 'required|numeric',
        ]);

        foreach ($request->penilaian as $item) {
            Penilaian::updateOrCreate(
                [
                    'pengajuan_id' => $request->pengajuan_id,
                    'kriteria_id'  => $item['kriteria_id'],
                ],
                [
                    'subkriteria_id' => $item['subkriteria_id'],
                    'nilai'          => $item['nilai'],
                ]
            );
        }

        return redirect()->route('admin.penilaian.index')
            ->with('success', 'Penilaian berhasil disimpan.');
    }

    // ── Form edit penilaian ──
    public function edit(Pengajuan $pengajuan)
    {
        $pengajuan->load('bantuanSosial');
        $kriterias  = Kriteria::with('subKriterias')->orderBy('kriteria_id')->get();
        $penilaians = Penilaian::where('pengajuan_id', $pengajuan->id)
            ->get()
            ->keyBy('kriteria_id');

        return view('admin.penilaian.edit', compact('pengajuan', 'kriterias', 'penilaians'));
    }

    // ── Hitung MOORA ──
    public function hitungMoora()
    {
        $hasil = $this->mooraService->hitung();

        if ($hasil['error']) {
            return redirect()->route('admin.penilaian.index')
                ->with('error', $hasil['error']);
        }

        // Konversi agar aman di session (tanpa batasLayak — kelayakan ditentukan di view: yi >= 0.35)
        $hasilSession = [
            'kriterias'   => $hasil['kriterias']->map(fn($k) => [
                'kriteria_id'   => $k->kriteria_id,
                'kode_kriteria' => $k->kode_kriteria,
                'nama'          => $k->nama,
                'bobot'         => $k->bobot,
                'tipe'          => $k->tipe,
            ])->toArray(),
            'pengajuans'  => $hasil['pengajuans']->map(fn($p) => [
                'id'            => $p->id,
                'nama'          => $p->nama,
                'nik'           => $p->nik,
                'jenis_bantuan' => $p->bantuanSosial->nama_bantuan ?? '-',
            ])->toArray(),
            'matrix'      => $hasil['matrix'],
            'akarKuadrat' => $hasil['akarKuadrat'],
            'normalized'  => $hasil['normalized'],
            'yi'          => $hasil['yi'],
            'ranked'      => array_map(fn($r) => [
                'index'         => $r['index'],
                'nama'          => $r['pengajuan']->nama,
                'nik'           => $r['pengajuan']->nik,
                'jenis_bantuan' => $r['pengajuan']->bantuanSosial->nama_bantuan ?? '-',
                'yi'            => $r['yi'],
            ], $hasil['ranked']),
            'n' => $hasil['n'],
            'm' => $hasil['m'],
        ];

        return redirect()->route('admin.penilaian.index')
            ->with('success', 'Perhitungan MOORA berhasil! Hasil telah disimpan.')
            ->with('hasil_moora', $hasilSession);
    }
}