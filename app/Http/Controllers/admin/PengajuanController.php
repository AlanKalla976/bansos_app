<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengajuan;
use App\Models\BantuanSosial;
use App\Models\User;
use App\Exports\PengajuanExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class PengajuanController extends Controller
{
    public function index(Request $request)
    {
        $query = Pengajuan::with(['user', 'bantuanSosial'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('bantuan_sosial_id')) {
            $query->where('bantuan_sosial_id', $request->bantuan_sosial_id);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('nik', 'like', '%' . $request->search . '%');
            });
        }

        $pengajuans = $query->paginate(10)->withQueryString();
        $bantuans   = BantuanSosial::orderBy('nama_bantuan')->get();

        return view('admin.pengajuan.index', compact('pengajuans', 'bantuans'));
    }

    public function create()
    {
        $bantuans = BantuanSosial::orderBy('nama_bantuan')->get();
        $users    = User::where('role', 'masyarakat')->orderBy('name')->get();
        return view('admin.pengajuan.create', compact('bantuans', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id'           => 'required|exists:users,users_id',
            'bantuan_sosial_id' => 'required|exists:bantuan_sosials,id',
            'nama'              => 'required|string|max:100',
            'nik'               => [
                                        'required',
                                        'digits:16',
                                        Rule::unique('pengajuans', 'nik')
                                            ->where(fn ($query) => $query->where('bantuan_sosial_id', $request->bantuan_sosial_id)),
                                    ],
            'alamat'            => 'required|string',
            'no_telepon'        => 'required|string|max:15',
            'jenis_kelamin'     => 'required|in:L,P',
            'tanggal_lahir'     => 'required|date',
            'pendidikan'        => 'required|in:Tidak Sekolah,SD,SMP,SMA/SMK,Diploma,S1,S2,S3',
            'penghasilan'       => 'nullable|numeric|min:0',
            'jumlah_tanggungan' => 'nullable|integer|min:0',
            'pekerjaan'         => 'nullable|string|max:100',
            'kepemilikan_rumah' => 'nullable|string|max:50',
            'foto_ktp'          => 'required|image|max:2048',
            'foto_kk'           => 'required|image|max:2048',
            'foto_sktm'         => 'required|image|max:2048',
            'foto_rumah'        => 'required|image|max:2048',
        ]);

        $data = $request->except(['foto_ktp', 'foto_kk', 'foto_sktm', 'foto_rumah', '_token']);

        foreach (['foto_ktp', 'foto_kk', 'foto_sktm', 'foto_rumah'] as $foto) {
            if ($request->hasFile($foto)) {
                $data[$foto] = $request->file($foto)->store('pengajuan', 'public');
            }
        }

        $data['kepemilikan_aset'] = $request->kepemilikan_aset ?? [];
        $data['status']           = $data['status'] ?? 'Menunggu';

        Pengajuan::create($data);

        return redirect()->route('admin.pengajuan.index')
                         ->with('success', 'Pengajuan berhasil ditambahkan.');
    }

    public function show(Pengajuan $pengajuan)
    {
        $pengajuan->load(['user', 'bantuanSosial']);
        return view('admin.pengajuan.show', compact('pengajuan'));
    }

    public function edit(Pengajuan $pengajuan)
    {
        $bantuans = BantuanSosial::orderBy('nama_bantuan')->get();
        $users    = User::where('role', 'masyarakat')->orderBy('name')->get();
        return view('admin.pengajuan.edit', compact('pengajuan', 'bantuans', 'users'));
    }

    public function update(Request $request, Pengajuan $pengajuan)
    {
        $primaryKey     = $pengajuan->getKey();      // nilai PK row ini
        $primaryKeyName = $pengajuan->getKeyName();  // nama kolom PK sebenarnya

        $request->validate([
            'bantuan_sosial_id' => 'required|exists:bantuan_sosials,id',
            'nama'              => 'required|string|max:100',
            // ✅ NIK boleh sama untuk bantuan_sosial_id yang BEDA,
            //    tapi tetap tidak boleh dobel untuk bantuan_sosial_id yang SAMA.
            //    Row yang sedang diedit dikecualikan dari pengecekan.
            'nik'               => [
                                        'required',
                                        'digits:16',
                                        Rule::unique('pengajuans', 'nik')
                                            ->where(fn ($query) => $query->where('bantuan_sosial_id', $request->bantuan_sosial_id))
                                            ->ignore($primaryKey, $primaryKeyName),
                                    ],
            'alamat'            => 'required|string',
            'no_telepon'        => 'required|string|max:15',
            'jenis_kelamin'     => 'required|in:L,P',
            'tanggal_lahir'     => 'required|date',
            'pendidikan'        => 'required|in:Tidak Sekolah,SD,SMP,SMA/SMK,Diploma,S1,S2,S3',
            'penghasilan'       => 'nullable|numeric|min:0',
            'jumlah_tanggungan' => 'nullable|integer|min:0',
            'pekerjaan'         => 'nullable|string|max:100',
            'kepemilikan_rumah' => 'nullable|string|max:50',
            'status'            => 'required|in:Menunggu,Diverifikasi,Ditolak,Diterima',
            'alasan_penolakan'  => 'nullable|string',
            'foto_ktp'          => 'nullable|image|max:2048',
            'foto_kk'           => 'nullable|image|max:2048',
            'foto_sktm'         => 'nullable|image|max:2048',
            'foto_rumah'        => 'nullable|image|max:2048',
        ]);

        $data = $request->except(['foto_ktp', 'foto_kk', 'foto_sktm', 'foto_rumah', '_token', '_method']);

        foreach (['foto_ktp', 'foto_kk', 'foto_sktm', 'foto_rumah'] as $foto) {
            if ($request->hasFile($foto)) {
                if ($pengajuan->$foto) {
                    Storage::disk('public')->delete($pengajuan->$foto);
                }
                $data[$foto] = $request->file($foto)->store('pengajuan', 'public');
            }
        }

        $data['kepemilikan_aset'] = $request->kepemilikan_aset ?? [];

        // ✅ Hanya update tabel pengajuans, tidak menyentuh tabel users sama sekali
        $pengajuan->update($data);

        return redirect()->route('admin.pengajuan.index')
                         ->with('success', 'Pengajuan berhasil diperbarui.');
    }

    public function destroy(Pengajuan $pengajuan)
    {
        foreach (['foto_ktp', 'foto_kk', 'foto_sktm', 'foto_rumah'] as $foto) {
            if ($pengajuan->$foto) {
                Storage::disk('public')->delete($pengajuan->$foto);
            }
        }

        $pengajuan->delete();

        return redirect()->route('admin.pengajuan.index')
                         ->with('success', 'Pengajuan berhasil dihapus.');
    }

    /**
     * Export data pengajuan ke Excel.
     * Jika $bantuan_sosial_id null -> export semua data.
     */
    public function exportExcel($bantuan_sosial_id = null)
    {
        $bantuan  = $bantuan_sosial_id ? BantuanSosial::find($bantuan_sosial_id) : null;
        $fileName = $bantuan
            ? 'pengajuan-' . Str::slug($bantuan->nama_bantuan) . '-' . now()->format('Ymd_His') . '.xlsx'
            : 'pengajuan-semua-' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new PengajuanExport($bantuan_sosial_id), $fileName);
    }

    /**
     * Export data pengajuan ke PDF.
     * Jika $bantuan_sosial_id null -> export semua data.
     */
    public function exportPdf($bantuan_sosial_id = null)
    {
        $bantuan = $bantuan_sosial_id ? BantuanSosial::find($bantuan_sosial_id) : null;

        $query = Pengajuan::with(['bantuanSosial'])->latest();
        if ($bantuan_sosial_id) {
            $query->where('bantuan_sosial_id', $bantuan_sosial_id);
        }
        $pengajuans = $query->get();

        $fileName = $bantuan
            ? 'pengajuan-' . Str::slug($bantuan->nama_bantuan) . '-' . now()->format('Ymd_His') . '.pdf'
            : 'pengajuan-semua-' . now()->format('Ymd_His') . '.pdf';

        $pdf = Pdf::loadView('admin.pengajuan.pdf', compact('pengajuans', 'bantuan'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download($fileName);
    }
}