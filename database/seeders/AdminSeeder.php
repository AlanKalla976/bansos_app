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
    }
}