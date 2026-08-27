<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monitorings', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tabel penyalurans
            $table->unsignedBigInteger('penyaluran_id');
            $table->foreign('penyaluran_id')
                  ->references('id')
                  ->on('penyalurans')
                  ->cascadeOnDelete();

            // Aspek Evaluasi
            $table->enum('ketepatan_waktu', ['Tepat Waktu', 'Terlambat']);
            $table->enum('ketepatan_sasaran', ['Sesuai Sasaran', 'Tidak Sesuai Sasaran']);
            $table->enum('dampak', ['Sangat Membantu', 'Membantu', 'Cukup Membantu', 'Tidak Membantu']);
            $table->text('keterangan_dampak')->nullable();

            // Petugas yang melakukan monitoring & Waktu
            $table->unsignedBigInteger('petugas_id')->nullable();
            $table->foreign('petugas_id')
                  ->references('users_id')
                  ->on('users')
                  ->nullOnDelete();

            $table->date('tanggal_monitoring');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monitorings');
    }
};
