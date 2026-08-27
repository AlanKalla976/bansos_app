<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengajuans', function (Blueprint $table) {
            // ID Petugas yang melakukan validasi (nullable karena belum tentu sudah divalidasi)
            $table->unsignedBigInteger('validated_by')->nullable()->after('alasan_penolakan');
            $table->foreign('validated_by')
                  ->references('users_id')
                  ->on('users')
                  ->nullOnDelete();

            // Tanggal validasi dilakukan
            $table->timestamp('validated_at')->nullable()->after('validated_by');
        });
    }

    public function down(): void
    {
        Schema::table('pengajuans', function (Blueprint $table) {
            $table->dropForeign(['validated_by']);
            $table->dropColumn(['validated_by', 'validated_at']);
        });
    }
};
