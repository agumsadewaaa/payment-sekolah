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

        // 2. Ambil SEMUA tagihan dari kelas lama yang pernah dibayar (carry-over - termasuk yang lunas)
        $tagihanKelasLama = KasSiswa::where('siswa_id', $this->siswa->id)
            ->select('tagihan_id')
            ->groupBy('tagihan_id')
            ->pluck('tagihan_id');

        // Gabungkan tagihan kelas saat ini + semua tagihan kelas lama
        $allTagihanIds = $tagihanKelasSaatIni->merge($tagihanKelasLama)->unique();

        $result = [];
        
        // Header info siswa
        $result[] = ['DAFTAR TAGIHAN SISWA'];
        $result[] = [];
        $result[] = ['Nama', $this->siswa->nama];
        $result[] = ['NISN', $this->siswa->nisn];
        $result[] = ['Kelas', $this->siswa->kelas];
        $result[] = [];
        
        // Header tabel tagihan
        $result[] = ['Nama Tagihan', 'Nominal', 'Total Bayar', 'Sisa', 'Status'];

        $tagihans = Tagihan::whereIn('id', $allTagihanIds)->orderBy('tagihan')->get();
        
        foreach ($tagihans as $tagihan) {
            $totalBayar = KasSiswa::where('siswa_id', $this->siswa->id)
                        ->where('tagihan_id', $tagihan->id)
                        ->whereNull('deleted_at')
                        ->sum('nominal');

            $sisa = $tagihan->nominal - $totalBayar;

            // Baris tagihan utama
            $result[] = [
                $tagihan->tagihan,
                $tagihan->nominal,
                $totalBayar,
                $sisa > 0 ? $sisa : 0,
                $sisa <= 0 ? 'Lunas' : 'Belum Lunas',
            ];
            
            // Detail pembayaran
            $pembayaran = KasSiswa::where('siswa_id', $this->siswa->id)
                ->where('tagihan_id', $tagihan->id)
                ->whereNull('deleted_at')
                ->orderBy('tanggal', 'asc')
                ->get();
                
            if ($pembayaran->count() > 0) {
                $result[] = ['', 'Detail', 'Tanggal Bayar', 'Metode', 'Jumlah Bayar'];
                foreach ($pembayaran as $i => $p) {
                    $result[] = [
                        '',
                        'Angsuran ' . ($i + 1),
                        \Carbon\Carbon::parse($p->tanggal)->format('d-M-Y'),
                        $p->metode_pembayaran ?? '-',
                        $p->nominal
                    ];
                }
                $result[] = []; // baris kosong pemisah
            }
        }

        return $result;
    }

    public function headings(): array
    {
        return [];
    }
}

