<?php

namespace App\Exports;

use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\KasSiswa;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SiswaExport implements FromArray, WithHeadings
{
    protected $siswa;

    public function __construct(Siswa $siswa)
    {
        $this->siswa = $siswa;
    }

    public function array(): array
    {
        // 1. Ambil tagihan kelas saat ini
        $tagihanKelasSaatIni = Tagihan::where('kelas', $this->siswa->jurusan)->pluck('id');

        // 2. Ambil tagihan dari kelas lama yang BELUM LUNAS (carry-over)
        $tagihanBelumLunasKelasLama = KasSiswa::where('siswa_id', $this->siswa->id)
            ->select('tagihan_id')
            ->groupBy('tagihan_id')
            ->get()
            ->filter(function($kas) {
                $tagihan = Tagihan::find($kas->tagihan_id);
                if (!$tagihan) return false;
                
                $totalBayar = KasSiswa::where('siswa_id', $this->siswa->id)
                    ->where('tagihan_id', $kas->tagihan_id)
                    ->sum('nominal');
                
                // Hanya ambil yang belum lunas
                return $totalBayar < $tagihan->nominal;
            })
            ->pluck('tagihan_id');

        // Gabungkan tagihan kelas saat ini + carry-over belum lunas
        $allTagihanIds = $tagihanKelasSaatIni->merge($tagihanBelumLunasKelasLama)->unique();

        $tagihans = Tagihan::whereIn('id', $allTagihanIds)->get()->map(function ($tagihan) {
            $totalBayar = KasSiswa::where('siswa_id', $this->siswa->id)
                        ->where('tagihan_id', $tagihan->id)
                        ->sum('nominal');

            $sisa = $tagihan->nominal - $totalBayar;

            return [
                'nama_tagihan' => $tagihan->tagihan,
                'nominal'      => $tagihan->nominal,
                'total_bayar'  => $totalBayar,
                'sisa'         => $sisa > 0 ? $sisa : 0,
                'status'       => $sisa <= 0 ? 'Lunas' : 'Belum Lunas',
            ];
        });

        return $tagihans->toArray();
    }

    public function headings(): array
    {
        return ['Nama Tagihan', 'Nominal', 'Total Bayar', 'Sisa', 'Status'];
    }
}

