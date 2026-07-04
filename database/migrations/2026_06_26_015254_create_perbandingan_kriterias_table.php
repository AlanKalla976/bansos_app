<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perbandingan_kriterias', function (Blueprint $table) {
            $table->id('perbandingan_id');
            $table->foreignId('kriteria_pertama_id')
                ->constrained('kriterias', 'kriteria_id')
                ->cascadeOnDelete();
            $table->foreignId('kriteria_kedua_id')
                ->constrained('kriterias', 'kriteria_id')
                ->cascadeOnDelete();
            $table->decimal('nilai_perbandingan', 10, 6);
            $table->timestamps();

            $table->unique(['kriteria_pertama_id', 'kriteria_kedua_id'], 'unique_pasangan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perbandingan_kriterias');
    }
};