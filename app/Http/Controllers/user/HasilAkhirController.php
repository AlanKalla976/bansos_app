<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\HasilAkhir;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HasilAkhirController extends Controller
{
    /**
     * Hitung status Layak/Tidak Layak berdasarkan kuota per jenis bantuan.
     * Ranking 1..kuota (di dalam masing-masing jenis bantuan) => Layak.
     *
     * @param \Illuminate\Support\Collection $items Koleksi HasilAkhir (relasi pengajuan.bantuanSosial harus sudah di-load)
     */
    private function attachStatusByKuota($items)
    {
        $grouped = $items->groupBy(function ($h) {
            return $h->pengajuan->bantuanSosial->id ?? 'unknown';
        });

        foreach ($grouped as $bantuanId => $group) {
            $kuota = optional($group->first()->pengajuan->bantuanSosial)->kuota ?? 0;

            $sorted = $group->sortBy(function ($h) {
                return $h->ranking;
            })->values();

            foreach ($sorted as $index => $h) {
                $h->status_computed = ($index < $kuota) ? 'Layak' : 'Tidak Layak';
            }
        }

        return $items;
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        // Cek apakah user sudah pernah mengajukan
        $pengajuanUser = Pengajuan::where('user_id', $user->users_id)->first();

        // Jika belum mengajukan sama sekali
        if (!$pengajuanUser) {
            return view('user.hasilakhir.index', [
                'status'      => 'belum_mengajukan',
                'hasilAkhirs' => collect(),
                'hasilSendiri'=> null,
            ]);
        }

        // Cek apakah pengajuan user sudah ada di hasil_akhirs (sudah dihitung MOORA)
        $hasilSendiri = HasilAkhir::with(['pengajuan.bantuanSosial'])
            ->whereHas('pengajuan', function ($q) use ($user) {
                $q->where('user_id', $user->users_id);
            })
            ->orderBy('ranking')
            ->first();

        // Sudah mengajukan tapi belum dihitung MOORA
        if (!$hasilSendiri) {
            return view('user.hasilakhir.index', [
                'status'       => 'belum_dinilai',
                'pengajuanUser'=> $pengajuanUser,
                'hasilAkhirs'  => collect(),
                'hasilSendiri' => null,
            ]);
        }

        // Sudah mengajukan dan sudah ada hasil MOORA
        // Ambil SEMUA hasil (tanpa filter search dulu) supaya status per-kuota
        // dihitung berdasarkan ranking di dalam masing-masing jenis bantuan secara utuh,
        // baru difilter oleh pencarian setelahnya.
        $allHasilAkhirs = HasilAkhir::with(['pengajuan.bantuanSosial'])
            ->orderBy('ranking')
            ->get();

        $allHasilAkhirs = $this->attachStatusByKuota($allHasilAkhirs);

        // Samakan status_computed milik hasilSendiri dengan yang sudah dihitung di atas
        $hasilSendiri = $allHasilAkhirs->firstWhere('hasil_id', $hasilSendiri->hasil_id) ?? $hasilSendiri;

        $hasilAkhirs = $allHasilAkhirs;

        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $hasilAkhirs = $hasilAkhirs->filter(function ($h) use ($search) {
                $nama = strtolower($h->pengajuan->nama ?? '');
                $nik  = strtolower($h->pengajuan->nik ?? '');
                return str_contains($nama, $search) || str_contains($nik, $search);
            })->values();
        }

        return view('user.hasilakhir.index', [
            'status'       => 'sudah_dinilai',
            'hasilAkhirs'  => $hasilAkhirs,
            'hasilSendiri' => $hasilSendiri,
            'pengajuanUser'=> $pengajuanUser,
        ]);
    }
}