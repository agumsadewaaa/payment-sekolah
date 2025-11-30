<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KelasSeeder extends Seeder
{
    public function run()
    {
        // safe insert only when table exists and empty
        if (!\Schema::hasTable('tb_kelas')) {
            return;
        }

        if (DB::table('tb_kelas')->count() > 0) {
            return;
        }

        $rows = [
            ['kode' => 'X-TKJ', 'kelas' => '10', 'jurusan' => 'TKJ', 'created_at' => now(), 'updated_at' => now()],
            ['kode' => 'XI-TKJ', 'kelas' => '11', 'jurusan' => 'TKJ', 'created_at' => now(), 'updated_at' => now()],
            ['kode' => 'XII-TKJ', 'kelas' => '12', 'jurusan' => 'TKJ', 'created_at' => now(), 'updated_at' => now()],
            ['kode' => 'X-RPL', 'kelas' => '10', 'jurusan' => 'RPL', 'created_at' => now(), 'updated_at' => now()],
            ['kode' => 'XI-RPL', 'kelas' => '11', 'jurusan' => 'RPL', 'created_at' => now(), 'updated_at' => now()],
            ['kode' => 'XII-RPL', 'kelas' => '12', 'jurusan' => 'RPL', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('tb_kelas')->insert($rows);
    }
}
