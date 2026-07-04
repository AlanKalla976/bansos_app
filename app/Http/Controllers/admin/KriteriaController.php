<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kriteria;
use App\Models\PerbandinganKriteria;
use App\Services\AhpService;
use Illuminate\Http\Request;

class KriteriaController extends Controller
{
    public function __construct(protected AhpService $ahpService) {}

    // ── Halaman utama ──────────────────────────────────────────────────────
    public function index()
    {
        $kriterias    = Kriteria::orderBy('kriteria_id')->get();

        // Ambil semua perbandingan yang sudah tersimpan, key-kan untuk lookup cepat
        $perbandingan = PerbandinganKriteria::with(['kriteriaPertama', 'kriteriaKedua'])
            ->get()
            ->keyBy(fn($p) => $p->kriteria_pertama_id . '_' . $p->kriteria_kedua_id);

        // Buat daftar semua pasangan kombinasi (i < j) agar tidak duplikat
        $pasangan = [];
        $list     = $kriterias->values();
        for ($i = 0; $i < $list->count(); $i++) {
            for ($j = $i + 1; $j < $list->count(); $j++) {
                $pasangan[] = [
                    'pertama' => $list[$i],
                    'kedua'   => $list[$j],
                ];
            }
        }

        return view('admin.kriteria.index', compact('kriterias', 'pasangan', 'perbandingan'));
    }

    // ── Tambah Kriteria ────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'tipe' => 'required|in:benefit,cost',
        ]);

        // Kode otomatis: C1, C2, C3, ... berdasarkan jumlah kriteria + 1
        $urutan       = Kriteria::count() + 1;
        $kodeKriteria = 'C' . $urutan;

        Kriteria::create([
            'kode_kriteria' => $kodeKriteria,
            'nama'          => $request->nama,
            'tipe'          => $request->tipe,
            'bobot'         => 0,
        ]);

        return back()->with('success', 'Kriteria berhasil ditambahkan. Perbarui perbandingan dan hitung ulang AHP.');
    }

    // ── Update Kriteria ────────────────────────────────────────────────────
    public function update(Request $request, Kriteria $kriteria)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'tipe' => 'required|in:benefit,cost',
        ]);

        $kriteria->update($request->only('nama', 'tipe'));

        return back()->with('success', 'Kriteria berhasil diperbarui.');
    }

    // ── Hapus Kriteria ─────────────────────────────────────────────────────
    public function destroy(Kriteria $kriteria)
    {
        // Hapus semua perbandingan yang melibatkan kriteria ini (cascadeOnDelete pun ada, tapi eksplisit lebih aman)
        PerbandinganKriteria::where('kriteria_pertama_id', $kriteria->kriteria_id)
            ->orWhere('kriteria_kedua_id', $kriteria->kriteria_id)
            ->delete();

        $kriteria->delete();

        // Reindex kode C1, C2, C3, ... agar selalu berurutan
        $this->reindexKode();

        return back()->with('success', 'Kriteria berhasil dihapus. Reindeks kode kriteria dilakukan otomatis.');
    }

    // ── Simpan Perbandingan ────────────────────────────────────────────────
    public function simpanPerbandingan(Request $request)
    {
        $request->validate([
            'perbandingan'         => 'required|array',
            'perbandingan.*.id_a'  => 'required|exists:kriterias,kriteria_id',
            'perbandingan.*.id_b'  => 'required|exists:kriterias,kriteria_id',
            'perbandingan.*.nilai' => 'required|numeric|min:0.1111|max:9',
        ]);

        foreach ($request->perbandingan as $item) {
            $idA = (int) $item['id_a'];
            $idB = (int) $item['id_b'];

            // Jaga agar id_a selalu < id_b untuk menghindari duplikat terbalik
            if ($idA === $idB) {
                continue;
            }

            // Simpan/update dengan updateOrCreate (key: pasangan unik)
            PerbandinganKriteria::updateOrCreate(
                [
                    'kriteria_pertama_id' => $idA,
                    'kriteria_kedua_id'   => $idB,
                ],
                ['nilai_perbandingan' => (float) $item['nilai']]
            );
        }

        return back()->with('success', 'Data perbandingan berhasil disimpan. Klik "Hitung AHP" untuk menghitung bobot.');
    }

    // ── Hitung AHP ─────────────────────────────────────────────────────────
    public function hitungAhp()
    {
        $hasil = $this->ahpService->hitung();

        if ($hasil['error']) {
            return back()->with('error', $hasil['error']);
        }

        // Serialisasi Collection ke plain array agar tidak corrupt di session
        $hasilSession = [
            'kriterias'   => $hasil['kriterias']->map(fn($k) => [
                'kriteria_id'   => $k->kriteria_id,
                'kode_kriteria' => $k->kode_kriteria,
                'nama'          => $k->nama,
                'tipe'          => $k->tipe,
                'bobot'         => $k->bobot,
            ])->toArray(),
            'n'           => $hasil['n'],
            'matrix'      => $hasil['matrix'],
            'jumlahKolom' => $hasil['jumlahKolom'],
            'normalized'  => $hasil['normalized'],
            'eigenVector' => $hasil['eigenVector'],
            'weightedSum' => $hasil['weightedSum'],
            'lambdaMax'   => $hasil['lambdaMax'],
            'ci'          => $hasil['ci'],
            'ri'          => $hasil['ri'],
            'cr'          => $hasil['cr'],
            'konsisten'   => $hasil['konsisten'],
        ];

        if ($hasil['konsisten']) {
            $this->ahpService->simpanBobot($hasil['eigenVector'], $hasil['kriterias']);

            return back()
                ->with('success', 'Perbandingan Konsisten! Bobot kriteria berhasil disimpan ke database.')
                ->with('hasil_ahp', $hasilSession);
        }

        return back()
            ->with('error', 'Perbandingan Tidak Konsisten! CR = ' . number_format($hasil['cr'], 4) . '. Silakan perbaiki nilai perbandingan dan hitung ulang.')
            ->with('hasil_ahp', $hasilSession);
    }

    // ── Helper: reindex kode C1, C2, ... ──────────────────────────────────
    private function reindexKode(): void
    {
        $kriterias = Kriteria::orderBy('kriteria_id')->get();
        foreach ($kriterias as $i => $k) {
            $k->update(['kode_kriteria' => 'C' . ($i + 1)]);
        }
    }
}