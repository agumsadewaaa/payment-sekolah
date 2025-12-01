<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tb_kas_siswa') && ! Schema::hasColumn('tb_kas_siswa', 'kas_sekolah_id')) {
            Schema::table('tb_kas_siswa', function (Blueprint $table) {
                $table->unsignedBigInteger('kas_sekolah_id')->nullable()->after('id');
            });

            // add FK if kas_sekolah table exists
            if (Schema::hasTable('tb_kas_sekolah')) {
                Schema::table('tb_kas_siswa', function (Blueprint $table) {
                    $table->foreign('kas_sekolah_id')->references('id')->on('tb_kas_sekolah')->onDelete('set null');
                });
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('tb_kas_siswa') && Schema::hasColumn('tb_kas_siswa', 'kas_sekolah_id')) {
            Schema::table('tb_kas_siswa', function (Blueprint $table) {
                $table->dropForeign(['kas_sekolah_id']);
                $table->dropColumn('kas_sekolah_id');
            });
        }
    }
};
