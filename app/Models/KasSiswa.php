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
        'tanggal' => 'datetime',
        'metode_pembayaran' => 'string',
        'nominal' => 'integer',
        'status' => 'string'
    ];

    public static array $rules = [
        'kas_sekolah_id' => 'required|integer|exists:tb_kas_sekolah,id',
        'siswa_id' => 'required|integer|exists:tb_siswa,id',
        'tagihan_id' => 'required|integer|exists:tb_tagihan_siswa,id',
        'tanggal' => 'required|date',
        'metode_pembayaran' => 'nullable|string|max:50',
        'nominal' => 'required|integer|min:1',
        'status' => 'required|string|in:lunas,belum_lunas'
    ];

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
