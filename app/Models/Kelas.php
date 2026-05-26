<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    public $table = 'tb_kelas';
    public $timestamps = true;  // Ensure timestamps are managed

    public $fillable = [
        'kode',
        'kelas',
        'jurusan'
    ];

    protected $casts = [
        'id' => 'integer',
        'kode' => 'string',
        'kelas' => 'string',
        'jurusan' => 'string'
    ];

    public static array $rules = [
        'kode' => 'required',
        'kelas' => 'required',
        'jurusan' => 'required'
    ];

    public function siswas()
    {
        return $this->hasMany(Siswa::class, 'jurusan', 'id');
    }

    public function tagihans()
    {
        return $this->hasMany(Tagihan::class, 'kelas', 'id');
    }

    
}

