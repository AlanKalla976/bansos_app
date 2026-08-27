<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengajuan extends Model
{
    protected $table = 'pengajuans';

    protected $fillable = [
        'user_id',
        'bantuan_sosial_id',
        'nama',
        'nik',
        'alamat',
        'no_telepon',
        'jenis_kelamin',
        'tanggal_lahir',
        'pendidikan',
        'penghasilan',
        'jumlah_tanggungan',
        'pekerjaan',
        'kepemilikan_rumah',
        'foto_ktp',
        'foto_kk',
        'foto_sktm',
        'foto_rumah',
        'status',
        'alasan_penolakan',
        'validated_by',
        'validated_at',
    ];

    protected $casts = [
        'tanggal_lahir'    => 'date',
        'penghasilan'      => 'decimal:2',
        'jumlah_tanggungan'=> 'integer',
        'validated_at'     => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'users_id');
    }

    public function bantuanSosial()
    {
        return $this->belongsTo(BantuanSosial::class, 'bantuan_sosial_id');
    }

    /**
     * Petugas yang melakukan validasi berkas.
     */
    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by', 'users_id');
    }

    /**
     * Helper: apakah berkas sudah divalidasi (Valid atau Tidak Valid)?
     */
    public function sudahDivalidasi(): bool
    {
        return in_array($this->status, ['Diverifikasi', 'Ditolak']);
    }

    /**
     * Helper: apakah pengajuan ini bisa masuk proses penilaian?
     */
    public function bisaDinilai(): bool
    {
        return $this->status === 'Diverifikasi';
    }

    /**
     * Hasil Akhir (MOORA) terkait pengajuan ini.
     */
    public function hasilAkhir()
    {
        return $this->hasOne(HasilAkhir::class, 'pengajuan_id', 'id');
    }
}