<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penyaluran extends Model
{
    protected $table = 'penyalurans';

    protected $fillable = [
        'hasil_id',
        'tanggal_pengambilan',
        'waktu_mulai',
        'waktu_selesai',
        'lokasi_pengambilan',
        'keterangan',
        'status',
        'petugas_id',
        'tanggal_realisasi',
        'waktu_realisasi',
        'penerima_aktual',
        'foto_dokumentasi',
        'confirmed_by',
        'confirmed_at',
    ];

    protected $casts = [
        'tanggal_pengambilan' => 'date',
        'tanggal_realisasi'   => 'date',
        'confirmed_at'        => 'datetime',
    ];

    public function hasilAkhir()
    {
        return $this->belongsTo(HasilAkhir::class, 'hasil_id', 'hasil_id');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id', 'users_id');
    }

    public function confirmer()
    {
        return $this->belongsTo(User::class, 'confirmed_by', 'users_id');
    }

    public function monitoring()
    {
        return $this->hasOne(Monitoring::class, 'penyaluran_id');
    }
}
