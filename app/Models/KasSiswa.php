<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KasSiswa extends Model
{
    use SoftDeletes;

    protected $table = 'tb_kas_siswa';

    protected $fillable = [
        'kas_sekolah_id',
        'siswa_id',
        'tagihan_id',
        'tanggal',
        'metode_pembayaran',
        'nominal',
        'status'
    ];

    protected $casts = [
        'id' => 'integer',
        'kas_sekolah_id' => 'integer',
        'siswa_id' => 'integer',
        'tagihan_id' => 'integer',
        'tanggal' => 'date',
        'metode_pembayaran' => 'string',
        'nominal' => 'integer',
        'status' => 'string'
    ];

    public static array $rules = [];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id', 'id');
    }

    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class, 'tagihan_id', 'id');
    }

    public function kasSekolah()
    {
        return $this->belongsTo(KasSekolah::class, 'kas_sekolah_id', 'id');
    }

}
