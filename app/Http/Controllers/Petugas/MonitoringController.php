<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Penyaluran;
use App\Models\Monitoring;
use App\Models\BantuanSosial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MonitoringController extends Controller
{
    /**
     * Menampilkan daftar monitoring penyaluran (Hanya untuk yang statusnya Sudah Diambil)
     */
    public function index(Request $request)
    {
        $query = Penyaluran::with(['hasilAkhir.pengajuan.bantuanSosial', 'monitoring.petugas'])
            ->where('status', 'Sudah Diambil')
            ->latest();

        // Filter status monitoring (Belum / Sudah Dimonitoring)
        $statusFilter = $request->input('status', 'Semua');
        if ($statusFilter === 'Belum') {
            $query->whereDoesntHave('monitoring');
        } elseif ($statusFilter === 'Sudah') {
            $query->whereHas('monitoring');
        }

        // Filter jenis bantuan
        if ($request->filled('bantuan_sosial_id')) {
            $query->whereHas('hasilAkhir.pengajuan', function ($q) use ($request) {
                $q->where('bantuan_sosial_id', $request->bantuan_sosial_id);
            });
        }

        // Filter pencarian nama / NIK
        if ($request->filled('search')) {
            $query->whereHas('hasilAkhir.pengajuan', function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('nik', 'like', '%' . $request->search . '%');
            });
        }

        $penyalurans = $query->paginate(15)->withQueryString();
        $bantuans = BantuanSosial::orderBy('nama_bantuan')->get();

        // Statistik
        $statsTotal = Penyaluran::where('status', 'Sudah Diambil')->count();
        $statsBelum = Penyaluran::where('status', 'Sudah Diambil')->whereDoesntHave('monitoring')->count();
        $statsSudah = Penyaluran::where('status', 'Sudah Diambil')->whereHas('monitoring')->count();

        return view('petugas.monitoring.index', compact(
            'penyalurans',
            'bantuans',
            'statusFilter',
            'statsTotal',
            'statsBelum',
            'statsSudah'
        ));
    }

    /**
     * Halaman form pengisian monitoring evaluasi
     */
    public function create(Penyaluran $penyaluran)
    {
        $penyaluran->load(['hasilAkhir.pengajuan.bantuanSosial', 'monitoring']);

        // Guard: Hanya untuk penyaluran yang Sudah Diambil
        if ($penyaluran->status !== 'Sudah Diambil') {
            return redirect()
                ->route('admin.petugas.monitoring.index')
                ->with('error', 'Monitoring hanya dapat dilakukan untuk penyaluran yang sudah direalisasikan.');
        }

        // Hitung Otomatis Ketepatan Waktu
        $rencana = $penyaluran->tanggal_pengambilan;
        $realisasi = $penyaluran->tanggal_realisasi;
        $ketepatanWaktu = 'Terlambat';
        if ($rencana && $realisasi) {
            $ketepatanWaktu = $realisasi->lte($rencana) ? 'Tepat Waktu' : 'Terlambat';
        }

        // Hitung Otomatis Ketepatan Sasaran
        $namaDisetujui = trim($penyaluran->hasilAkhir->pengajuan->nama ?? '');
        $penerimaAktual = trim($penyaluran->penerima_aktual ?? '');
        $ketepatanSasaran = (strcasecmp($namaDisetujui, $penerimaAktual) === 0) ? 'Sesuai Sasaran' : 'Tidak Sesuai Sasaran';

        return view('petugas.monitoring.create', compact('penyaluran', 'ketepatanWaktu', 'ketepatanSasaran'));
    }

    /**
     * Simpan evaluasi monitoring
     */
    public function store(Request $request, Penyaluran $penyaluran)
    {
        $penyaluran->load(['hasilAkhir.pengajuan']);

        // Guard: Hanya untuk penyaluran yang Sudah Diambil
        if ($penyaluran->status !== 'Sudah Diambil') {
            return redirect()
                ->route('admin.petugas.monitoring.index')
                ->with('error', 'Monitoring hanya dapat dilakukan untuk penyaluran yang sudah direalisasikan.');
        }

        $request->validate([
            'dampak'            => 'required|in:Sangat Membantu,Membantu,Cukup Membantu,Tidak Membantu',
            'keterangan_dampak' => 'required|string|max:1000',
        ], [
            'dampak.required'            => 'Evaluasi dampak bantuan wajib dipilih.',
            'keterangan_dampak.required' => 'Keterangan dampak wajib diisi.',
        ]);

        // Hitung Otomatis ulang untuk integritas data
        $rencana = $penyaluran->tanggal_pengambilan;
        $realisasi = $penyaluran->tanggal_realisasi;
        $ketepatanWaktu = 'Terlambat';
        if ($rencana && $realisasi) {
            $ketepatanWaktu = $realisasi->lte($rencana) ? 'Tepat Waktu' : 'Terlambat';
        }

        $namaDisetujui = trim($penyaluran->hasilAkhir->pengajuan->nama ?? '');
        $penerimaAktual = trim($penyaluran->penerima_aktual ?? '');
        $ketepatanSasaran = (strcasecmp($namaDisetujui, $penerimaAktual) === 0) ? 'Sesuai Sasaran' : 'Tidak Sesuai Sasaran';

        // Simpan data monitoring (atau update jika sudah pernah diisi sebelumnya untuk mekanisme koreksi)
        Monitoring::updateOrCreate(
            ['penyaluran_id' => $penyaluran->id],
            [
                'ketepatan_waktu'   => $ketepatanWaktu,
                'ketepatan_sasaran' => $ketepatanSasaran,
                'dampak'            => $request->dampak,
                'keterangan_dampak' => $request->keterangan_dampak,
                'petugas_id'        => Auth::guard('admin')->user()->users_id,
                'tanggal_monitoring'=> now()->toDateString(),
            ]
        );

        return redirect()
            ->route('admin.petugas.monitoring.index', ['status' => 'Sudah'])
            ->with('success', 'Data monitoring evaluasi berhasil disimpan.');
    }
}
