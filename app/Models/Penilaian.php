<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penilaian extends Model
{
    protected $table      = 'penilaians';
    protected $primaryKey = 'penilaian_id';

    protected $fillable = [
        'pengajuan_id',
        'kriteria_id',
        'subkriteria_id',
        'nilai',
    ];

    protected $casts = [
        'nilai' => 'float',
    ];

    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class, 'pengajuan_id', 'id');
    }

    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class, 'kriteria_id', 'kriteria_id');
    }

    public function subKriteria()
    {
        return $this->belongsTo(SubKriteria::class, 'subkriteria_id', 'subkriteria_id');
    }
}