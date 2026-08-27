<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@bansos.com'],
            [
                'nik'      => '3273010101900001',
                'name'     => 'Administrator',
                'email'    => 'admin@bansos.com',
                'password' => Hash::make('admin123'),
                'role'     => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'petugas@bansos.com'],
            [
                'nik'      => '3273010101900002',
                'name'     => 'Petugas Bansos',
                'email'    => 'petugas@bansos.com',
                'password' => Hash::make('petugas123'),
                'role'     => 'petugas',
            ]
        );

        User::updateOrCreate(
            ['email' => 'lurah@bansos.com'],
            [
                'nik'      => '3273010101900003',
                'name'     => 'Lurah Harjamukti',
                'email'    => 'lurah@bansos.com',
                'password' => Hash::make('lurah123'),
                'role'     => 'lurah',
            ]
        );
    }
}