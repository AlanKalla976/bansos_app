<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hasil_akhirs', function (Blueprint $table) {
            $table->id('hasil_id');
            $table->foreignId('pengajuan_id')->constrained('pengajuans', 'id')->cascadeOnDelete();
            $table->decimal('nilai_yi', 15, 8);
            $table->integer('ranking');
            $table->enum('status', ['Layak', 'Tidak Layak']);
            $table->timestamps();

            $table->unique('pengajuan_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hasil_akhirs');
    }
};