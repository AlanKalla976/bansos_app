<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BantuanSosial;
use App\Models\HasilAkhir;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HasilAkhirController extends Controller
{
    /**
     * Hitung status Layak/Tidak Layak berdasarkan kuota per jenis bantuan,
     * sekaligus menghitung ranking KHUSUS di dalam masing-masing jenis bantuan
     * (selalu mulai dari 1, tidak bergantung pada kolom 'ranking' yang tersimpan
     * di DB agar tidak bolong/loncat kalau ada data yang dihapus).
     *
     * @param \Illuminate\Support\Collection $items Koleksi HasilAkhir
     *        (relasi pengajuan.bantuanSosial harus sudah di-load)
     */
    private function attachStatusByKuota($items)
    {
        $grouped = $items->groupBy(function ($h) {
            return $h->pengajuan->bantuanSosial->id ?? 'unknown';
        });

        foreach ($grouped as $bantuanId => $group) {
            $kuota = optional($group->first()->pengajuan->bantuanSosial)->kuota ?? 0;

            // Urutkan berdasarkan skor MOORA (nilai_yi) tertinggi -> terendah,
            // bukan berdasarkan kolom 'ranking' yang tersimpan di DB.
            $sorted = $group->sortByDesc('nilai_yi')->values();

            foreach ($sorted as $index => $h) {
                $h->ranking_in_bantuan = $index + 1; // ranking 1..N khusus jenis bantuan ini
                $h->status_computed    = ($index < $kuota) ? 'Layak' : 'Tidak Layak';
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
                'status'         => 'belum_mengajukan',
                'hasilAkhirs'    => collect(),
                'hasilSendiri'   => null,
                'bantuanList'    => collect(),
                'jenisBantuanId' => null,
            ]);
        }

        // Cek apakah pengajuan user sudah dihitung MOORA
        $sudahDinilai = HasilAkhir::whereHas('pengajuan', function ($q) use ($user) {
            $q->where('user_id', $user->users_id);
        })->exists();

        if (!$sudahDinilai) {
            return view('user.hasilakhir.index', [
                'status'         => 'belum_dinilai',
                'pengajuanUser'  => $pengajuanUser,
                'hasilAkhirs'    => collect(),
                'hasilSendiri'   => null,
                'bantuanList'    => collect(),
                'jenisBantuanId' => null,
            ]);
        }

        // Ambil SEMUA hasil, urutkan berdasarkan skor (nilai_yi) tertinggi -> terendah.
        // Tidak pakai orderBy('ranking') lagi supaya kalau ada data yang dihapus,
        // ranking otomatis urut kembali dari 1 tanpa loncat.
        $allHasilAkhirs = HasilAkhir::with(['pengajuan.bantuanSosial'])
            ->get()
            ->sortByDesc('nilai_yi')
            ->values();

        // Ranking global (tampilan biasa, semua jenis bantuan digabung jadi satu urutan)
        foreach ($allHasilAkhirs as $index => $h) {
            $h->global_ranking = $index + 1;
        }

        // Ranking + status Layak/Tidak Layak per jenis bantuan (berdasarkan kuota)
        $allHasilAkhirs = $this->attachStatusByKuota($allHasilAkhirs);

        // Data hasil milik user sendiri (dihitung dari data lengkap, sebelum difilter search/jenis bantuan)
        $hasilSendiri = $allHasilAkhirs->first(function ($h) use ($user) {
            return ($h->pengajuan->user_id ?? null) == $user->users_id;
        });

        $jenisBantuanId = $request->input('jenis_bantuan');
        $hasilAkhirs    = $allHasilAkhirs;

        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $hasilAkhirs = $hasilAkhirs->filter(function ($h) use ($search) {
                $nama = strtolower($h->pengajuan->nama ?? '');
                $nik  = strtolower($h->pengajuan->nik ?? '');
                return str_contains($nama, $search) || str_contains($nik, $search);
            })->values();
        }

        if ($jenisBantuanId) {
            $hasilAkhirs = $hasilAkhirs->filter(function ($h) use ($jenisBantuanId) {
                return ($h->pengajuan->bantuanSosial->id ?? null) == $jenisBantuanId;
            })->values();
        }

        // Ranking yang ditampilkan di tabel:
        // - Tampilan biasa (tidak difilter jenis bantuan)  -> pakai ranking global
        // - Difilter jenis bantuan tertentu                -> pakai ranking khusus jenis itu (mulai dari 1 lagi)
        foreach ($hasilAkhirs as $h) {
            $h->display_ranking = $jenisBantuanId ? $h->ranking_in_bantuan : $h->global_ranking;
        }

        $bantuanList = BantuanSosial::orderBy('nama_bantuan')->get();

        return view('user.hasilakhir.index', [
            'status'         => 'sudah_dinilai',
            'hasilAkhirs'    => $hasilAkhirs,
            'hasilSendiri'   => $hasilSendiri,
            'pengajuanUser'  => $pengajuanUser,
            'bantuanList'    => $bantuanList,
            'jenisBantuanId' => $jenisBantuanId,
        ]);
    }
}