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
    ];

    protected $casts = [
        'tanggal_lahir'    => 'date',
        'penghasilan'      => 'decimal:2',
        'jumlah_tanggungan'=> 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'users_id');
    }

    public function bantuanSosial()
    {
        return $this->belongsTo(BantuanSosial::class, 'bantuan_sosial_id');
    }
}