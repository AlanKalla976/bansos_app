<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('users_id');
            $table->char('nik', 16)->unique();
            $table->string('name', 100);
            $table->string('email')->unique();
            $table->string('password', 255);
            $table->enum('role', ['admin', 'masyarakat'])->default('masyarakat');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};