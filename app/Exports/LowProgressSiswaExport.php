<?php

namespace App\Exports;

use App\Models\Siswa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LowProgressSiswaExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        $allSiswa = Siswa::with('jurusans')->get();
        
        return $allSiswa->filter(function($siswa) {
            return $siswa->progress < 50;
        });
    }

    public function headings(): array
    {
        return [
            'Nama Siswa',
            'NISN',
            'Kelas',
            'Kontak Ortu',
            'Progress (%)'
        ];
    }

    public function map($siswa): array
    {
        return [
            $siswa->nama,
            $siswa->nisn,
            $siswa->jurusans ? $siswa->jurusans->kode : '-',
            $siswa->kontak_ortu,
            round($siswa->progress)
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
