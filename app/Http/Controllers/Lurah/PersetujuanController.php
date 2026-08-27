<?php

namespace App\Http\Controllers\Lurah;

use App\Http\Controllers\Controller;
use App\Models\BantuanSosial;
use App\Models\HasilAkhir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PersetujuanController extends Controller
{
    /**
     * Daftar calon penerima beserta hasil ranking MOORA.
     * Lurah dapat memfilter berdasarkan status persetujuan dan jenis bantuan.
     */
    public function index(Request $request)
    {
        // Ambil SEMUA data untuk hitung ranking per kuota (sama seperti HasilAkhirController)
        $baseQuery = HasilAkhir::with(['pengajuan.bantuanSosial', 'approvedBy'])
            ->whereHas('pengajuan'); // hanya yang pengajuannya tidak terhapus

        // Filter cari nama / NIK
        if ($request->filled('search')) {
            $search = $request->search;
            $baseQuery->whereHas('pengajuan', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nik',  'like', "%{$search}%");
            });
        }

        // Filter jenis bantuan
        if ($request->filled('jenis_bantuan')) {
            $baseQuery->whereHas('pengajuan', function ($q) use ($request) {
                $q->where('bantuan_sosial_id', $request->jenis_bantuan);
            });
        }

        // Ambil semua, urutkan nilai_yi tertinggi → terendah, hitung ranking in-group
        $allItems = $baseQuery->get()->sortByDesc('nilai_yi')->values();
        $allItems = $this->attachRankingInfo($allItems);

        // Filter status persetujuan SETELAH ranking dihitung
        $statusFilter = $request->input('persetujuan_status', '');
        $filtered = $statusFilter
            ? $allItems->filter(fn($h) => $h->persetujuan_status === $statusFilter)->values()
            : $allItems;

        // Pagination manual
        $perPage = 15;
        $page    = $request->get('page', 1);
        $offset  = ($page - 1) * $perPage;

        $itemsForPage = $filtered->slice($offset, $perPage)->values();

        $hasilAkhirs = new \Illuminate\Pagination\LengthAwarePaginator(
            $itemsForPage,
            $filtered->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $jenisBantuanList = BantuanSosial::pluck('nama_bantuan', 'id');

        // Statistik untuk header cards (dari SEMUA data tanpa filter)
        $allForStats = HasilAkhir::with(['pengajuan.bantuanSosial'])->get()
            ->sortByDesc('nilai_yi')->values();
        $allForStats = $this->attachRankingInfo($allForStats);

        $statsMenunggu  = $allForStats->where('persetujuan_status', 'Menunggu Persetujuan')->count();
        $statsDisetujui = $allForStats->where('persetujuan_status', 'Disetujui')->count();
        $statsDitolak   = $allForStats->where('persetujuan_status', 'Ditolak')->count();

        return view('lurah.persetujuan.index', compact(
            'hasilAkhirs',
            'jenisBantuanList',
            'statusFilter',
            'statsMenunggu',
            'statsDisetujui',
            'statsDitolak'
        ));
    }

    /**
     * Detail calon penerima: data diri, kondisi ekonomi, dokumen,
     * nilai & ranking MOORA, serta form keputusan.
     */
    public function show(HasilAkhir $hasilAkhir)
    {
        $hasilAkhir->load([
            'pengajuan.bantuanSosial',
            'pengajuan.user',
            'approvedBy',
        ]);

        // Hitung ranking in-bantuan untuk ditampilkan
        $siblings = HasilAkhir::whereHas('pengajuan', function ($q) use ($hasilAkhir) {
            $q->where('bantuan_sosial_id',
                $hasilAkhir->pengajuan->bantuan_sosial_id ?? 0);
        })->orderByDesc('nilai_yi')->pluck('hasil_id')->toArray();

        $rankingDalamBantuan = array_search($hasilAkhir->hasil_id, $siblings) + 1;

        // Kuota jenis bantuan untuk menentukan apakah rekomendasi MOORA "Layak"
        $kuota = $hasilAkhir->pengajuan->bantuanSosial->kuota ?? 0;
        $isRekomendasiLayak = $rankingDalamBantuan <= $kuota;

        return view('lurah.persetujuan.show', compact(
            'hasilAkhir',
            'rankingDalamBantuan',
            'isRekomendasiLayak'
        ));
    }

    /**
     * Setujui calon penerima.
     */
    public function setujui(Request $request, HasilAkhir $hasilAkhir)
    {
        if ($hasilAkhir->sudahDiproses()) {
            return redirect()
                ->route('admin.lurah.persetujuan.show', $hasilAkhir->hasil_id)
                ->with('error', 'Calon penerima ini sudah pernah diproses sebelumnya.');
        }

        $lurahId = Auth::guard('admin')->user()->users_id;

        $hasilAkhir->update([
            'persetujuan_status' => 'Disetujui',
            'alasan_penolakan_lurah' => null,
            'persetujuan_oleh'   => $lurahId,
            'persetujuan_at'     => now(),
        ]);

        // Otomatis buat record di penyalurans dengan status Belum Dijadwalkan
        \App\Models\Penyaluran::create([
            'hasil_id' => $hasilAkhir->hasil_id,
            'status'   => 'Belum Dijadwalkan',
        ]);

        return redirect()
            ->route('admin.lurah.persetujuan.index')
            ->with('success', "Calon penerima atas nama {$hasilAkhir->pengajuan->nama} telah disetujui.");
    }

    /**
     * Tolak calon penerima (wajib isi alasan).
     */
    public function tolak(Request $request, HasilAkhir $hasilAkhir)
    {
        if ($hasilAkhir->sudahDiproses()) {
            return redirect()
                ->route('admin.lurah.persetujuan.show', $hasilAkhir->hasil_id)
                ->with('error', 'Calon penerima ini sudah pernah diproses sebelumnya.');
        }

        $request->validate([
            'alasan_penolakan_lurah' => 'required|string|min:10|max:1000',
        ], [
            'alasan_penolakan_lurah.required' => 'Alasan penolakan wajib diisi.',
            'alasan_penolakan_lurah.min'      => 'Alasan penolakan minimal 10 karakter.',
        ]);

        $lurahId = Auth::guard('admin')->user()->users_id;

        $hasilAkhir->update([
            'persetujuan_status'     => 'Ditolak',
            'alasan_penolakan_lurah' => $request->alasan_penolakan_lurah,
            'persetujuan_oleh'       => $lurahId,
            'persetujuan_at'         => now(),
        ]);

        return redirect()
            ->route('admin.lurah.persetujuan.index')
            ->with('success', "Calon penerima atas nama {$hasilAkhir->pengajuan->nama} telah ditolak.");
    }

    // ── Private Helpers ───────────────────────────────────────────────────────

    /**
     * Hitung ranking per jenis bantuan dan ranking global.
     * Tidak mengubah algoritma MOORA — hanya memberi atribut dinamis untuk display.
     */
    private function attachRankingInfo($items)
    {
        // Global ranking
        foreach ($items as $index => $h) {
            $h->global_ranking = $index + 1;
        }

        // Ranking per jenis bantuan
        $grouped = $items->groupBy(fn($h) => $h->pengajuan->bantuanSosial->id ?? 'unknown');
        foreach ($grouped as $group) {
            $sorted = $group->sortByDesc('nilai_yi')->values();
            foreach ($sorted as $index => $h) {
                $h->ranking_in_bantuan = $index + 1;
            }
        }

        return $items;
    }
}
