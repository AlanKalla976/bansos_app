<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table      = 'users';
    protected $primaryKey = 'users_id';
    public    $incrementing = true;
    protected $keyType    = 'int';

    protected $fillable = [
        'nik',
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    public function pengajuans()
    {
        return $this->hasMany(Pengajuan::class, 'user_id', 'users_id');
    }
}