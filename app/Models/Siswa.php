<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Siswa extends Model
{
    use SoftDeletes;

    protected $table = 'tb_siswa';

    protected $fillable = [
        'nama',
        'nisn',
        'kontak_ortu',
        'kelas',
        'jurusan',
        'tahun_masuk',
        'tahun_lulus',
        'status_siswa'
    ];

    public static array $rules = [
        'nama' => 'required',
        'nisn' => 'required',
        'kontak_ortu' => 'required',
        'kelas' => 'required',
        'jurusan' => 'required',
        'tahun_masuk' => 'required',
        'status_siswa' => 'required'
    ];

    // Relasi: Siswa -> Kelas (Jurusan)
    public function jurusans()
    {
        return $this->belongsTo(Kelas::class, 'jurusan', 'id');
    }

    // Relasi: Siswa -> Tagihan (via jurusan/kelas)
    public function tagihans()
    {
        return $this->hasMany(Tagihan::class, 'kelas', 'jurusan');
    }

    // Relasi: Siswa -> KasSiswa (riwayat pembayaran)
    public function kasSiswas()
    {
        return $this->hasMany(KasSiswa::class, 'siswa_id', 'id');
    }

    // Helper progress pembayaran
    public function getProgressAttribute()
    {
        $totalTagihan = $this->tagihans->sum('nominal');
        $totalBayar   = $this->kasSiswas->sum('nominal');

        return $totalTagihan > 0 ? round(($totalBayar / $totalTagihan) * 100, 2) : 0;
    }
}

