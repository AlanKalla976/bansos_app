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
        
        if ($pengajuan->status !== 'Diverifikasi') {
            return redirect()->route('admin.penilaian.index')
                ->with('error', 'Penilaian hanya dapat diinput untuk pengajuan yang telah divalidasi (status Diverifikasi).');
        }

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

        $pengajuan = Pengajuan::findOrFail($request->pengajuan_id);
        if ($pengajuan->status !== 'Diverifikasi') {
            return redirect()->route('admin.penilaian.index')
                ->with('error', 'Penilaian hanya dapat diinput untuk pengajuan yang telah divalidasi (status Diverifikasi).');
        }

        // Cek apakah sebelumnya sudah ada penilaian untuk pengajuan ini (untuk pesan yang sesuai)
        $isUpdate = Penilaian::where('pengajuan_id', $request->pengajuan_id)->exists();

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

        $pesan = $isUpdate ? 'Penilaian berhasil diperbarui.' : 'Penilaian berhasil disimpan.';

        return redirect()->route('admin.penilaian.index')
            ->with('success', $pesan);
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

    /**
     * Tentukan status Layak/Tidak Layak berdasarkan kuota per jenis bantuan.
     * Ranking 1..kuota (di dalam masing-masing jenis bantuan) => Layak.
     *
     * @param array $ranked Array hasil ranked dari MooraService, tiap item berisi 'pengajuan' (model Pengajuan) dan 'yi'
     * @return array Ranked array yang sama, ditambah key 'status'
     */
    private function tentukanStatusByKuota(array $ranked): array
    {
        // Kelompokkan index berdasarkan jenis bantuan
        $grouped = [];
        foreach ($ranked as $idx => $r) {
            $bantuanId = $r['pengajuan']->bantuanSosial->id ?? 'unknown';
            $grouped[$bantuanId][] = $idx;
        }

        foreach ($grouped as $bantuanId => $indexes) {
            // Kuota diambil dari bantuanSosial pertama pada grup ini
            $firstIdx = $indexes[0];
            $kuota    = optional($ranked[$firstIdx]['pengajuan']->bantuanSosial)->kuota ?? 0;

            // Urutan dalam grup sudah sesuai urutan ranking global (karena $ranked sudah terurut Yi desc),
            // jadi posisi ke-n di dalam grup ini = urutan kelayakan untuk jenis bantuan tersebut.
            foreach ($indexes as $posisiDalamGrup => $idx) {
                $ranked[$idx]['status'] = ($posisiDalamGrup < $kuota) ? 'Layak' : 'Tidak Layak';
            }
        }

        return $ranked;
    }

    // ── Hitung MOORA ──
    public function hitungMoora()
    {
        $hasil = $this->mooraService->hitung();

        if ($hasil['error']) {
            return redirect()->route('admin.penilaian.index')
                ->with('error', $hasil['error']);
        }

        // Tentukan status Layak/Tidak Layak berdasarkan kuota per jenis bantuan
        $rankedWithStatus = $this->tentukanStatusByKuota($hasil['ranked']);

        // Konversi agar aman di session
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
                'kuota'         => $r['pengajuan']->bantuanSosial->kuota ?? 0,
                'yi'            => $r['yi'],
                'status'        => $r['status'],
            ], $rankedWithStatus),
            'n' => $hasil['n'],
            'm' => $hasil['m'],
        ];

        return redirect()->route('admin.penilaian.index')
            ->with('success', 'Perhitungan MOORA berhasil! Hasil telah disimpan.')
            ->with('hasil_moora', $hasilSession);
    }
}