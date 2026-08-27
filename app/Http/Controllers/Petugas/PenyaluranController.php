<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Penyaluran;
use App\Models\BantuanSosial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PenyaluranController extends Controller
{
    /**
     * Tampilkan daftar penjadwalan penyaluran
     */
    public function index(Request $request)
    {
        $query = Penyaluran::with(['hasilAkhir.pengajuan.bantuanSosial', 'petugas'])->latest();

        // Filter status penyaluran
        $statusFilter = $request->filled('status') ? $request->status : 'Belum Dijadwalkan';
        $query->where('status', $statusFilter);

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
        $statsBelum = Penyaluran::where('status', 'Belum Dijadwalkan')->count();
        $statsSudah = Penyaluran::where('status', 'Sudah Dijadwalkan')->count();
        $statsDiambil = Penyaluran::where('status', 'Sudah Diambil')->count();
        $statsTidakDiambil = Penyaluran::where('status', 'Tidak Diambil')->count();

        return view('petugas.penyaluran.index', compact(
            'penyalurans',
            'bantuans',
            'statusFilter',
            'statsBelum',
            'statsSudah',
            'statsDiambil',
            'statsTidakDiambil'
        ));
    }

    /**
     * Form edit jadwal / input jadwal baru
     */
    public function edit(Penyaluran $penyaluran)
    {
        $penyaluran->load(['hasilAkhir.pengajuan.bantuanSosial']);

        // Guard: pastikan hanya yang sudah disetujui Lurah yang boleh dijadwalkan
        if (($penyaluran->hasilAkhir->persetujuan_status ?? '') !== 'Disetujui') {
            return redirect()
                ->route('admin.petugas.penyaluran.index')
                ->with('error', 'Penerima bantuan belum disetujui oleh Lurah.');
        }

        return view('petugas.penyaluran.edit', compact('penyaluran'));
    }

    /**
     * Simpan jadwal penyaluran
     */
    public function update(Request $request, Penyaluran $penyaluran)
    {
        $penyaluran->load(['hasilAkhir']);

        // Guard: pastikan status disetujui Lurah
        if (($penyaluran->hasilAkhir->persetujuan_status ?? '') !== 'Disetujui') {
            return redirect()
                ->route('admin.petugas.penyaluran.index')
                ->with('error', 'Penerima bantuan belum disetujui oleh Lurah.');
        }

        $request->validate([
            'tanggal_pengambilan' => 'required|date|after_or_equal:today',
            'waktu_mulai'         => 'required',
            'waktu_selesai'       => 'required|after:waktu_mulai',
            'lokasi_pengambilan'  => 'required|string|max:255',
            'keterangan'          => 'nullable|string|max:1000',
            'status'              => 'required|in:Sudah Dijadwalkan,Sudah Diambil,Tidak Diambil',
        ], [
            'tanggal_pengambilan.required'  => 'Tanggal pengambilan wajib diisi.',
            'tanggal_pengambilan.after_or_equal' => 'Tanggal pengambilan tidak boleh hari kemarin.',
            'waktu_mulai.required'          => 'Waktu mulai wajib diisi.',
            'waktu_selesai.required'        => 'Waktu selesai wajib diisi.',
            'waktu_selesai.after'           => 'Waktu selesai harus setelah waktu mulai.',
            'lokasi_pengambilan.required'   => 'Lokasi pengambilan wajib diisi.',
        ]);

        $penyaluran->update([
            'tanggal_pengambilan' => $request->tanggal_pengambilan,
            'waktu_mulai'         => $request->waktu_mulai,
            'waktu_selesai'       => $request->waktu_selesai,
            'lokasi_pengambilan'  => $request->lokasi_pengambilan,
            'keterangan'          => $request->keterangan,
            'status'              => $request->status,
            'petugas_id'          => Auth::guard('admin')->user()->users_id,
        ]);

        return redirect()
            ->route('admin.petugas.penyaluran.index', ['status' => $request->status])
            ->with('success', 'Jadwal penyaluran berhasil diperbarui.');
    }

    /**
     * Tampilkan form konfirmasi realisasi pengambilan
     */
    public function showKonfirmasi(Penyaluran $penyaluran)
    {
        $penyaluran->load(['hasilAkhir.pengajuan.bantuanSosial']);

        // Guard: Hanya yang berstatus Sudah Dijadwalkan yang bisa dikonfirmasi
        if ($penyaluran->status !== 'Sudah Dijadwalkan') {
            return redirect()
                ->route('admin.petugas.penyaluran.index', ['status' => $penyaluran->status])
                ->with('error', 'Hanya penyaluran dengan status Sudah Dijadwalkan yang dapat dikonfirmasi.');
        }

        return view('petugas.penyaluran.konfirmasi', compact('penyaluran'));
    }

    /**
     * Simpan konfirmasi realisasi pengambilan bantuan
     */
    public function konfirmasi(Request $request, Penyaluran $penyaluran)
    {
        $penyaluran->load(['hasilAkhir.pengajuan']);

        // Guard: Hanya yang berstatus Sudah Dijadwalkan yang bisa dikonfirmasi
        if ($penyaluran->status !== 'Sudah Dijadwalkan') {
            return redirect()
                ->route('admin.petugas.penyaluran.index')
                ->with('error', 'Hanya penyaluran dengan status Sudah Dijadwalkan yang dapat dikonfirmasi.');
        }

        $namaPenerimaDisetujui = $penyaluran->hasilAkhir->pengajuan->nama ?? '';

        $request->validate([
            'tanggal_realisasi' => 'required|date|before_or_equal:today',
            'waktu_realisasi'   => 'required',
            'penerima_aktual'   => [
                'required',
                'string',
                'max:100',
                // Validasi agar penerima aktual harus sesuai/mengandung nama penerima yang disetujui (case-insensitive)
                function ($attribute, $value, $fail) use ($namaPenerimaDisetujui) {
                    if (stripos($value, $namaPenerimaDisetujui) === false) {
                        $fail('Nama penerima aktual harus sesuai dengan nama penerima yang disetujui (' . $namaPenerimaDisetujui . '). Jika diwakilkan, tulis nama perwakilan dengan menyertakan nama penerima yang disetujui (Contoh: "Budi Santoso (diwakilkan oleh Ani)").');
                    }
                }
            ],
            'keterangan'        => 'nullable|string|max:1000',
            'foto_dokumentasi'  => 'nullable|image|max:2048',
        ], [
            'tanggal_realisasi.required' => 'Tanggal realisasi pengambilan wajib diisi.',
            'tanggal_realisasi.before_or_equal' => 'Tanggal realisasi tidak boleh hari esok.',
            'waktu_realisasi.required'   => 'Waktu realisasi pengambilan wajib diisi.',
            'penerima_aktual.required'   => 'Nama penerima aktual wajib diisi.',
        ]);

        $data = [
            'tanggal_realisasi' => $request->tanggal_realisasi,
            'waktu_realisasi'   => $request->waktu_realisasi,
            'penerima_aktual'   => $request->penerima_aktual,
            'keterangan'        => $request->keterangan,
            'status'            => 'Sudah Diambil',
            'confirmed_by'      => Auth::guard('admin')->user()->users_id,
            'confirmed_at'      => now(),
        ];

        // Upload foto dokumentasi jika disertakan
        if ($request->hasFile('foto_dokumentasi')) {
            $data['foto_dokumentasi'] = $request->file('foto_dokumentasi')->store('realisasi', 'public');
        }

        $penyaluran->update($data);

        return redirect()
            ->route('admin.petugas.penyaluran.index', ['status' => 'Sudah Diambil'])
            ->with('success', 'Konfirmasi pengambilan bantuan berhasil disimpan.');
    }
}
