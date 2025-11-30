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
        Schema::create('tb_kas_sekolah', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT
            $table->date('tanggal');
            $table->string('catatan');
            $table->integer('tipe'); // 1 = pemasukan, 2 = pengeluaran (misalnya)
            $table->integer('nominal');
            $table->softDeletes();    // deleted_at
            $table->timestamps();     // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_kas_sekolah');
    }
};
