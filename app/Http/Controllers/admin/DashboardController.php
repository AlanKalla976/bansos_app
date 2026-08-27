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
        if ($request->is('admin/petugas') || $request->is('admin/petugas/*')) {
            $admin = Auth::guard('petugas')->user();
        } elseif ($request->is('admin/lurah') || $request->is('admin/lurah/*')) {
            $admin = Auth::guard('lurah')->user();
        } else {
            $admin = Auth::guard('admin')->user();
        }

        // ── DASHBOARD PETUGAS ──
        if ($admin->role === 'petugas') {
            $stats = [
                'menunggu_validasi' => \App\Models\Pengajuan::where('status', 'Menunggu')->count(),
                'pengajuan_valid'    => \App\Models\Pengajuan::where('status', 'Diverifikasi')->count(),
                'pengajuan_tidak_valid' => \App\Models\Pengajuan::where('status', 'Ditolak')->count(),
                'belum_dijadwalkan'  => \App\Models\Penyaluran::where('status', 'Belum Dijadwalkan')->count(),
                'sudah_dijadwalkan'  => \App\Models\Penyaluran::where('status', 'Sudah Dijadwalkan')->count(),
                'bantuan_diambil'    => \App\Models\Penyaluran::where('status', 'Sudah Diambil')->count(),
                'tepat_waktu'        => \App\Models\Monitoring::where('ketepatan_waktu', 'Tepat Waktu')->count(),
                'terlambat'          => \App\Models\Monitoring::where('ketepatan_waktu', 'Terlambat')->count(),
                'sesuai_sasaran'     => \App\Models\Monitoring::where('ketepatan_sasaran', 'Sesuai Sasaran')->count(),
                'tidak_sesuai'       => \App\Models\Monitoring::where('ketepatan_sasaran', 'Tidak Sesuai Sasaran')->count(),
            ];

            return view('admin.dashboard.index', compact('admin', 'stats'));
        }

        // ── DASHBOARD LURAH ──
        if ($admin->role === 'lurah') {
            $stats = [
                'total_calon'        => \App\Models\HasilAkhir::count(),
                'menunggu_setuju'    => \App\Models\HasilAkhir::where('persetujuan_status', 'Menunggu Persetujuan')->count(),
                'disetujui'          => \App\Models\HasilAkhir::where('persetujuan_status', 'Disetujui')->count(),
                'ditolak'            => \App\Models\HasilAkhir::where('persetujuan_status', 'Ditolak')->count(),
                'total_penyaluran'   => \App\Models\Penyaluran::where('status', 'Sudah Diambil')->count(),
                'tepat_waktu'        => \App\Models\Monitoring::where('ketepatan_waktu', 'Tepat Waktu')->count(),
                'terlambat'          => \App\Models\Monitoring::where('ketepatan_waktu', 'Terlambat')->count(),
                'sesuai_sasaran'     => \App\Models\Monitoring::where('ketepatan_sasaran', 'Sesuai Sasaran')->count(),
                'tidak_sesuai'       => \App\Models\Monitoring::where('ketepatan_sasaran', 'Tidak Sesuai Sasaran')->count(),
                'dampak_sangat'      => \App\Models\Monitoring::where('dampak', 'Sangat Membantu')->count(),
                'dampak_membantu'    => \App\Models\Monitoring::where('dampak', 'Membantu')->count(),
                'dampak_cukup'       => \App\Models\Monitoring::where('dampak', 'Cukup Membantu')->count(),
                'dampak_tidak'       => \App\Models\Monitoring::where('dampak', 'Tidak Membantu')->count(),
            ];

            return view('admin.dashboard.index', compact('admin', 'stats'));
        }

        // ── DASHBOARD ADMIN (Lama/Default) ──
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

        // Filter Dashboard
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

        // Monitoring Chart
        $chartData = $this->getChartData($filterTahun, $filterBantuan);

        // 5 Hasil Terbaru
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