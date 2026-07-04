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
    public function index(Request $request)
    {
        $query = HasilAkhir::with(['pengajuan.bantuanSosial'])
            ->orderBy('ranking');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('pengajuan', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nik',  'like', "%{$search}%");
            });
        }

        if ($request->filled('jenis_bantuan')) {
            $query->whereHas('pengajuan.bantuanSosial', fn($q) =>
                $q->where('id', $request->jenis_bantuan)
            );
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $hasilAkhirs      = $query->paginate(10)->withQueryString();
        $jenisBantuanList = BantuanSosial::pluck('nama_bantuan', 'id');

        $total           = HasilAkhir::count();
        $totalLayak      = HasilAkhir::where('status', 'Layak')->count();
        $totalTidakLayak = HasilAkhir::where('status', 'Tidak Layak')->count();

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

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $hasilAkhirs = $query->get();

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