<?php

namespace App\Services;

use App\Models\Kriteria;
use App\Models\PerbandinganKriteria;

class AhpService
{
    /**
     * Tabel Random Index (RI) Saaty
     * Indeks = jumlah kriteria (n)
     */
    private array $ri = [
        1  => 0.00,
        2  => 0.00,
        3  => 0.58,
        4  => 0.90,
        5  => 1.12,
        6  => 1.24,
        7  => 1.32,
        8  => 1.41,
        9  => 1.45,
        10 => 1.49,
    ];

    /**
     * Lakukan seluruh perhitungan AHP dan kembalikan hasilnya.
     *
     * Langkah:
     *  1. Bentuk matriks perbandingan n×n
     *  2. Hitung jumlah setiap kolom
     *  3. Buat matriks normalisasi (bagi setiap elemen dengan jumlah kolomnya)
     *  4. Hitung Eigen Vector / Priority Vector (rata-rata baris normalisasi)
     *  5. Hitung Weighted Sum Vector (A × w, baris demi baris)
     *  6. Hitung λMax = rata-rata (Weighted Sum Vector[i] / Eigen Vector[i])
     *  7. Hitung CI = (λMax − n) / (n − 1)
     *  8. Hitung CR = CI / RI
     */
    public function hitung(): array
    {
        $kriterias    = Kriteria::orderBy('kriteria_id')->get();
        $perbandingan = PerbandinganKriteria::all();
        $n            = $kriterias->count();

        if ($n < 2) {
            return ['error' => 'Minimal 2 kriteria diperlukan untuk perhitungan AHP.'];
        }

        // ── STEP 1: Bentuk matriks perbandingan n×n ──────────────────────
        // Inisialisasi diagonal = 1, off-diagonal = 0 (belum diisi)
        $matrix = [];
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $matrix[$i][$j] = ($i === $j) ? 1.0 : 0.0;
            }
        }

        // Buat mapping kriteria_id → index baris/kolom
        $idxMap = [];
        foreach ($kriterias as $i => $k) {
            $idxMap[$k->kriteria_id] = $i;
        }

        // Isi nilai dari tabel perbandingan (atas diagonal) + nilai kebalikan (bawah diagonal)
        foreach ($perbandingan as $p) {
            $idxI = $idxMap[$p->kriteria_pertama_id] ?? null;
            $idxJ = $idxMap[$p->kriteria_kedua_id]   ?? null;

            if ($idxI !== null && $idxJ !== null && $idxI !== $idxJ) {
                $nilai = (float) $p->nilai_perbandingan;
                if ($nilai > 0) {
                    $matrix[$idxI][$idxJ] = $nilai;
                    $matrix[$idxJ][$idxI] = 1.0 / $nilai;
                }
            }
        }

        // ── STEP 2: Jumlah setiap kolom ──────────────────────────────────
        $jumlahKolom = array_fill(0, $n, 0.0);
        for ($j = 0; $j < $n; $j++) {
            for ($i = 0; $i < $n; $i++) {
                $jumlahKolom[$j] += $matrix[$i][$j];
            }
        }

        // ── STEP 3: Matriks normalisasi ───────────────────────────────────
        // normalized[i][j] = matrix[i][j] / jumlahKolom[j]
        $normalized = [];
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $normalized[$i][$j] = ($jumlahKolom[$j] > 0)
                    ? $matrix[$i][$j] / $jumlahKolom[$j]
                    : 0.0;
            }
        }

        // ── STEP 4: Eigen Vector (Priority Vector) ────────────────────────
        // eigenVector[i] = rata-rata baris ke-i pada matriks normalisasi
        $eigenVector = [];
        for ($i = 0; $i < $n; $i++) {
            $eigenVector[$i] = array_sum($normalized[$i]) / $n;
        }

        // ── STEP 5: Weighted Sum Vector ───────────────────────────────────
        // weightedSum[i] = Σ(matrix[i][j] * eigenVector[j])   untuk j=0..n-1
        // Ini adalah perkalian matriks A dengan vektor bobot w: hasil = A·w
        $weightedSum = array_fill(0, $n, 0.0);
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $weightedSum[$i] += $matrix[$i][$j] * $eigenVector[$j];
            }
        }

        // ── STEP 6: λMax ──────────────────────────────────────────────────
        // λMax = rata-rata (weightedSum[i] / eigenVector[i])
        $lambdaSum = 0.0;
        for ($i = 0; $i < $n; $i++) {
            if ($eigenVector[$i] > 0) {
                $lambdaSum += $weightedSum[$i] / $eigenVector[$i];
            }
        }
        $lambdaMax = $lambdaSum / $n;

        // ── STEP 7: Consistency Index (CI) ────────────────────────────────
        $ci = ($n > 1) ? ($lambdaMax - $n) / ($n - 1) : 0.0;

        // ── STEP 8: Consistency Ratio (CR) ───────────────────────────────
        $ri = $this->ri[$n] ?? 1.49;
        $cr = ($ri > 0) ? $ci / $ri : 0.0;

        return [
            'kriterias'    => $kriterias,
            'n'            => $n,
            'matrix'       => $matrix,
            'jumlahKolom'  => $jumlahKolom,
            'normalized'   => $normalized,
            'eigenVector'  => $eigenVector,
            'weightedSum'  => $weightedSum,
            'lambdaMax'    => $lambdaMax,
            'ci'           => $ci,
            'ri'           => $ri,
            'cr'           => $cr,
            'konsisten'    => $cr <= 0.1,
            'error'        => null,
        ];
    }

    /**
     * Simpan Eigen Vector ke kolom bobot pada tabel kriteria.
     */
    public function simpanBobot(array $eigenVector, $kriterias): void
    {
        foreach ($kriterias as $i => $kriteria) {
            $kriteria->update(['bobot' => $eigenVector[$i]]);
        }
    }
}