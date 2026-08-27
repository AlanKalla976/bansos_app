<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Gunakan ALTER TABLE SQL langsung untuk merubah type ENUM kolom role agar aman di semua driver (MySQL/SQLite)
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'masyarakat', 'petugas', 'lurah') DEFAULT 'masyarakat'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'masyarakat') DEFAULT 'masyarakat'");
    }
};
