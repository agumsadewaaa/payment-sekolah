<?php

namespace App\Exports;

use App\Models\KasSekolah;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KasRangeExport implements FromCollection, WithHeadings, WithStyles, WithEvents, ShouldAutoSize, WithColumnFormatting
{
    protected string $from;
    protected string $to;
    protected Carbon $exportedAt;

    protected int $saldoSebelumnya = 0;
    protected int $totalPendapatan = 0;
    protected int $totalPengeluaran = 0;
    protected int $saldoAkhir = 0;

    public function __construct(string $from, string $to)
    {
        $this->from = $from;
        $this->to   = $to;
        $this->exportedAt = Carbon::now();
    }

    public function collection()
    {
        $fromDate = Carbon::parse($this->from)->startOfDay();
        $toDate   = Carbon::parse($this->to)->endOfDay();

        // Saldo sebelumnya (exclude import excel)
        $this->saldoSebelumnya = (int)(KasSekolah::nonImport()->where('tanggal', '<', $fromDate)
            ->selectRaw("SUM(CASE WHEN tipe='1' THEN nominal WHEN tipe='2' THEN -nominal ELSE 0 END) as saldo")
            ->value('saldo') ?? 0);

        // Data periode (exclude import excel)
        $rows = KasSekolah::nonImport()->whereBetween('tanggal', [$fromDate, $toDate])
            ->orderBy('tanggal','asc')
            ->get(['tanggal','catatan','tipe','nominal']);

        $this->totalPendapatan  = (int)$rows->where('tipe','1')->sum('nominal');
        $this->totalPengeluaran = (int)$rows->where('tipe','2')->sum('nominal');
        $this->saldoRange       = $this->totalPendapatan - $this->totalPengeluaran;
        $this->saldoAkhir       = $this->saldoSebelumnya + $this->totalPendapatan - $this->totalPengeluaran;

        $data = new Collection();

        // Baris saldo sebelumnya (sebagai opening balance)
        $data->push([
            Carbon::parse($fromDate)->subDay(),
            'Saldo sebelumnya',
            null,
            null,
            $this->saldoSebelumnya
        ]);

        // Running balance
        $running = $this->saldoSebelumnya;
        foreach ($rows as $r) {
            $pendapatan  = $r->tipe == '1'  ? (int)$r->nominal : null;
            $pengeluaran = $r->tipe == '2' ? (int)$r->nominal : null;
            $running += ($pendapatan ?? 0) - ($pengeluaran ?? 0);

            $data->push([
                $r->tanggal,
                $r->catatan,
                $pendapatan,
                $pengeluaran,
                $running,
            ]);
        }

        // ===== Footer 5 baris (label di kolom C, nominal di kolom D) =====
        $data->push(['','', 'Total Pendapatan', $this->totalPendapatan,  null]);
        $data->push(['','', 'Total Pengeluaran', $this->totalPengeluaran, null]);
        $data->push(['','', 'Saldo', $this->saldoRange,  null]);          // saldo periode (hijau)
        $data->push(['','', 'Saldo sebelumnya', $this->saldoSebelumnya, null]);
        $data->push(['','', 'Saldo', $this->saldoAkhir,  null]);          // saldo akhir

        return $data;
    }

    public function headings(): array
    {
        $periode = Carbon::parse($this->from)->format('d-M-Y')
            .' s.d. '.Carbon::parse($this->to)->format('d-M-Y');
        
        $exportedAtFormatted = $this->exportedAt->format('d M Y H:i');

        // Generate judul dinamis berdasarkan bulan periode
        $fromCarbon = Carbon::parse($this->from);
        $toCarbon = Carbon::parse($this->to);
        
        $fromMonth = $fromCarbon->format('F'); // Nama bulan penuh dalam bahasa Inggris
        $toMonth = $toCarbon->format('F');
        $fromYear = $fromCarbon->format('Y');
        $toYear = $toCarbon->format('Y');
        
        // Konversi nama bulan ke Bahasa Indonesia
        $bulanIndo = [
            'January' => 'JANUARI',
            'February' => 'FEBRUARI',
            'March' => 'MARET',
            'April' => 'APRIL',
            'May' => 'MEI',
            'June' => 'JUNI',
            'July' => 'JULI',
            'August' => 'AGUSTUS',
            'September' => 'SEPTEMBER',
            'October' => 'OKTOBER',
            'November' => 'NOVEMBER',
            'December' => 'DESEMBER'
        ];
        
        $fromBulan = $bulanIndo[$fromMonth] ?? strtoupper($fromMonth);
        $toBulan = $bulanIndo[$toMonth] ?? strtoupper($toMonth);
        
        // Tentukan format judul
        if ($fromMonth === $toMonth && $fromYear === $toYear) {
            // Bulan dan tahun sama: DESEMBER 2025
            $judulPeriode = "$fromBulan $fromYear";
        } elseif ($fromYear === $toYear) {
            // Tahun sama tapi bulan beda: NOVEMBER-DESEMBER 2025
            $judulPeriode = "$fromBulan-$toBulan $fromYear";
        } else {
            // Tahun berbeda: DESEMBER 2024-JANUARI 2025
            $judulPeriode = "$fromBulan $fromYear-$toBulan $toYear";
        }

        return [
            ["KAS SMK YPE SAMPANG BLN $judulPeriode"],
            [$periode],
            ["Export: $exportedAtFormatted"],
            ['Tanggal','Catatan','Pendapatan','Pengeluaran','Saldo'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Judul + periode + export time
        $sheet->mergeCells('A1:E1');
        $sheet->mergeCells('A2:E2');
        $sheet->mergeCells('A3:E3');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal('center');

        // Header kolom (row 4)
        $sheet->getStyle('A4:E4')->getFont()->setBold(true);
        $sheet->getStyle('A4:E4')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A4:E4')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('D9D9D9');

        // Border seluruh tabel
        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle("A4:E{$highestRow}")
            ->getBorders()->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Perataan
        $sheet->getStyle("C5:E{$highestRow}")->getAlignment()->setHorizontal('right');
        $sheet->getStyle("A5:B{$highestRow}")->getAlignment()->setHorizontal('left');

        // Lebar kolom catatan
        $sheet->getColumnDimension('B')->setWidth(48);

        // Warna isi baris transaksi (bukan footer):
        //   - C (Pendapatan) hijau, D (Pengeluaran) merah
        //   Footer akan dioverride di bawah.
        for ($row = 5; $row <= $highestRow; $row++) {
            $valPend = $sheet->getCell("C{$row}")->getValue();
            $valPeng = $sheet->getCell("D{$row}")->getValue();

            if (!is_null($valPend) && $valPend != 0) {
                $sheet->getStyle("C{$row}")->getFont()->getColor()->setRGB('008000');
            }
            if (!is_null($valPeng) && $valPeng != 0) {
                $sheet->getStyle("D{$row}")->getFont()->getColor()->setRGB('FF0000');
            }
        }

        // ===== Styling khusus FOOTER (5 baris paling bawah) =====
        $footerStart = $highestRow - 4;
        $totalPendRow = $footerStart;
        $totalPengRow = $footerStart + 1;
        $saldoRow1    = $footerStart + 2;
        $saldoSebRow  = $footerStart + 3;
        $saldoRow2    = $footerStart + 4;

        // Bold footer
        $sheet->getStyle("A{$footerStart}:E{$highestRow}")->getFont()->setBold(true);

        // 1) Semua LABEL footer (kolom C) hitam
        $sheet->getStyle("C{$footerStart}:C{$highestRow}")
            ->getFont()->getColor()->setRGB('000000');

        // 2) Angka footer (kolom D) diberi warna sesuai
        $sheet->getStyle("D{$totalPendRow}")->getFont()->getColor()->setRGB('008000'); // pendapatan hijau
        $sheet->getStyle("D{$totalPengRow}")->getFont()->getColor()->setRGB('FF0000'); // pengeluaran merah
        $sheet->getStyle("D{$saldoRow1}")->getFont()->getColor()->setRGB('008000');    // saldo periode hijau
        $sheet->getStyle("D{$saldoSebRow}")->getFont()->getColor()->setRGB('000000');  // saldo sebelumnya hitam
        $sheet->getStyle("D{$saldoRow2}")->getFont()->getColor()->setRGB('000000');    // saldo akhir hitam

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();

                // Freeze header (row 4 is headers, so freeze after row 4)
                $sheet->freezePane('A5');

                // Format tanggal
                $sheet->getStyle("A5:A{$highestRow}")
                      ->getNumberFormat()->setFormatCode('dd-mm-yyyy');
            },
        ];
    }

    public function columnFormats(): array
    {
        // Angka tanpa ,00
        return [
            'C' => '#,##0',
            'D' => '#,##0',
            'E' => '#,##0',
        ];
    }
}
