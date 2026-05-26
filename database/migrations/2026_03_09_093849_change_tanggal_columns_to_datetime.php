<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tb_kas_sekolah', function (Blueprint $table) {
            $table->dateTime('tanggal')->change();
        });

        Schema::table('tb_kas_siswa', function (Blueprint $table) {
            $table->dateTime('tanggal')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_kas_sekolah', function (Blueprint $table) {
            $table->date('tanggal')->change();
        });

        Schema::table('tb_kas_siswa', function (Blueprint $table) {
            $table->date('tanggal')->change();
        });
    }
};
