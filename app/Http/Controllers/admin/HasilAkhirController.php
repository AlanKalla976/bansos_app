<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HasilAkhir;
use App\Models\BantuanSosial;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\HasilAkhirExport;
use Barryvdh\DomPDF\Facade\Pdf;

class HasilAkhirController extends Controller
{
    /**
     * Hitung ulang status Layak/Tidak Layak berdasarkan kuota per jenis bantuan,
     * dan hitung dua jenis ranking sekaligus:
     *
     * - global_ranking     : ranking gabungan semua jenis bantuan (urut nilai_yi
     *                        tertinggi -> terendah), dihitung dari SELURUH data,
     *                        tidak bergantung kolom 'ranking' di DB supaya tidak
     *                        bolong/loncat kalau ada data yang dihapus.
     * - ranking_in_bantuan : ranking di dalam masing-masing jenis bantuan saja,
     *                        selalu mulai dari 1 lagi untuk tiap jenis bantuan.
     *
     * status_computed (Layak/Tidak Layak) ditentukan dari ranking_in_bantuan
     * dibandingkan kuota jenis bantuan tsb.
     *
     * PENTING: fungsi ini harus dipanggil terhadap koleksi LENGKAP (semua data,
     * belum difilter jenis_bantuan/status/search) supaya global_ranking dan
     * kuota per jenis bantuan dihitung dengan benar. Filter diterapkan SETELAH
     * fungsi ini selesai.
     *
     * @param \Illuminate\Support\Collection $items Koleksi HasilAkhir LENGKAP
     *        (relasi pengajuan.bantuanSosial harus sudah di-load)
     */
    private function attachStatusByKuota($items)
    {
        // Urutkan seluruh data berdasarkan skor MOORA (nilai_yi) tertinggi -> terendah
        // untuk mendapatkan ranking global yang selalu rapi (1,2,3,... tanpa lompat).
        $items = $items->sortByDesc('nilai_yi')->values();

        foreach ($items as $index => $h) {
            $h->global_ranking = $index + 1;
        }

        // Kelompokkan per jenis bantuan sosial untuk hitung ranking & status per grup
        $grouped = $items->groupBy(function ($h) {
            return $h->pengajuan->bantuanSosial->id ?? 'unknown';
        });

        foreach ($grouped as $bantuanId => $group) {
            $kuota = optional($group->first()->pengajuan->bantuanSosial)->kuota ?? 0;

            // Urutkan berdasarkan nilai_yi tertinggi -> terendah di dalam grup ini,
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
        // Ambil SEMUA data dulu (tanpa filter jenis_bantuan/status, tanpa pagination)
        // supaya global_ranking, ranking_in_bantuan, dan status per-kuota dihitung
        // dari populasi data yang lengkap dan benar.
        $baseQuery = HasilAkhir::with(['pengajuan.bantuanSosial']);

        if ($request->filled('search')) {
            $search = $request->search;
            $baseQuery->whereHas('pengajuan', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nik',  'like', "%{$search}%");
            });
        }

        // Ambil semua data (sudah kena filter search jika ada), lalu urutkan
        // berdasarkan nilai_yi (bukan kolom ranking) supaya tidak bolong.
        $allItems = $baseQuery->get()->sortByDesc('nilai_yi')->values();
        $allItems = $this->attachStatusByKuota($allItems);

        $jenisBantuanId = $request->input('jenis_bantuan');

        // Filter jenis bantuan diterapkan SETELAH ranking dihitung, supaya
        // ranking_in_bantuan tetap konsisten (mulai dari 1 untuk jenis itu).
        $filtered = $allItems;
        if ($jenisBantuanId) {
            $filtered = $filtered->filter(function ($h) use ($jenisBantuanId) {
                return ($h->pengajuan->bantuanSosial->id ?? null) == $jenisBantuanId;
            })->values();
        }

        // Filter status (Layak/Tidak Layak) diterapkan setelah status dihitung dari kuota
        if ($request->filled('status')) {
            $filtered = $filtered->filter(function ($h) use ($request) {
                return $h->status_computed === $request->status;
            })->values();
        }

        // Tentukan ranking yang ditampilkan di tabel:
        // - Tidak difilter jenis bantuan -> pakai global_ranking
        // - Difilter jenis bantuan tertentu -> pakai ranking_in_bantuan (mulai dari 1 lagi)
        foreach ($filtered as $h) {
            $h->ranking_display = $jenisBantuanId ? $h->ranking_in_bantuan : $h->global_ranking;
        }

        // Pagination manual
        $perPage = 10;
        $page    = $request->get('page', 1);
        $offset  = ($page - 1) * $perPage;

        $itemsForPage = $filtered->slice($offset, $perPage)->values();

        $hasilAkhirs = new \Illuminate\Pagination\LengthAwarePaginator(
            $itemsForPage,
            $filtered->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $jenisBantuanList = BantuanSosial::pluck('nama_bantuan', 'id');

        // Statistik total berdasarkan status hasil perhitungan kuota (bukan kolom status di DB)
        // Dihitung dari SELURUH data (tanpa filter apapun) supaya statistik tetap global.
        $allForStats = $this->attachStatusByKuota(
            HasilAkhir::with(['pengajuan.bantuanSosial'])->get()
        );

        $total           = $allForStats->count();
        $totalLayak      = $allForStats->where('status_computed', 'Layak')->count();
        $totalTidakLayak = $allForStats->where('status_computed', 'Tidak Layak')->count();

        return view('admin.hasilakhir.index', compact(
            'hasilAkhirs',
            'jenisBantuanList',
            'total',
            'totalLayak',
            'totalTidakLayak'
        ));
    }

    public function exportExcel(Request $request)
    {
        $namaJenis = 'Semua';
        if ($request->filled('jenis_bantuan')) {
            $bantuan   = BantuanSosial::find($request->jenis_bantuan);
            $namaJenis = $bantuan ? $bantuan->nama_bantuan : 'Semua';
        }

        return Excel::download(
            new HasilAkhirExport($request->all()),
            'Laporan_' . $namaJenis . '.xlsx'
        );
    }

    public function exportPdf(Request $request)
    {
        // Ambil semua data (dengan filter search saja) untuk hitung ranking secara utuh
        $query = HasilAkhir::with(['pengajuan.bantuanSosial']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('pengajuan', fn($q) =>
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nik',  'like', "%{$search}%")
            );
        }

        $allItems = $query->get()->sortByDesc('nilai_yi')->values();
        $allItems = $this->attachStatusByKuota($allItems);

        $jenisBantuanId = $request->input('jenis_bantuan');

        $hasilAkhirs = $allItems;
        if ($jenisBantuanId) {
            $hasilAkhirs = $hasilAkhirs->filter(function ($h) use ($jenisBantuanId) {
                return ($h->pengajuan->bantuanSosial->id ?? null) == $jenisBantuanId;
            })->values();
        }

        if ($request->filled('status')) {
            $hasilAkhirs = $hasilAkhirs->filter(function ($h) use ($request) {
                return $h->status_computed === $request->status;
            })->values();
        }

        foreach ($hasilAkhirs as $h) {
            $h->ranking_display = $jenisBantuanId ? $h->ranking_in_bantuan : $h->global_ranking;
        }

        $namaJenis = 'Semua';
        if ($request->filled('jenis_bantuan')) {
            $bantuan   = BantuanSosial::find($request->jenis_bantuan);
            $namaJenis = $bantuan ? $bantuan->nama_bantuan : 'Semua';
        }

        $pdf = Pdf::loadView('admin.hasilakhir.pdf', compact('hasilAkhirs', 'namaJenis'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('Laporan_' . $namaJenis . '.pdf');
    }
}