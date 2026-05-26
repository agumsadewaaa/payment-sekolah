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
        'tanggal' => 'datetime',
        'catatan' => 'string',
        'tipe' => 'integer',
        'metode_pembayaran' => 'string',
        'nominal' => 'integer'
    ];

    public static array $rules = [
        'tanggal' => 'required|date',
        'catatan' => 'nullable|string|max:500',
        'tipe' => 'required|integer|in:1,2',
        'metode_pembayaran' => 'nullable|string|max:50',
        'nominal' => 'required|integer|min:1'
    ];

    public function kasSiswas()
    {
        return $this->hasMany(KasSiswa::class, 'kas_sekolah_id', 'id');
    }

    public function scopeNonImport($query)
    {
        return $query->where(function($q) {
            $q->whereNull('metode_pembayaran')->orWhere('metode_pembayaran', '!=', 'Import Excel');
        });
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
