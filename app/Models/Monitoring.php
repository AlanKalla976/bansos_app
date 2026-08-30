<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Monitoring extends Model
{
    protected $table = 'monitorings';

    protected $fillable = [
        'penyaluran_id',
        'ketepatan_waktu',
        'ketepatan_sasaran',
        'dampak',
        'keterangan_dampak',
        'foto_penggunaan',
        'petugas_id',
        'tanggal_monitoring',
    ];

    protected $casts = [
        'tanggal_monitoring' => 'date',
    ];

    public function penyaluran()
    {
        return $this->belongsTo(Penyaluran::class, 'penyaluran_id');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id', 'users_id');
    }
}
