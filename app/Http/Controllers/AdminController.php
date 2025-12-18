<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Imports\SiswaImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin');
    }

    public function promoteAndGraduate(Request $request)
    {
        $year = Carbon::now()->year;

        DB::transaction(function () use ($year) {
            // Ambil semua siswa yang perlu diproses
            $siswa = DB::table('tb_siswa')->get();

            foreach ($siswa as $s) {
                // Tentukan kelas baru
                $kelasBaru = match ($s->kelas) {
                    10 => 11,
                    11 => 12,
                    12 => 0,
                    default => $s->kelas
                };

                // Jika naik kelas (10→11 atau 11→12), cari jurusan baru
                if (in_array($s->kelas, [10, 11])) {
                    // Ambil info kelas lama
                    $kelasLama = DB::table('tb_kelas')->find($s->jurusan);
                    
                    if ($kelasLama) {
                        // Cari kelas baru dengan jurusan yang sama
                        $kelasBaru_obj = DB::table('tb_kelas')
                            ->where('kelas', $kelasBaru)
                            ->where('jurusan', $kelasLama->jurusan)
                            ->first();

                        // Update siswa dengan kelas dan jurusan baru
                        DB::table('tb_siswa')
                            ->where('id', $s->id)
                            ->update([
                                'kelas' => $kelasBaru,
                                'jurusan' => $kelasBaru_obj ? $kelasBaru_obj->id : $s->jurusan,
                                'updated_at' => now(),
                            ]);
                    }
                } elseif ($s->kelas == 12) {
                    // Kelas 12 → Lulus
                    DB::table('tb_siswa')
                        ->where('id', $s->id)
                        ->update([
                            'kelas' => 0,
                            'tahun_lulus' => $year,
                            'status_siswa' => 'Aktif-Lulus',
                            'updated_at' => now(),
                        ]);
                }
            }
        });

        return redirect()
            ->route('admin.index')
            ->with('status', "Kenaikan kelas & kelulusan siswa berhasil diproses untuk tahun {$year}.");
    }

    public function downloadTemplate()
    {
        $filePath = public_path('templates/template_import_siswa.xlsx');
        
        // Jika file tidak ada, generate dulu
        if (!file_exists($filePath)) {
            $this->generateTemplate();
        }

        return response()->download($filePath, 'Template_Import_Siswa.xlsx');
    }

    public function generateTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
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
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
        ];
        $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);

        // PENTING: Set kolom NISN (B) dan Kontak Ortu (C) sebagai TEXT format
        // Ini memastikan Excel tidak mengubah angka dengan 0 di depan
        $sheet->getStyle('B:B')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
        $sheet->getStyle('C:C')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

        // Set contoh data (mulai dari baris 2) - Hanya 2 contoh
        $contohData = [
            ['Budi Santoso', '0051234567', '081234567890', '10', 'Teknologi Komputer Jaringan', '2025'],
            ['Ani Putri', '0051234568', '081298765432', '10', 'Akuntansi', '2025'],
        ];

        $row = 2;
        foreach ($contohData as $data) {
            $col = 'A';
            foreach ($data as $value) {
                // Set NISN dan Kontak sebagai text type
                if ($col === 'B' || $col === 'C') {
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
            '  Kolom ini sudah di-set sebagai TEXT, cukup ketik angka saja: 0051234567',
            '  Angka 0 di depan akan tetap tersimpan',
            '- Kontak Ortu: Nomor HP orang tua (opsional)',
            '  Kolom ini sudah TEXT, ketik langsung: 081234567890',
            '- Kelas: Angka kelas: 10, 11, atau 12 (wajib)',
            '- Jurusan: Nama jurusan sesuai database kelas (wajib)',
            '  Contoh: IPA, IPS, Bahasa, TKJ, RPL, MM, AKL, dll',
            '- Tahun Masuk: Tahun masuk sekolah (opsional, default tahun sekarang)',
            '',
            'CARA MENGGUNAKAN:',
            '1. Hapus 2 baris contoh data (baris 2-3)',
            '2. Isi data siswa mulai dari baris 2',
            '3. Ketik NISN dan Kontak Ortu langsung (TIDAK perlu tanda petik \')',
            '4. Pastikan kombinasi Kelas dan Jurusan sudah ada di database!',
            '5. NISN harus unik dan belum terdaftar',
            '6. Simpan file dan upload di menu Import Excel',
        ];

        $rowKet = $row + 2;
        foreach ($keterangan as $ket) {
            $sheet->setCellValue('A' . $rowKet, $ket);
            $sheet->mergeCells('A' . $rowKet . ':F' . $rowKet);
            $rowKet++;
        }

        // Pastikan folder templates ada
        $templatesDir = public_path('templates');
        if (!file_exists($templatesDir)) {
            mkdir($templatesDir, 0755, true);
        }

        // Save file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save(public_path('templates/template_import_siswa.xlsx'));

        return redirect()->back()->with('success', 'Template berhasil di-generate!');
    }

    public function importSiswa(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:2048'
        ]);

        try {
            Excel::import(new SiswaImport, $request->file('file'));

            return redirect()
                ->route('admin.index')
                ->with('success', 'Data siswa berhasil diimport!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            
            foreach ($failures as $failure) {
                $errors[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
            }

            return redirect()
                ->back()
                ->with('error', 'Import gagal: ' . implode('<br>', $errors));
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }
}
