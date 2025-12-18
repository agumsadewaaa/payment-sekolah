<?php

require __DIR__ . '/../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set header kolom di baris pertama (WAJIB untuk WithHeadingRow)
$headers = [
    'A1' => 'Nama',
    'B1' => 'NISN',
    'C1' => 'Kontak Ortu',
    'D1' => 'Kelas',
    'E1' => 'Jurusan',
    'F1' => 'Tahun Masuk',
];

foreach ($headers as $cell => $value) {
    $sheet->setCellValue($cell, $value);
}

// Style header
$headerStyle = [
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
];
$sheet->getStyle('A1:F1')->applyFromArray($headerStyle);

// Set contoh data (mulai dari baris 2)
$contohData = [
    ['Budi Santoso', "'0051234567", "'081234567890", '10', 'Teknologi Komputer Jaringan', '2025'],
    ['Ani Putri', "'0051234568", "'081298765432", '10', 'Akuntansi', '2025'],
];

$row = 2;
foreach ($contohData as $data) {
    $col = 'A';
    foreach ($data as $value) {
        // Set NISN sebagai text agar 0 di depan tidak hilang
        if ($col === 'B') {
            $sheet->setCellValueExplicit($col . $row, $value, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        } else {
            $sheet->setCellValue($col . $row, $value);
        }
        $col++;
    }
    $row++;
}

// Set lebar kolom
$sheet->getColumnDimension('A')->setWidth(25);
$sheet->getColumnDimension('B')->setWidth(15);
$sheet->getColumnDimension('C')->setWidth(15);
$sheet->getColumnDimension('D')->setWidth(10);
$sheet->getColumnDimension('E')->setWidth(15);
$sheet->getColumnDimension('F')->setWidth(15);

// Set tinggi baris header
$sheet->getRowDimension(1)->setRowHeight(25);

// Add keterangan
$sheet->setCellValue('A' . ($row + 1), 'KETERANGAN:');
$sheet->getStyle('A' . ($row + 1))->getFont()->setBold(true);

$keterangan = [
    '- Nama: Nama lengkap siswa (wajib)',
    '- NISN: Nomor Induk Siswa Nasional (wajib, harus unik, 10-20 digit)',
    '  PENTING: NISN sudah otomatis diformat TEXT di template ini!',
    '  Jangan ubah format kolom NISN agar angka 0 di depan tidak hilang',
    '- Kontak Ortu: Nomor HP orang tua (opsional)',
    '- Kelas: Angka kelas: 10, 11, atau 12 (wajib)',
    '- Jurusan: Nama jurusan sesuai database kelas (wajib)',
    '  Contoh: IPA, IPS, Bahasa, TKJ, RPL, MM, AKL, dll',
    '- Tahun Masuk: Tahun masuk sekolah (opsional, default tahun sekarang)',
    '',
    'CARA MENGGUNAKAN:',
    '1. Hapus 3 baris contoh data (baris 2-4)',
    '2. Isi data siswa mulai dari baris 2',
    '3. Pastikan kombinasi Kelas dan Jurusan sudah ada di database!',
    '4. NISN harus unik dan belum terdaftar',
    '5. Simpan file dan upload di menu Import Excel',
];

$rowKet = $row + 2;
foreach ($keterangan as $ket) {
    $sheet->setCellValue('A' . $rowKet, $ket);
    $sheet->mergeCells('A' . $rowKet . ':F' . $rowKet);
    $rowKet++;
}

// Save file
$writer = new Xlsx($spreadsheet);
$writer->save(__DIR__ . '/template_import_siswa.xlsx');

echo "Template berhasil dibuat!\n";
