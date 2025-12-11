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
        Schema::create('tb_siswa', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT
            $table->string('nama');
            $table->string('nisn'); // Changed from bigInteger to string to preserve leading zeros
            $table->string('kontak_ortu')->nullable();
            $table->integer('kelas'); // values: 10, 11, 12
            $table->string('jurusan');
            $table->integer('tahun_masuk');
            $table->integer('tahun_lulus')->nullable();
            $table->string('status_siswa')->default('aktif');

            $table->timestamps();     // created_at, updated_at
            $table->softDeletes();    // deleted_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_siswa');
    }
};
