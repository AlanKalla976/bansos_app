<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bantuan_sosials', function (Blueprint $table) {
            $table->integer('kuota')->default(0)->after('deskripsi');
        });
    }

    public function down(): void
    {
        Schema::table('bantuan_sosials', function (Blueprint $table) {
            $table->dropColumn('kuota');
        });
    }
};