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

        $totalBPNT = Pengajuan::whereHas('bantuanSosial', fn($q) =>
            $q->where('nama_bantuan', 'LIKE', '%BPNT%')
        )->count();

        $totalBLT = Pengajuan::whereHas('bantuanSosial', fn($q) =>
            $q->where('nama_bantuan', 'LIKE', '%BLT%')
        )->count();

        $totalPKH = Pengajuan::whereHas('bantuanSosial', fn($q) =>
            $q->where('nama_bantuan', 'LIKE', '%PKH%')
        )->count();

        // Hitung Layak/Tidak Layak berdasarkan kuota per jenis bantuan
        $totalLayak      = 0;
        $totalTidakLayak = 0;

        $semuaBantuan = BantuanSosial::all();
        foreach ($semuaBantuan as $bantuan) {
            $kuota = $bantuan->kuota ?? 0;
            $hasil = HasilAkhir::whereHas('pengajuan', fn($q) =>
                $q->where('bantuan_sosial_id', $bantuan->id)
            )->orderBy('ranking')->get();

            foreach ($hasil as $item) {
                if ($item->ranking <= $kuota) {
                    $totalLayak++;
                } else {
                    $totalTidakLayak++;
                }
            }
        }

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

        $filterTahun   = $request->tahun ?? date('Y');
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
                'pengajuan.bantuanSosial',
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
        if ($filterBantuan != 'semua') {
            $bantuanSosialList = BantuanSosial::where('id', $filterBantuan)->get();
        } else {
            $bantuanSosialList = BantuanSosial::all();
        }

        $labels     = [];
        $layak      = [];
        $tidakLayak = [];

        foreach ($bantuanSosialList as $bantuan) {
            $kuota = $bantuan->kuota ?? 0;

            $hasil = HasilAkhir::with(['pengajuan.bantuanSosial'])
                ->whereYear('created_at', $tahun)
                ->whereHas('pengajuan', fn($q) =>
                    $q->where('bantuan_sosial_id', $bantuan->id)
                )
                ->orderBy('ranking')
                ->get();

            $labels[]     = $bantuan->nama_bantuan;
            $layak[]      = $hasil->filter(fn($item) => $item->ranking <= $kuota)->count();
            $tidakLayak[] = $hasil->filter(fn($item) => $item->ranking > $kuota)->count();
        }

        return [
            'labels'     => $labels,
            'layak'      => $layak,
            'tidakLayak' => $tidakLayak,
        ];
    }
}