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
        $tagihans = Tagihan::where('kelas', $this->siswa->kelas)->get()->map(function ($tagihan) {
            $kas = KasSiswa::where('siswa_id', $this->siswa->id)
                        ->where('tagihan_id', $tagihan->id)
                        ->first();

            return [
                'nama_tagihan' => $tagihan->tagihan,
                'nominal'      => $kas ? $kas->nominal : 0,
                'status'       => $kas && $kas->status === 'lunas' ? 'Lunas' : 'Belum Lunas',
            ];
        });

        return $tagihans->toArray();
    }

    public function headings(): array
    {
        return ['Nama Tagihan', 'Nominal', 'Status'];
    }
}

