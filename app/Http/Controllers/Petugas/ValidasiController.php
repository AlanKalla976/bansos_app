<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\BantuanSosial;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ValidasiController extends Controller
{
    /**
     * Daftar pengajuan yang menunggu atau sudah divalidasi.
     * Petugas hanya melihat pengajuan berstatus Menunggu (dan bisa filter ke yang sudah divalidasi).
     */
    public function index(Request $request)
    {
        $query = Pengajuan::with(['user', 'bantuanSosial', 'validator'])->latest();

        // Default: tampilkan yang Menunggu validasi
        $statusFilter = $request->filled('status') ? $request->status : 'Menunggu';
        $query->where('status', $statusFilter);

        if ($request->filled('bantuan_sosial_id')) {
            $query->where('bantuan_sosial_id', $request->bantuan_sosial_id);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('nik',  'like', '%' . $request->search . '%');
            });
        }

        $pengajuans = $query->paginate(15)->withQueryString();
        $bantuans   = BantuanSosial::orderBy('nama_bantuan')->get();

        // Statistik ringkasan untuk header
        $statsMenunggu    = Pengajuan::where('status', 'Menunggu')->count();
        $statsDiverifikasi = Pengajuan::where('status', 'Diverifikasi')->count();
        $statsDitolak     = Pengajuan::where('status', 'Ditolak')->count();

        return view('petugas.validasi.index', compact(
            'pengajuans',
            'bantuans',
            'statusFilter',
            'statsMenunggu',
            'statsDiverifikasi',
            'statsDitolak'
        ));
    }

    /**
     * Detail pengajuan — untuk diperiksa oleh Petugas.
     */
    public function show(Pengajuan $pengajuan)
    {
        $pengajuan->load(['user', 'bantuanSosial', 'validator']);
        return view('petugas.validasi.show', compact('pengajuan'));
    }

    /**
     * Simpan keputusan validasi: Valid (Diverifikasi) atau Tidak Valid (Ditolak).
     */
    public function validasi(Request $request, Pengajuan $pengajuan)
    {
        // Pastikan pengajuan masih berstatus Menunggu (belum pernah divalidasi)
        if ($pengajuan->status !== 'Menunggu') {
            return redirect()
                ->route('admin.petugas.validasi.show', $pengajuan)
                ->with('error', 'Pengajuan ini sudah pernah divalidasi sebelumnya.');
        }

        $request->validate([
            'keputusan'        => 'required|in:Diverifikasi,Ditolak',
            'alasan_penolakan' => 'required_if:keputusan,Ditolak|nullable|string|max:500',
        ], [
            'keputusan.required'             => 'Keputusan validasi wajib dipilih.',
            'keputusan.in'                   => 'Keputusan tidak valid.',
            'alasan_penolakan.required_if'   => 'Alasan penolakan wajib diisi jika berkas dinyatakan Tidak Valid.',
        ]);

        $pengajuan->update([
            'status'           => $request->keputusan,
            'alasan_penolakan' => $request->keputusan === 'Ditolak' ? $request->alasan_penolakan : null,
            'validated_by'     => (Auth::guard('petugas')->user() ?? Auth::guard('admin')->user())->users_id,
            'validated_at'     => now(),
        ]);

        $pesan = $request->keputusan === 'Diverifikasi'
            ? "Pengajuan atas nama {$pengajuan->nama} dinyatakan Valid dan siap dinilai."
            : "Pengajuan atas nama {$pengajuan->nama} dinyatakan Tidak Valid.";

        return redirect()
            ->route('admin.petugas.validasi.index')
            ->with('success', $pesan);
    }
}
