<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KasSekolah extends Model
{
    use SoftDeletes;

    public $table = 'tb_kas_sekolah';

    public $fillable = [
        'tanggal',
        'catatan',
        'tipe',
        'metode_pembayaran',
        'nominal'
    ];

    protected $casts = [
        'id' => 'integer',
        'tanggal' => 'date',
        'catatan' => 'string',
        'tipe' => 'integer',
        'metode_pembayaran' => 'string',
        'nominal' => 'integer'
    ];

    public static array $rules = [
        
    ];

    public function kasSiswas()
    {
        return $this->hasMany(KasSiswa::class, 'kas_sekolah_id', 'id');
    }

    // Event agar anak ikut soft delete
    protected static function booted()
    {
        static::deleting(function ($kasSekolah) {
            if ($kasSekolah->isForceDeleting()) {
                $kasSekolah->kasSiswas()->forceDelete();
            } else {
                $kasSekolah->kasSiswas()->delete();
            }
        });

        static::restoring(function ($kasSekolah) {
            $kasSekolah->kasSiswas()->withTrashed()->restore();
        });
    }
}
