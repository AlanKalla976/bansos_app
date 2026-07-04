<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penilaians', function (Blueprint $table) {
            $table->id('penilaian_id');
            $table->foreignId('pengajuan_id')->constrained('pengajuans', 'id')->cascadeOnDelete();
            $table->foreignId('kriteria_id')->constrained('kriterias', 'kriteria_id')->cascadeOnDelete();
            $table->foreignId('subkriteria_id')->constrained('sub_kriterias', 'subkriteria_id')->cascadeOnDelete();
            $table->decimal('nilai', 10, 4);
            $table->timestamps();

            $table->unique(['pengajuan_id', 'kriteria_id'], 'unique_penilaian');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaians');
    }
};