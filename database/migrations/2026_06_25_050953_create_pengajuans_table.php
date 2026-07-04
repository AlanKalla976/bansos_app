<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuans', function (Blueprint $table) {
            $table->id();

            // ✅ PERBAIKAN: referensi ke users_id sesuai PK tabel users
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')
                  ->references('users_id')
                  ->on('users')
                  ->nullOnDelete();

            $table->foreignId('bantuan_sosial_id')
                ->constrained('bantuan_sosials')
                ->cascadeOnDelete();

            // Data Identitas
            $table->string('nama', 100);
            $table->string('nik', 16);
            $table->text('alamat');
            $table->string('no_telepon', 15);
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->date('tanggal_lahir');
            $table->enum('pendidikan', [
                'Tidak Sekolah',
                'SD',
                'SMP',
                'SMA/SMK',
                'Diploma',
                'S1',
                'S2',
                'S3',
            ]);

            // Data Ekonomi
            $table->decimal('penghasilan', 15, 2)->nullable();
            $table->integer('jumlah_tanggungan')->nullable();
            $table->string('pekerjaan', 100)->nullable();
            $table->string('kepemilikan_rumah', 50)->nullable();

            // Dokumen
            $table->string('foto_ktp');
            $table->string('foto_kk');
            $table->string('foto_sktm');
            $table->string('foto_rumah');

            // Status
            $table->enum('status', [
                'Menunggu',
                'Diverifikasi',
                'Ditolak',
                'Diterima',
            ])->default('Menunggu');

            $table->text('alasan_penolakan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuans');
    }
};