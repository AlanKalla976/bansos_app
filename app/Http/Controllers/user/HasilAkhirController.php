<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\HasilAkhir;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HasilAkhirController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Cek apakah user sudah pernah mengajukan
        $pengajuanUser = Pengajuan::where('user_id', $user->users_id)->first();

        // Jika belum mengajukan sama sekali
        if (!$pengajuanUser) {
            return view('user.hasilakhir.index', [
                'status'      => 'belum_mengajukan',
                'hasilAkhirs' => collect(),
                'hasilSendiri'=> null,
            ]);
        }

        // Cek apakah pengajuan user sudah ada di hasil_akhirs (sudah dihitung MOORA)
        $hasilSendiri = HasilAkhir::with(['pengajuan.bantuanSosial'])
            ->whereHas('pengajuan', function ($q) use ($user) {
                $q->where('user_id', $user->users_id);
            })
            ->orderBy('ranking')
            ->first();

        // Sudah mengajukan tapi belum dihitung MOORA
        if (!$hasilSendiri) {
            return view('user.hasilakhir.index', [
                'status'       => 'belum_dinilai',
                'pengajuanUser'=> $pengajuanUser,
                'hasilAkhirs'  => collect(),
                'hasilSendiri' => null,
            ]);
        }

        // Sudah mengajukan dan sudah ada hasil MOORA
        // Tampilkan semua hasil dengan fitur search
        $query = HasilAkhir::with(['pengajuan.bantuanSosial'])
            ->orderBy('ranking');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('pengajuan', function ($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('nik', 'like', '%' . $search . '%');
            });
        }

        $hasilAkhirs = $query->get();

        return view('user.hasilakhir.index', [
            'status'       => 'sudah_dinilai',
            'hasilAkhirs'  => $hasilAkhirs,
            'hasilSendiri' => $hasilSendiri,
            'pengajuanUser'=> $pengajuanUser,
        ]);
    }
}