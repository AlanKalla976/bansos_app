<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kriterias', function (Blueprint $table) {
            $table->id('kriteria_id');
            $table->string('kode_kriteria', 10)->unique();
            $table->string('nama', 100);
            $table->decimal('bobot', 10, 6)->default(0);
            $table->enum('tipe', ['benefit', 'cost']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kriterias');
    }
};