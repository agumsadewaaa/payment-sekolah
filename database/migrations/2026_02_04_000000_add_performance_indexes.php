<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Menambahkan indexes untuk meningkatkan performa query database.
     * Indexes ini akan mempercepat filtering, searching, dan joins.
     */
    public function up(): void
    {
        // ===== tb_siswa indexes =====
        Schema::table('tb_siswa', function (Blueprint $table) {
            // Skip jika index sudah ada (untuk avoid duplicate key error)
            if (!$this->indexExists('tb_siswa', 'idx_siswa_kelas')) {
                $table->index('kelas', 'idx_siswa_kelas');
            }
            if (!$this->indexExists('tb_siswa', 'idx_siswa_jurusan')) {
                $table->index('jurusan', 'idx_siswa_jurusan');
            }
            if (!$this->indexExists('tb_siswa', 'idx_siswa_status')) {
                $table->index('status_siswa', 'idx_siswa_status');
            }
            if (!$this->indexExists('tb_siswa', 'idx_siswa_deleted_at')) {
                $table->index('deleted_at', 'idx_siswa_deleted_at');
            }
            if (!$this->indexExists('tb_siswa', 'idx_siswa_kelas_jurusan')) {
                $table->index(['kelas', 'jurusan'], 'idx_siswa_kelas_jurusan');
            }
        });

        // ===== tb_kas_sekolah indexes =====
        Schema::table('tb_kas_sekolah', function (Blueprint $table) {
            if (!$this->indexExists('tb_kas_sekolah', 'idx_kas_tanggal')) {
                $table->index('tanggal', 'idx_kas_tanggal');
            }
            if (!$this->indexExists('tb_kas_sekolah', 'idx_kas_tipe')) {
                $table->index('tipe', 'idx_kas_tipe');
            }
            if (!$this->indexExists('tb_kas_sekolah', 'idx_kas_deleted_at')) {
                $table->index('deleted_at', 'idx_kas_deleted_at');
            }
            if (!$this->indexExists('tb_kas_sekolah', 'idx_kas_tanggal_tipe')) {
                $table->index(['tanggal', 'tipe'], 'idx_kas_tanggal_tipe');
            }
        });

        // ===== tb_kas_siswa indexes =====
        Schema::table('tb_kas_siswa', function (Blueprint $table) {
            if (!$this->indexExists('tb_kas_siswa', 'idx_kas_siswa_id')) {
                $table->index('siswa_id', 'idx_kas_siswa_id');
            }
            if (!$this->indexExists('tb_kas_siswa', 'idx_kas_tagihan_id')) {
                $table->index('tagihan_id', 'idx_kas_tagihan_id');
            }
            if (!$this->indexExists('tb_kas_siswa', 'idx_kas_sekolah_id')) {
                $table->index('kas_sekolah_id', 'idx_kas_sekolah_id');
            }
            if (!$this->indexExists('tb_kas_siswa', 'idx_kas_siswa_deleted_at')) {
                $table->index('deleted_at', 'idx_kas_siswa_deleted_at');
            }
            if (!$this->indexExists('tb_kas_siswa', 'idx_kas_siswa_tagihan')) {
                $table->index(['siswa_id', 'tagihan_id'], 'idx_kas_siswa_tagihan');
            }
        });

        // ===== tb_tagihan_siswa indexes =====
        Schema::table('tb_tagihan_siswa', function (Blueprint $table) {
            if (!$this->indexExists('tb_tagihan_siswa', 'idx_tagihan_kelas')) {
                $table->index('kelas', 'idx_tagihan_kelas');
            }
        });

        // ===== tb_kelas indexes =====
        Schema::table('tb_kelas', function (Blueprint $table) {
            if (!$this->indexExists('tb_kelas', 'idx_kelas_kode')) {
                $table->index('kode', 'idx_kelas_kode');
            }
            if (!$this->indexExists('tb_kelas', 'idx_kelas_jurusan')) {
                $table->index(['kelas', 'jurusan'], 'idx_kelas_jurusan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_siswa', function (Blueprint $table) {
            $table->dropIndex('idx_siswa_kelas');
            $table->dropIndex('idx_siswa_jurusan');
            $table->dropIndex('idx_siswa_status');
            $table->dropIndex('idx_siswa_deleted_at');
            $table->dropIndex('idx_siswa_kelas_jurusan');
        });

        Schema::table('tb_kas_sekolah', function (Blueprint $table) {
            $table->dropIndex('idx_kas_tanggal');
            $table->dropIndex('idx_kas_tipe');
            $table->dropIndex('idx_kas_deleted_at');
            $table->dropIndex('idx_kas_tanggal_tipe');
        });

        Schema::table('tb_kas_siswa', function (Blueprint $table) {
            $table->dropIndex('idx_kas_siswa_id');
            $table->dropIndex('idx_kas_tagihan_id');
            $table->dropIndex('idx_kas_sekolah_id');
            $table->dropIndex('idx_kas_siswa_deleted_at');
            $table->dropIndex('idx_kas_siswa_tagihan');
        });

        Schema::table('tb_tagihan_siswa', function (Blueprint $table) {
            $table->dropIndex('idx_tagihan_kelas');
        });

        Schema::table('tb_kelas', function (Blueprint $table) {
            $table->dropIndex('idx_kelas_kode');
            $table->dropIndex('idx_kelas_jurusan');
        });
    }

    /**
     * Check if an index exists on a table.
     */
    private function indexExists(string $table, string $indexName): bool
    {
        $connection = Schema::getConnection();
        $doctrineSchemaManager = $connection->getDoctrineSchemaManager();
        $doctrineTable = $doctrineSchemaManager->listTableDetails($table);
        
        return $doctrineTable->hasIndex($indexName);
    }
};
