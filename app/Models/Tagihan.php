<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    protected $table = 'tb_tagihan_siswa';

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

    public static array $rules = [];

    public function siswas()
    {
        return $this->hasMany(Siswa::class, 'kelas', 'kelas');
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
