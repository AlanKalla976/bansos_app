<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Pengajuan;
use App\Models\Penyaluran;
use App\Models\Monitoring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StatusBantuanController extends Controller
{
    public function index()
    {
        // Ambil pengajuan milik user yang login beserta relasi hasil akhir, penyaluran, dan monitoring
        $pengajuans = Pengajuan::with(['bantuanSosial', 'hasilAkhir.penyaluran.monitoring'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('user.status_bantuan.index', compact('pengajuans'));
    }

    public function storeEvaluasi(Request $request, Penyaluran $penyaluran)
    {
        $penyaluran->load(['hasilAkhir.pengajuan']);

        // Guard: Pastikan penyaluran ini milik user yang sedang login
        if (($penyaluran->hasilAkhir->pengajuan->user_id ?? null) !== Auth::id()) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        // Guard: Pastikan statusnya memang Sudah Diambil
        if ($penyaluran->status !== 'Sudah Diambil') {
            return redirect()->back()->with('error', 'Evaluasi hanya dapat dilakukan setelah bantuan diambil.');
        }

        $request->validate([
            'dampak'            => 'required|in:Sangat Membantu,Membantu,Cukup Membantu,Tidak Membantu',
            'keterangan_dampak' => 'required|string|max:1000',
            'foto_penggunaan'   => 'required|image|max:2048',
        ], [
            'dampak.required'            => 'Evaluasi dampak bantuan wajib dipilih.',
            'keterangan_dampak.required' => 'Keterangan dampak wajib diisi.',
            'foto_penggunaan.required'   => 'Foto bukti penggunaan bantuan wajib diunggah.',
            'foto_penggunaan.image'      => 'File bukti penggunaan harus berupa gambar.',
            'foto_penggunaan.max'        => 'Ukuran gambar maksimal adalah 2MB.',
        ]);

        // Hitung Otomatis Ketepatan Waktu dan Sasaran untuk kelengkapan data monitoring
        $rencana = $penyaluran->tanggal_pengambilan;
        $realisasi = $penyaluran->tanggal_realisasi;
        $ketepatanWaktu = 'Terlambat';
        if ($rencana && $realisasi) {
            $ketepatanWaktu = $realisasi->lte($rencana) ? 'Tepat Waktu' : 'Terlambat';
        }

        $namaDisetujui = trim($penyaluran->hasilAkhir->pengajuan->nama ?? '');
        $penerimaAktual = trim($penyaluran->penerima_aktual ?? '');
        $ketepatanSasaran = (strcasecmp($namaDisetujui, $penerimaAktual) === 0) ? 'Sesuai Sasaran' : 'Tidak Sesuai Sasaran';

        $data = [
            'ketepatan_waktu'   => $ketepatanWaktu,
            'ketepatan_sasaran' => $ketepatanSasaran,
            'dampak'            => $request->dampak,
            'keterangan_dampak' => $request->keterangan_dampak,
            'petugas_id'        => null, // Diisi oleh warga sendiri
            'tanggal_monitoring'=> now()->toDateString(),
        ];

        // Simpan file jika diupload
        if ($request->hasFile('foto_penggunaan')) {
            $data['foto_penggunaan'] = $request->file('foto_penggunaan')->store('penggunaan', 'public');
        }

        Monitoring::updateOrCreate(
            ['penyaluran_id' => $penyaluran->id],
            $data
        );

        return redirect()
            ->route('user.statusbantuan.index')
            ->with('success', 'Terima kasih, evaluasi dampak bantuan berhasil disimpan.');
    }
}
