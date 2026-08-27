<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penyalurans', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke hasil_akhirs (calon penerima yang disetujui Lurah)
            $table->unsignedBigInteger('hasil_id');
            $table->foreign('hasil_id')
                  ->references('hasil_id')
                  ->on('hasil_akhirs')
                  ->cascadeOnDelete();

            // Data Penjadwalan
            $table->date('tanggal_pengambilan')->nullable();
            $table->time('waktu_mulai')->nullable();
            $table->time('waktu_selesai')->nullable();
            $table->string('lokasi_pengambilan')->nullable();
            $table->text('keterangan')->nullable();

            // Status Penyaluran
            $table->enum('status', [
                'Belum Dijadwalkan',
                'Sudah Dijadwalkan',
                'Sudah Diambil',
                'Tidak Diambil'
            ])->default('Belum Dijadwalkan');

            // Petugas yang menjadwalkan/mengupdate
            $table->unsignedBigInteger('petugas_id')->nullable();
            $table->foreign('petugas_id')
                  ->references('users_id')
                  ->on('users')
                  ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penyalurans');
    }
};
