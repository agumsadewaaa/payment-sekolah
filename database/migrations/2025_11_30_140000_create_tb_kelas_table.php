<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('tb_kelas')) {
            Schema::create('tb_kelas', function (Blueprint $table) {
                $table->increments('id');
                $table->string('kode', 50)->nullable(false);
                $table->string('kelas', 50)->nullable(false);
                $table->string('jurusan', 100)->nullable(false);
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('tb_kelas')) {
            Schema::dropIfExists('tb_kelas');
        }
    }
};
