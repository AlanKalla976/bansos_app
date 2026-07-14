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
     * Hitung ulang status Layak/Tidak Layak berdasarkan kuota per jenis bantuan.
     * Ranking 1..kuota (per jenis bantuan) => Layak, sisanya => Tidak Layak.
     *
     * Juga menghitung ranking_display, yaitu posisi urutan di dalam grup
     * jenis bantuan (dimulai dari 1), supaya saat data difilter per jenis
     * bantuan, ranking yang ditampilkan tidak mengikuti ranking global,
     * melainkan mulai dari 1 lagi khusus untuk jenis bantuan tersebut.
     *
     * @param \Illuminate\Support\Collection $items Koleksi HasilAkhir (dengan relasi pengajuan.bantuanSosial di-load)
     */
    private function attachStatusByKuota($items)
    {
        // Kelompokkan per jenis bantuan sosial
        $grouped = $items->groupBy(function ($h) {
            return $h->pengajuan->bantuanSosial->id ?? 'unknown';
        });

        foreach ($grouped as $bantuanId => $group) {
            $kuota = optional($group->first()->pengajuan->bantuanSosial)->kuota ?? 0;

            // Urutkan berdasarkan ranking di dalam grup jenis bantuan ini
            $sorted = $group->sortBy(function ($h) {
                return $h->ranking;
            })->values();

            foreach ($sorted as $index => $h) {
                // index dimulai dari 0, jadi posisi urutan = index + 1
                $h->status_computed = ($index < $kuota) ? 'Layak' : 'Tidak Layak';

                // Ranking yang ditampilkan = posisi urutan di dalam grup jenis
                // bantuan ini, bukan ranking global, supaya saat difilter per
                // jenis bantuan, ranking mulai dari 1.
                $h->ranking_display = $index + 1;
            }
        }

        return $items;
    }

    public function index(Request $request)
    {
        // Ambil semua data dulu (tanpa pagination) supaya status per-kuota bisa dihitung
        // berdasarkan ranking di dalam masing-masing jenis bantuan, baru dipaginate manual.
        $baseQuery = HasilAkhir::with(['pengajuan.bantuanSosial'])
            ->orderBy('ranking');

        if ($request->filled('search')) {
            $search = $request->search;
            $baseQuery->whereHas('pengajuan', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nik',  'like', "%{$search}%");
            });
        }

        if ($request->filled('jenis_bantuan')) {
            $baseQuery->whereHas('pengajuan.bantuanSosial', fn($q) =>
                $q->where('id', $request->jenis_bantuan)
            );
        }

        $allItems = $baseQuery->get();
        $allItems = $this->attachStatusByKuota($allItems);

        // Filter status (Layak/Tidak Layak) diterapkan setelah status dihitung dari kuota
        if ($request->filled('status')) {
            $allItems = $allItems->filter(function ($h) use ($request) {
                return $h->status_computed === $request->status;
            })->values();
        }

        // Pagination manual
        $perPage = 10;
        $page    = $request->get('page', 1);
        $offset  = ($page - 1) * $perPage;

        $itemsForPage = $allItems->slice($offset, $perPage)->values();

        $hasilAkhirs = new \Illuminate\Pagination\LengthAwarePaginator(
            $itemsForPage,
            $allItems->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $jenisBantuanList = BantuanSosial::pluck('nama_bantuan', 'id');

        // Statistik total berdasarkan status hasil perhitungan kuota (bukan kolom status di DB)
        $allForStats = $this->attachStatusByKuota(
            HasilAkhir::with(['pengajuan.bantuanSosial'])->orderBy('ranking')->get()
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
        $query = HasilAkhir::with(['pengajuan.bantuanSosial'])
            ->orderBy('ranking');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('pengajuan', fn($q) =>
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nik',  'like', "%{$search}%")
            );
        }

        if ($request->filled('jenis_bantuan')) {
            $query->whereHas('pengajuan.bantuanSosial', fn($q) =>
                $q->where('id', $request->jenis_bantuan)
            );
        }

        $hasilAkhirs = $query->get();
        $hasilAkhirs = $this->attachStatusByKuota($hasilAkhirs);

        if ($request->filled('status')) {
            $hasilAkhirs = $hasilAkhirs->filter(function ($h) use ($request) {
                return $h->status_computed === $request->status;
            })->values();
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