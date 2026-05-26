<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    protected $table = 'tb_tagihan_siswa';
    public $timestamps = true;  // Ensure timestamps are managed

    protected $fillable = [
        'kelas',
        'tagihan',
        'nominal'
    ];

    protected $casts = [
        'id' => 'integer',
        'kelas' => 'integer',
        'tagihan' => 'string',
        'nominal' => 'integer'
    ];

    public static array $rules = [
        'kelas' => 'required|integer|exists:tb_kelas,id',
        'tagihan' => 'required|string|max:255',
        'nominal' => 'required|integer|min:1'
    ];

    public function siswas()
    {
        // Tagihan.kelas stores tb_kelas.id, and Siswa.jurusan will be the FK to tb_kelas.id
        return $this->hasMany(Siswa::class, 'jurusan', 'kelas');
    }

    public function kasSiswa()
    {
        return $this->hasMany(KasSiswa::class, 'tagihan_id', 'id');
    }

    public function kelass()
    {
        return $this->belongsTo(Kelas::class, 'kelas', 'id');
    }
}
