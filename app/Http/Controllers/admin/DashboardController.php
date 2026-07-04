<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pengajuan;
use App\Models\HasilAkhir;
use App\Models\BantuanSosial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        // ==========================
        // Statistik Dashboard
        // ==========================
        $totalMasyarakat = User::where('role', 'masyarakat')->count();
        $totalPengajuan  = Pengajuan::count();

        $totalBPNT = Pengajuan::whereHas('bantuanSosial', function ($q) {
            $q->where('nama_bantuan', 'LIKE', '%BPNT%');
        })->count();

        $totalBLT = Pengajuan::whereHas('bantuanSosial', function ($q) {
            $q->where('nama_bantuan', 'LIKE', '%BLT%');
        })->count();

        $totalPKH = Pengajuan::whereHas('bantuanSosial', function ($q) {
            $q->where('nama_bantuan', 'LIKE', '%PKH%');
        })->count();

        // Perbaikan batas kelayakan (>= 0.35) untuk Counter Statistik
        $totalLayak = HasilAkhir::where('nilai_yi', '>=', 0.35)->count();
        $totalTidakLayak = HasilAkhir::where('nilai_yi', '<', 0.35)->count();

        // ==========================
        // Filter Dashboard
        // ==========================
        $tahunList = HasilAkhir::selectRaw('YEAR(created_at) as tahun')
            ->groupBy('tahun')
            ->orderByDesc('tahun')
            ->pluck('tahun');

        if ($tahunList->isEmpty()) {
            $tahunList = collect([date('Y')]);
        }

        $filterTahun = $request->tahun ?? date('Y');
        $filterBantuan = $request->jenis_bantuan ?? 'semua';

        $jenisBantuanList = BantuanSosial::all();

        // ==========================
        // Monitoring Chart
        // ==========================
        $chartData = $this->getChartData($filterTahun, $filterBantuan);

        // ==========================
        // 5 Hasil Terbaru
        // ==========================
        $hasilTerbaru = HasilAkhir::with([
                'pengajuan',
                'pengajuan.user',
                'pengajuan.bantuanSosial'
            ])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard.index', compact(
            'admin',
            'totalMasyarakat',
            'totalPengajuan',
            'totalBPNT',
            'totalBLT',
            'totalPKH',
            'totalLayak',
            'totalTidakLayak',
            'tahunList',
            'filterTahun',
            'filterBantuan',
            'jenisBantuanList',
            'chartData',
            'hasilTerbaru'
        ));
    }

    private function getChartData($tahun, $filterBantuan)
    {
        // Menentukan list bantuan yang akan ditampilkan di grafik berdasarkan filter
        if ($filterBantuan != 'semua') {
            $bantuanSosialList = BantuanSosial::where('id', $filterBantuan)->get();
        } else {
            $bantuanSosialList = BantuanSosial::all();
        }

        $query = HasilAkhir::with([
            'pengajuan',
            'pengajuan.bantuanSosial'
        ])->whereYear('created_at', $tahun);

        if ($filterBantuan != 'semua') {
            $query->whereHas('pengajuan', function ($q) use ($filterBantuan) {
                $q->where('bantuan_sosial_id', $filterBantuan);
            });
        }

        $hasil = $query->get();

        $labels = [];
        $layak = [];
        $tidakLayak = [];

        foreach ($bantuanSosialList as $bantuan) {
            $labels[] = $bantuan->nama_bantuan;

            // Penentuan Layak/Tidak Layak di dalam grafik disesuaikan dengan nilai_yi (0.35)
            $layak[] = $hasil->filter(function ($item) use ($bantuan) {
                return optional($item->pengajuan)->bantuan_sosial_id == $bantuan->id
                    && $item->nilai_yi >= 0.35;
            })->count();

            $tidakLayak[] = $hasil->filter(function ($item) use ($bantuan) {
                return optional($item->pengajuan)->bantuan_sosial_id == $bantuan->id
                    && $item->nilai_yi < 0.35;
            })->count();
        }

        return [
            'labels' => $labels,
            'layak' => $layak,
            'tidakLayak' => $tidakLayak,
        ];
    }
}