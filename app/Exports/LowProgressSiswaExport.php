<?php

namespace App\Exports;

use App\Models\Siswa;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LowProgressSiswaExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithEvents
{
    protected $exportedAt;

    public function __construct()
    {
        $this->exportedAt = Carbon::now();
    }

    public function collection()
    {
        // Eager load relationships untuk menghindari N+1 query
        $allSiswa = Siswa::with(['jurusans', 'kasSiswas.tagihan'])->get();
        
        return $allSiswa->filter(function($siswa) {
            return $siswa->progress < 50;
        });
    }

    public function headings(): array
    {
        return [
            ['SISWA PROGRESS RENDAH (< 50%)'],
            ['Export: ' . $this->exportedAt->format('d M Y H:i')],
            [],
            [
                'Nama Siswa',
                'NIS',
                'Kelas',
                'Kontak Ortu',
                'Progress (%)'
            ]
        ];
    }

    public function map($siswa): array
    {
        return [
            $siswa->nama,
            $siswa->nis,
            $siswa->jurusans ? $siswa->jurusans->kode : '-',
            $siswa->kontak_ortu,
            round($siswa->progress)
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            2 => ['font' => ['italic' => true]],
            4 => ['font' => ['bold' => true]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Merge cells untuk judul
                $sheet->mergeCells('A1:E1');
                $sheet->mergeCells('A2:E2');
                
                // Center alignment untuk judul
                $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal('center');
            },
        ];
    }
}
