<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hasil_akhirs', function (Blueprint $table) {
            // Status persetujuan Lurah (terpisah dari status MOORA)
            $table->enum('persetujuan_status', [
                'Menunggu Persetujuan',
                'Disetujui',
                'Ditolak',
            ])->default('Menunggu Persetujuan')->after('status');

            // Alasan penolakan dari Lurah (wajib diisi jika Ditolak)
            $table->text('alasan_penolakan_lurah')->nullable()->after('persetujuan_status');

            // ID Lurah yang memproses (FK ke users.users_id)
            $table->unsignedBigInteger('persetujuan_oleh')->nullable()->after('alasan_penolakan_lurah');
            $table->foreign('persetujuan_oleh')
                  ->references('users_id')
                  ->on('users')
                  ->nullOnDelete();

            // Waktu persetujuan/penolakan dibuat
            $table->timestamp('persetujuan_at')->nullable()->after('persetujuan_oleh');
        });
    }

    public function down(): void
    {
        Schema::table('hasil_akhirs', function (Blueprint $table) {
            $table->dropForeign(['persetujuan_oleh']);
            $table->dropColumn([
                'persetujuan_status',
                'alasan_penolakan_lurah',
                'persetujuan_oleh',
                'persetujuan_at',
            ]);
        });
    }
};
