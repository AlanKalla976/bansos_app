<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HasilAkhir extends Model
{
    protected $table      = 'hasil_akhirs';
    protected $primaryKey = 'hasil_id';

    protected $fillable = [
        'pengajuan_id',
        'nilai_yi',
        'ranking',
        'status',
    ];

    protected $casts = [
        'nilai_yi' => 'float',
    ];

    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class, 'pengajuan_id', 'id');
    }
}