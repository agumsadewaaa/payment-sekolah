<?php

require __DIR__ . '/../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();

// Koneksi database dari .env
try {
    $conn = new PDO(
        "mysql:host={$_ENV['DB_HOST']};port={$_ENV['DB_PORT']};dbname={$_ENV['DB_DATABASE']}",
        $_ENV['DB_USERNAME'],
        $_ENV['DB_PASSWORD']
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set header kolom di baris pertama (WAJIB untuk WithHeadingRow)
$headers = [
    'A1' => 'Nama',
    'B1' => 'NIS',
    'C1' => 'Kontak Ortu',
    'D1' => 'Kelas',
    'E1' => 'Jurusan',
    'F1' => 'Tahun Masuk',
    'G1' => 'Total Bayar',
    'H1' => 'Sisa Tagihan',
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
$sheet->getStyle('A1:H1')->applyFromArray($headerStyle);

// Query data siswa dari database
$query = "
    SELECT 
        s.id,
        s.nama,
        s.nis,
        s.kontak_ortu,
        s.kelas,
        k.jurusan,
        s.jurusan as jurusan_id,
        s.tahun_masuk
    FROM tb_siswa s
    LEFT JOIN tb_kelas k ON s.jurusan = k.id
    WHERE s.deleted_at IS NULL
    ORDER BY s.kelas, k.jurusan, s.nama
";

$stmt = $conn->prepare($query);
$stmt->execute();
$siswaList = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Jika tidak ada data siswa, tambahkan data contoh
if (empty($siswaList)) {
    $siswaList = [
        [
            'id' => 1,
            'nama' => 'Ahmad Fauzi',
            'nis' => "'0012345678",
            'kontak_ortu' => "'081234567890",
            'kelas' => 10,
            'jurusan' => 'Teknik Komputer Jaringan',
            'jurusan_id' => 1,
            'tahun_masuk' => 2025
        ],
        [
            'id' => 2,
            'nama' => 'Siti Nurhaliza',
            'nis' => "'0012345679",
            'kontak_ortu' => "'081234567891",
            'kelas' => 11,
            'jurusan' => 'Akuntansi',
            'jurusan_id' => 2,
            'tahun_masuk' => 2024
        ],
        [
            'id' => 3,
            'nama' => 'Budi Santoso',
            'nis' => "'0012345680",
            'kontak_ortu' => "'081234567892",
            'kelas' => 12,
            'jurusan' => 'Teknik Komputer Jaringan',
            'jurusan_id' => 4,
            'tahun_masuk' => 2023
        ],
    ];
}

$row = 2;
foreach ($siswaList as $siswa) {
    $siswaId = $siswa['id'];
    $jurusanId = $siswa['jurusan_id']; // ID dari tb_kelas (unique untuk kelas+jurusan)
    $kelasSiswa = $siswa['kelas']; // Nomor kelas siswa (10, 11, 12)
    $namaJurusan = $siswa['jurusan']; // Nama jurusan dari LEFT JOIN atau dummy data
    
    // Ambil nama jurusan dari tb_kelas jika bukan dummy data
    if (count($siswaList) <= 3 && $siswaId <= 3) {
        // Ini dummy data, skip query database
    } else {
        $queryJurusan = "SELECT jurusan FROM tb_kelas WHERE id = :jurusan_id";
        $stmtJurusan = $conn->prepare($queryJurusan);
        $stmtJurusan->execute(['jurusan_id' => $jurusanId]);
        $result = $stmtJurusan->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $namaJurusan = $result['jurusan'];
        }
    }
    
    // Untuk dummy data, gunakan data contoh pembayaran
    if (count($siswaList) <= 3 && $siswaId <= 3) {
        // Dummy data dengan contoh perhitungan
        if ($siswaId == 1) {
            // Kelas 10: Belum bayar
            $totalDibayar = 5000000;
            $sisaTagihan = 0; // Contoh tagihan kelas 10
        } elseif ($siswaId == 2) {
            // Kelas 11: Cicilan (sudah bayar sebagian)
            // Misal tagihan kelas 10: 5jt, kelas 11: 6jt = total 11jt
            // Sudah bayar 7jt, maka 5jt lunas kelas 10, sisa 2jt untuk kelas 11
            $totalDibayar = 7000000;
            $sisaTagihan = 4000000; // Sisa tagihan kelas 11 (6jt - 2jt)
        } else {
            // Kelas 12: Lunas
            $totalDibayar = 15000000; // Sudah bayar semua
            $sisaTagihan = 3000000;
        }
    } else {
        // Data real dari database
        // Ambil semua ID kelas dengan jurusan yang sama dari kelas 10 hingga kelas siswa
        $kelasRange = range(10, $kelasSiswa);
        $queryKelasIds = "
            SELECT id, kelas
            FROM tb_kelas
            WHERE jurusan = :jurusan
            AND kelas IN (" . implode(',', $kelasRange) . ")
            ORDER BY kelas ASC
        ";
        $stmtKelasIds = $conn->prepare($queryKelasIds);
        $stmtKelasIds->execute(['jurusan' => $namaJurusan]);
        $kelasIds = $stmtKelasIds->fetchAll(PDO::FETCH_ASSOC);
        
        // Ambil semua tagihan untuk kelas-kelas tersebut
        $tagihanList = [];
        if (!empty($kelasIds)) {
            $kelasIdList = array_column($kelasIds, 'id');
            $kelasIdMap = array_column($kelasIds, 'kelas', 'id'); // Map id -> nomor kelas
            
            $queryTagihanAll = "
                SELECT id, kelas as kelas_id, COALESCE(nominal, 0) as nominal
                FROM tb_tagihan_siswa
                WHERE kelas IN (" . implode(',', $kelasIdList) . ")
                ORDER BY kelas ASC
            ";
            $stmtTagihanAll = $conn->prepare($queryTagihanAll);
            $stmtTagihanAll->execute();
            $tagihanList = $stmtTagihanAll->fetchAll(PDO::FETCH_ASSOC);
            
            // Tambahkan nomor kelas ke setiap tagihan
            foreach ($tagihanList as &$tagihan) {
                $tagihan['kelas_num'] = $kelasIdMap[$tagihan['kelas_id']] ?? 0;
            }
            unset($tagihan);
            
            // Urutkan berdasarkan nomor kelas
            usort($tagihanList, function($a, $b) {
                return $a['kelas_num'] <=> $b['kelas_num'];
            });
        }
        
        // Hitung Total Bayar (akumulatif dari kelas 10 hingga kelas siswa)
        $tagihanIds = array_column($tagihanList, 'id');
        $totalDibayar = 0;
        
        if (!empty($tagihanIds)) {
            $queryBayar = "
                SELECT COALESCE(SUM(nominal), 0) as total_bayar
                FROM tb_kas_siswa
                WHERE siswa_id = :siswa_id
                AND tagihan_id IN (" . implode(',', $tagihanIds) . ")
                AND deleted_at IS NULL
            ";
            $stmtBayar = $conn->prepare($queryBayar);
            $stmtBayar->execute(['siswa_id' => $siswaId]);
            $totalDibayar = $stmtBayar->fetch(PDO::FETCH_ASSOC)['total_bayar'];
        }
        
        // Hitung total tagihan kumulatif dan alokasikan pembayaran
        $sisaBayar = $totalDibayar;
        $sisaTagihan = 0;
        $totalTagihanKumulatif = 0;
        
        foreach ($tagihanList as $tagihan) {
            $nominalTagihan = $tagihan['nominal'];
            $totalTagihanKumulatif += $nominalTagihan;
            
            if ($tagihan['kelas_num'] == $kelasSiswa) {
                // Ini tagihan kelas siswa saat ini
                if ($sisaBayar >= $nominalTagihan) {
                    // Tagihan kelas ini lunas
                    $sisaTagihan = 0;
                } else {
                    // Tagihan kelas ini belum lunas
                    $sisaTagihan = $nominalTagihan - $sisaBayar;
                }
            } else {
                // Tagihan kelas sebelumnya, kurangi sisa bayar
                $sisaBayar -= $nominalTagihan;
                if ($sisaBayar < 0) $sisaBayar = 0;
            }
        }
    }
    
    // Isi data ke Excel
    $sheet->setCellValue('A' . $row, $siswa['nama']);
    $sheet->setCellValueExplicit('B' . $row, $siswa['nis'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
    $sheet->setCellValueExplicit('C' . $row, $siswa['kontak_ortu'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
    $sheet->setCellValue('D' . $row, $siswa['kelas']);
    $sheet->setCellValue('E' . $row, $siswa['jurusan']);
    $sheet->setCellValue('F' . $row, $siswa['tahun_masuk']);
    $sheet->setCellValue('G' . $row, $totalDibayar);
    $sheet->setCellValue('H' . $row, $sisaTagihan);
    
    // Format currency untuk kolom nominal
    $sheet->getStyle('G' . $row . ':H' . $row)->getNumberFormat()
        ->setFormatCode('#,##0');
    
    $row++;
}

// Set lebar kolom
$sheet->getColumnDimension('A')->setWidth(25);
$sheet->getColumnDimension('B')->setWidth(15);
$sheet->getColumnDimension('C')->setWidth(15);
$sheet->getColumnDimension('D')->setWidth(10);
$sheet->getColumnDimension('E')->setWidth(30);
$sheet->getColumnDimension('F')->setWidth(15);
$sheet->getColumnDimension('G')->setWidth(18);
$sheet->getColumnDimension('H')->setWidth(18);

// Set tinggi baris header
$sheet->getRowDimension(1)->setRowHeight(25);

// Add keterangan
$sheet->setCellValue('A' . ($row + 1), 'KETERANGAN:');
$sheet->getStyle('A' . ($row + 1))->getFont()->setBold(true);

$keterangan = [
    '- Nama: Nama lengkap siswa',
    '- NIS: Nomor Induk Siswa (format text)',
    '- Kontak Ortu: Nomor HP orang tua (opsional)',
    '- Kelas: Angka kelas (10, 11, atau 12)',
    '- Jurusan: Nama jurusan (contoh: Teknik Komputer Jaringan, Akuntansi, Teknik Kendaraan Ringan)',
    '- Tahun Masuk: Tahun masuk sekolah',
    '- Total Bayar: Akumulatif pembayaran dari kelas 10 hingga kelas saat ini (jurusan sama)',
    '- Sisa Tagihan: Sisa tagihan kelas saat ini saja (setelah alokasi prioritas dari kelas rendah)',
];

$rowKet = $row + 2;
foreach ($keterangan as $ket) {
    $sheet->setCellValue('A' . $rowKet, $ket);
    $sheet->mergeCells('A' . $rowKet . ':H' . $rowKet);
    $rowKet++;
}

// Save file
$writer = new Xlsx($spreadsheet);
$writer->save(__DIR__ . '/template_import_siswa.xlsx');

echo "Template berhasil dibuat!\n";
