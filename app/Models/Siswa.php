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
        'nis',
        'kontak_ortu',
        'kelas',
        'jurusan',
        'tahun_masuk',
        'tahun_lulus',
        'status_siswa'
    ];

    public static array $rules = [
        'nama' => 'required|string|max:255',
        'nis' => 'required|string|max:20|unique:tb_siswa,nis',
        'kontak_ortu' => 'required|string|max:20',
        'kelas' => 'required|integer|in:10,11,12',
        'jurusan' => 'required|integer|exists:tb_kelas,id',
        'tahun_masuk' => 'required|integer|min:2000|max:2100',
        'status_siswa' => 'required|string|in:Aktif,Aktif-Lulus,Non-Aktif'
    ];

    // Relasi: Siswa -> Kelas (Jurusan)
    public function jurusans()
    {
        return $this->belongsTo(Kelas::class, 'jurusan', 'id');
    }

    protected $casts = [
        'id' => 'integer',
        'nis' => 'string',
        'kontak_ortu' => 'string',
        'kelas' => 'integer',
        'jurusan' => 'integer',
        'tahun_masuk' => 'integer',
        'tahun_lulus' => 'integer'
    ];

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
        // Cache untuk menghindari query berulang
        static $tagihanCache = [];
        
        // 1. Hitung tagihan kelas saat ini
        if (!isset($tagihanCache[$this->jurusan])) {
            $tagihanCache[$this->jurusan] = \App\Models\Tagihan::where('kelas', $this->jurusan)
                ->pluck('nominal', 'id');
        }
        $tagihanKelasSaatIni = $tagihanCache[$this->jurusan];
        $totalTagihanKelasSaatIni = $tagihanKelasSaatIni->sum();

        // 2. Hitung SISA tagihan kelas lama yang belum lunas
        // Gunakan eager loaded relationship jika tersedia
        $kasSiswaCollection = $this->relationLoaded('kasSiswas') 
            ? $this->kasSiswas 
            : $this->kasSiswas()->with('tagihan')->get();
            
        $pembayaranPerTagihan = $kasSiswaCollection->groupBy('tagihan_id')->map(function($items) {
            return $items->sum('nominal');
        });
        
        $tagihanKelasLama = 0;
        $totalTagihanKelasLama = 0;
        foreach ($pembayaranPerTagihan as $tagihanId => $totalBayar) {
            // Skip jika tagihan ini adalah tagihan kelas saat ini (sudah dihitung)
            if ($tagihanKelasSaatIni->has($tagihanId)) {
                continue;
            }
            
            $tagihan = null;
            // Coba ambil dari eager loaded relationship
            foreach ($kasSiswaCollection as $kas) {
                if ($kas->tagihan_id == $tagihanId && $kas->relationLoaded('tagihan')) {
                    $tagihan = $kas->tagihan;
                    break;
                }
            }
            
            // Fallback ke query jika tidak ada di eager load
            if (!$tagihan) {
                $tagihan = \App\Models\Tagihan::find($tagihanId);
            }
            
            if (!$tagihan) continue;
            
            $totalTagihanKelasLama += $tagihan->nominal;
            $sisa = $tagihan->nominal - $totalBayar;
            // Hanya hitung sisa yang belum lunas
            if ($sisa > 0) {
                $tagihanKelasLama += $sisa;
            }
        }

        // Jika ada sisa tagihan kelas lama yang belum lunas
        // (siswa naik kelas tapi tagihan sebelumnya belum lunas)
        if ($tagihanKelasLama > 0 && $totalTagihanKelasLama > 0) {
            $persentaseSisa = ($tagihanKelasLama / $totalTagihanKelasLama) * 100;
            return '-' . round($persentaseSisa, 2);
        }

        // Total = Tagihan kelas saat ini + Sisa tagihan kelas lama
        $totalTagihan = $totalTagihanKelasSaatIni + $tagihanKelasLama;

        // Hitung pembayaran untuk tagihan kelas saat ini saja
        $totalBayar = 0;
        foreach ($tagihanKelasSaatIni->keys() as $tagihanId) {
            $totalBayar += $pembayaranPerTagihan->get($tagihanId, 0);
        }

        return $totalTagihan > 0 ? round(($totalBayar / $totalTagihan) * 100, 2) : 0;
    }
}

