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
        'nama' => 'required|string|max:255',
        'nisn' => 'required|string|max:20',
        'kontak_ortu' => 'required|string|max:20',
        'kelas' => 'required|integer',
        'jurusan' => 'required',
        'tahun_masuk' => 'required|integer|min:2000|max:2100',
        'status_siswa' => 'required|string'
    ];

    // Relasi: Siswa -> Kelas (Jurusan)
    public function jurusans()
    {
        return $this->belongsTo(Kelas::class, 'jurusan', 'id');
    }

    protected $casts = [
        'id' => 'integer',
        'nisn' => 'string',
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
        // 1. Hitung tagihan kelas saat ini
        $totalTagihanKelasSaatIni = \App\Models\Tagihan::where('kelas', $this->jurusan)->sum('nominal');

        // 2. Hitung SISA tagihan kelas lama yang belum lunas
        $tagihanKelasLama = \App\Models\KasSiswa::where('siswa_id', $this->id)
            ->select('tagihan_id')
            ->groupBy('tagihan_id')
            ->get()
            ->map(function($kas) {
                $tagihan = \App\Models\Tagihan::find($kas->tagihan_id);
                if (!$tagihan) return 0;
                
                // Skip jika tagihan ini adalah tagihan kelas saat ini (sudah dihitung)
                if ($tagihan->kelas == $this->jurusan) return 0;
                
                $totalBayar = \App\Models\KasSiswa::where('siswa_id', $this->id)
                    ->where('tagihan_id', $kas->tagihan_id)
                    ->sum('nominal');
                
                $sisa = $tagihan->nominal - $totalBayar;
                
                // Hanya hitung sisa yang belum lunas
                return $sisa > 0 ? $sisa : 0;
            })
            ->sum();

        // Total = Tagihan kelas saat ini + Sisa tagihan kelas lama
        $totalTagihan = $totalTagihanKelasSaatIni + $tagihanKelasLama;

        // Hitung pembayaran untuk tagihan kelas saat ini saja
        $tagihanKelasSaatIni = \App\Models\Tagihan::where('kelas', $this->jurusan)->pluck('id');
        
        $totalBayar = $this->kasSiswas()
            ->whereIn('tagihan_id', $tagihanKelasSaatIni)
            ->sum('nominal');

        return $totalTagihan > 0 ? round(($totalBayar / $totalTagihan) * 100, 2) : 0;
    }
}

