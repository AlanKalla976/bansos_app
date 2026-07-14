<?php

namespace App\Services;

use App\Models\HasilAkhir;
use App\Models\Kriteria;
use App\Models\Penilaian;
use App\Models\Pengajuan;

class MooraService
{
    public function hitung(): array
    {
        // Ambil semua kriteria dengan bobot
        $kriterias = Kriteria::orderBy('kriteria_id')->get();

        // Ambil semua pengajuan yang sudah dinilai (Diverifikasi)
        $pengajuanIds = Penilaian::distinct()->pluck('pengajuan_id');
        $pengajuans   = Pengajuan::with('bantuanSosial')
            ->whereIn('id', $pengajuanIds)
            ->get();

        if ($pengajuans->isEmpty() || $kriterias->isEmpty()) {
            return ['error' => 'Data penilaian atau kriteria belum tersedia.'];
        }

        $n = $pengajuans->count(); // jumlah alternatif
        $m = $kriterias->count();  // jumlah kriteria

        // --- 1. Bentuk Matriks Keputusan (n x m) ---
        // $matrix[$i][$j] = nilai alternatif ke-i pada kriteria ke-j
        $matrix = [];
        foreach ($pengajuans as $i => $peng) {
            foreach ($kriterias as $j => $krit) {
                $penilaian = Penilaian::where('pengajuan_id', $peng->id)
                    ->where('kriteria_id', $krit->kriteria_id)
                    ->first();
                $matrix[$i][$j] = $penilaian ? (float) $penilaian->nilai : 0;
            }
        }

        // --- 2. Hitung akar jumlah kuadrat per kolom ---
        $akarKuadrat = [];
        for ($j = 0; $j < $m; $j++) {
            $sumKuadrat = 0;
            for ($i = 0; $i < $n; $i++) {
                $sumKuadrat += pow($matrix[$i][$j], 2);
            }
            $akarKuadrat[$j] = sqrt($sumKuadrat);
        } 

        // --- 3. Matriks Normalisasi ---
        $normalized = [];
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $m; $j++) {
                $normalized[$i][$j] = $akarKuadrat[$j] > 0
                    ? $matrix[$i][$j] / $akarKuadrat[$j]
                    : 0;
            }
        }

        // --- 4. Hitung Nilai Yi ---
        $yi = [];
        for ($i = 0; $i < $n; $i++) {
            $benefit = 0;
            $cost    = 0;
            foreach ($kriterias as $j => $krit) {
                $bobot = (float) $krit->bobot;
                $nilai = $normalized[$i][$j];
                if ($krit->tipe === 'benefit') {
                    $benefit += $bobot * $nilai;
                } else {
                    $cost += $bobot * $nilai;
                }
            }
            $yi[$i] = $benefit - $cost;
        }

        // --- 5. Ranking (urutkan Yi terbesar ke terkecil) ---
        $ranked = [];
        foreach ($pengajuans as $i => $peng) {
            $ranked[] = [
                'index'       => $i,
                'pengajuan'   => $peng,
                'yi'          => $yi[$i],
            ];
        }
        usort($ranked, fn($a, $b) => $b['yi'] <=> $a['yi']);

        // Tentukan status: 50% atas = Layak
        $batasLayak = ceil($n / 2);

        // --- 6. Simpan ke hasil_akhirs ---
        foreach ($ranked as $rank => $item) {
            HasilAkhir::updateOrCreate(
                ['pengajuan_id' => $item['pengajuan']->id],
                [
                    'nilai_yi' => $item['yi'],
                    'ranking'  => $rank + 1,
                    'status'   => ($rank + 1) <= $batasLayak ? 'Layak' : 'Tidak Layak',
                ]
            );
        }

        return [
            'error'       => null,
            'kriterias'   => $kriterias,
            'pengajuans'  => $pengajuans,
            'matrix'      => $matrix,
            'akarKuadrat' => $akarKuadrat,
            'normalized'  => $normalized,
            'yi'          => $yi,
            'ranked'      => $ranked,
            'batasLayak'  => $batasLayak,
            'n'           => $n,
            'm'           => $m,
        ];
    }
}