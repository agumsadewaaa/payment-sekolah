<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tb_tagihan_siswa', function (Blueprint $table) {
            $table->bigInteger('nominal')->after('tagihan')->default(0);
        });
    }

    public function down()
    {
        Schema::table('tb_tagihan_siswa', function (Blueprint $table) {
            $table->dropColumn('nominal');
        });
    }
};
