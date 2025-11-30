<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Only add deleted_at if it doesn't already exist (some installs may have added this column manually)
        if (!Schema::hasColumn('tb_kas_siswa', 'deleted_at')) {
            Schema::table('tb_kas_siswa', function (Blueprint $table) {
                $table->softDeletes(); // tambah deleted_at
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('tb_kas_siswa', 'deleted_at')) {
            Schema::table('tb_kas_siswa', function (Blueprint $table) {
                $table->dropSoftDeletes(); // rollback
            });
        }
    }
};
