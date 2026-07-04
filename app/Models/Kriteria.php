<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kriteria extends Model
{
    protected $table      = 'kriterias';
    protected $primaryKey = 'kriteria_id';

    protected $fillable = [
        'kode_kriteria',
        'nama',
        'bobot',
        'tipe',
    ];

    protected $casts = [
        'bobot' => 'float',
    ];

    public function getRouteKeyName(): string
    {
        return 'kriteria_id';
    }

    public function subKriterias()
    {
        return $this->hasMany(SubKriteria::class, 'kriteria_id', 'kriteria_id');
    }

    public function perbandinganSebagaiPertama()
    {
        return $this->hasMany(PerbandinganKriteria::class, 'kriteria_pertama_id', 'kriteria_id');
    }

    public function perbandinganSebagaiKedua()
    {
        return $this->hasMany(PerbandinganKriteria::class, 'kriteria_kedua_id', 'kriteria_id');
    }
}