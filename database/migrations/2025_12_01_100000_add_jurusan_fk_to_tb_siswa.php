<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // add temporary jurusan_id column and backfill from existing string value
        if (Schema::hasTable('tb_siswa') && ! Schema::hasColumn('tb_siswa', 'jurusan_id')) {
            Schema::table('tb_siswa', function (Blueprint $table) {
                // tb_kelas.id uses increments() (unsigned INT) so keep compatible type
                $table->unsignedInteger('jurusan_id')->nullable()->after('jurusan');
            });

            // backfill using PHP-level logic to avoid SQL type coercion issues
            $siswas = DB::table('tb_siswa')->select('id', 'jurusan')->get();
            foreach ($siswas as $s) {
                if (is_null($s->jurusan)) continue;

                // find a matching kelas by jurusan name (case-insensitive) first
                $kelas = DB::table('tb_kelas')
                    ->whereRaw('LOWER(jurusan) = ?', [strtolower($s->jurusan)])
                    ->orWhere('kode', $s->jurusan)
                    ->first();

                if ($kelas) {
                    DB::table('tb_siswa')->where('id', $s->id)->update(['jurusan_id' => $kelas->id]);
                }
            }

            // rename old string column so we can safely replace it
            if (Schema::hasColumn('tb_siswa', 'jurusan')) {
                Schema::table('tb_siswa', function (Blueprint $table) {
                    $table->string('jurusan_old')->nullable()->after('jurusan_id');
                });

                // copy values of old jurusan string into jurusan_old
                DB::statement("UPDATE tb_siswa SET jurusan_old = jurusan");

                // drop old jurusan string
                Schema::table('tb_siswa', function (Blueprint $table) {
                    $table->dropColumn('jurusan');
                });
            }

            // rename jurusan_id -> jurusan (now acting as FK int)
            // rename jurusan_id -> jurusan (keeps unsigned integer type compatible with tb_kelas.id)
            Schema::table('tb_siswa', function (Blueprint $table) {
                $table->renameColumn('jurusan_id', 'jurusan');
            });

            // add foreign key constraint (jurusan -> tb_kelas.id)
            if (! Schema::hasColumn('tb_siswa', 'jurusan')) {
                // nothing
            } else {
                Schema::table('tb_siswa', function (Blueprint $table) {
                    // keep jurusan nullable if it couldn't be backfilled
                    $table->foreign('jurusan')->references('id')->on('tb_kelas')->onDelete('set null');
                });
            }
        }
    }

    public function down()
    {
        if (Schema::hasTable('tb_siswa')) {
            Schema::table('tb_siswa', function (Blueprint $table) {
                // drop FK if exists
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
            });

            // remove FK and change column back to string carefully
            if (Schema::hasColumn('tb_siswa', 'jurusan')) {
                Schema::table('tb_siswa', function (Blueprint $table) {
                    $table->dropForeign(['jurusan']);
                });

                // create temporary jurusan_str, copy current int values into it (not ideal but preserves data)
                Schema::table('tb_siswa', function (Blueprint $table) {
                    $table->string('jurusan')->nullable()->change();
                });
            }

            // try to restore old jurusan_old if present
            if (Schema::hasColumn('tb_siswa', 'jurusan_old')) {
                DB::statement("UPDATE tb_siswa SET jurusan = jurusan_old");

                Schema::table('tb_siswa', function (Blueprint $table) {
                    $table->dropColumn('jurusan_old');
                });
            }
        }
    }
};
