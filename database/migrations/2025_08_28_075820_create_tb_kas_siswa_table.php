<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_kas_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')
                  ->constrained('tb_siswa')
                  ->onDelete('cascade');
            $table->foreignId('tagihan_id')
                  ->constrained('tb_tagihan_siswa')
                  ->onDelete('cascade');
            $table->date('tanggal');
            $table->integer('nominal');
            $table->enum('status', ['lunas', 'belum_lunas'])->default('belum_lunas');
            $table->timestamps();
            $table->softDeletes(); // <-- ini soft delete
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_kas_siswa');
    }
};
