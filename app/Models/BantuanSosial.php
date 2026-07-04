<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BantuanSosial extends Model
{
    protected $table = 'bantuan_sosials';

    protected $fillable = [
        'nama_bantuan',
        'deskripsi',
    ];
}