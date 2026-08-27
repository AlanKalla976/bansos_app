<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'users_id';

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

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function newQuery()
    {
        return parent::newQuery()->whereIn('role', ['admin', 'petugas', 'lurah']);
    }
}