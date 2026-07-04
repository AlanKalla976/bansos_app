<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerbandinganKriteria extends Model
{
    protected $table      = 'perbandingan_kriterias';
    protected $primaryKey = 'perbandingan_id';

    protected $fillable = [
        'kriteria_pertama_id',
        'kriteria_kedua_id',
        'nilai_perbandingan',
    ];

    protected $casts = [
        'nilai_perbandingan' => 'float',
    ];

    public function kriteriaPertama()
    {
        return $this->belongsTo(Kriteria::class, 'kriteria_pertama_id', 'kriteria_id');
    }

    public function kriteriaKedua()
    {
        return $this->belongsTo(Kriteria::class, 'kriteria_kedua_id', 'kriteria_id');
    }
}