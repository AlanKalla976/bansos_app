<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penyalurans', function (Blueprint $table) {
            // Kolom Realisasi Pengambilan Bantuan
            $table->date('tanggal_realisasi')->nullable()->after('keterangan');
            $table->time('waktu_realisasi')->nullable()->after('tanggal_realisasi');
            $table->string('penerima_aktual')->nullable()->after('waktu_realisasi');
            $table->string('foto_dokumentasi')->nullable()->after('penerima_aktual');
            
            // Petugas yang melakukan konfirmasi
            $table->unsignedBigInteger('confirmed_by')->nullable()->after('foto_dokumentasi');
            $table->foreign('confirmed_by')
                  ->references('users_id')
                  ->on('users')
                  ->nullOnDelete();

            $table->timestamp('confirmed_at')->nullable()->after('confirmed_by');
        });
    }

    public function down(): void
    {
        Schema::table('penyalurans', function (Blueprint $table) {
            $table->dropForeign(['confirmed_by']);
            $table->dropColumn([
                'tanggal_realisasi',
                'waktu_realisasi',
                'penerima_aktual',
                'foto_dokumentasi',
                'confirmed_by',
                'confirmed_at'
            ]);
        });
    }
};
