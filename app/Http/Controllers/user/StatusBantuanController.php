<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StatusBantuanController extends Controller
{
    public function index()
    {
        // Ambil pengajuan milik user yang login beserta relasi hasil akhir dan penyaluran
        $pengajuans = Pengajuan::with(['bantuanSosial', 'hasilAkhir.penyaluran'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('user.status_bantuan.index', compact('pengajuans'));
    }
}
