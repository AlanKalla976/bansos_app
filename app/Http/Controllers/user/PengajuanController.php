<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BantuanSosial;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PengajuanController extends Controller
{
    public function index()
    {
        $user     = Auth::user();
        $bantuans = BantuanSosial::orderBy('nama_bantuan')->get();

        $pengajuanMap = Pengajuan::where('user_id', $user->users_id)
            ->get()
            ->keyBy('bantuan_sosial_id');

        return view('user.pengajuan.index', compact('bantuans', 'pengajuanMap'));
    }

    public function create($bantuan_sosial_id)
    {
        $user           = Auth::user();
        $bantuan_sosial = BantuanSosial::findOrFail($bantuan_sosial_id);

        // Cek apakah user sudah pernah mengajukan bantuan ini
        $exists = Pengajuan::where('user_id', $user->users_id)
            ->where('bantuan_sosial_id', $bantuan_sosial_id)
            ->exists();

        if ($exists) {
            return redirect()->route('user.pengajuan.index')
                ->with('error', 'Anda sudah pernah mengajukan bantuan "' . $bantuan_sosial->nama_bantuan . '". Silakan pilih jenis bantuan lainnya.');
        }

        return view('user.pengajuan.create', compact('bantuan_sosial', 'user'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'bantuan_sosial_id'  => 'required|exists:bantuan_sosials,id',
            'nama'               => 'required|string|max:100',
            'nik'                => 'required|string|max:20',
            'alamat'             => 'required|string|max:255',
            'no_telepon'         => 'required|string|max:20',
            'jenis_kelamin'      => 'required|in:L,P',
            'tanggal_lahir'      => 'required|date',
            'pendidikan'         => 'required|string|max:50',
            'penghasilan'        => 'required|numeric|min:0',
            'jumlah_tanggungan'  => 'required|integer|min:0',
            'pekerjaan'          => 'required|string|max:100',
            'kepemilikan_rumah'  => 'required|string|max:50',
            'kepemilikan_aset'   => 'nullable|array',
            'kepemilikan_aset.*' => 'string|max:100',
            'foto_ktp'           => 'required|image|max:2048',
            'foto_kk'            => 'required|image|max:2048',
            'foto_sktm'          => 'required|image|max:2048',
            'foto_rumah'         => 'required|image|max:2048',
        ]);

        // Cek duplikasi: user hanya boleh mengajukan 1x per jenis bantuan
        $exists = Pengajuan::where('user_id', $user->users_id)
            ->where('bantuan_sosial_id', $request->bantuan_sosial_id)
            ->exists();

        if ($exists) {
            $bantuan = BantuanSosial::find($request->bantuan_sosial_id);
            return redirect()->route('user.pengajuan.index')
                ->with('error', 'Anda sudah pernah mengajukan bantuan "' . ($bantuan->nama_bantuan ?? '') . '". Silakan pilih jenis bantuan lainnya.');
        }

        $fotos = [];
        foreach (['foto_ktp', 'foto_kk', 'foto_sktm', 'foto_rumah'] as $foto) {
            $fotos[$foto] = $request->file($foto)->store('pengajuan', 'public');
        }

        $pengajuan = Pengajuan::create([
            'user_id'           => $user->users_id,
            'bantuan_sosial_id' => $request->bantuan_sosial_id,
            'nama'              => $request->nama,
            'nik'               => $request->nik,
            'alamat'            => $request->alamat,
            'no_telepon'        => $request->no_telepon,
            'jenis_kelamin'     => $request->jenis_kelamin,
            'tanggal_lahir'     => $request->tanggal_lahir,
            'pendidikan'        => $request->pendidikan,
            'penghasilan'       => $request->penghasilan,
            'jumlah_tanggungan' => $request->jumlah_tanggungan,
            'pekerjaan'         => $request->pekerjaan,
            'kepemilikan_rumah' => $request->kepemilikan_rumah,
            'kepemilikan_aset'  => $request->kepemilikan_aset ?? [],
            'status'            => 'Menunggu',
            ...$fotos,
        ]);

        return redirect()->route('user.pengajuan.success', $pengajuan->id);
    }

    public function success($id)
    {
        $pengajuan = Pengajuan::with('bantuanSosial')
            ->where('user_id', Auth::user()->users_id)
            ->findOrFail($id);

        return view('user.pengajuan.success', compact('pengajuan'));
    }

    public function show($id)
    {
        $pengajuan = Pengajuan::with('bantuanSosial')
            ->where('user_id', Auth::user()->users_id)
            ->findOrFail($id);

        return view('user.pengajuan.show', compact('pengajuan'));
    }
}