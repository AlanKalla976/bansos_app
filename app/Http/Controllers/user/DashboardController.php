<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Pengajuan;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $pengajuanQuery = Pengajuan::where('user_id', $user->users_id);

        $totalPengajuan      = (clone $pengajuanQuery)->count();
        $pengajuanMenunggu   = (clone $pengajuanQuery)->where('status', 'Menunggu')->count();
        $pengajuanVerifikasi = (clone $pengajuanQuery)->where('status', 'Diverifikasi')->count();
        $pengajuanDitolak    = (clone $pengajuanQuery)->where('status', 'Ditolak')->count();

        $riwayatPengajuan = Pengajuan::where('user_id', $user->users_id)
            ->with('bantuanSosial')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.dashboard.index', compact(
            'user',
            'totalPengajuan',
            'pengajuanMenunggu',
            'pengajuanVerifikasi',
            'pengajuanDitolak',
            'riwayatPengajuan',
        ));
    }
}