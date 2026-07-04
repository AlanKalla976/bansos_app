<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubKriteria extends Model
{
    protected $table      = 'sub_kriterias';
    protected $primaryKey = 'subkriteria_id';

    protected $fillable = [
        'kriteria_id',
        'nama',
        'nilai',
    ];

    protected $casts = [
        'nilai' => 'float',
    ];

    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class, 'kriteria_id', 'kriteria_id');
    }
}