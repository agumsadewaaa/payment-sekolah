<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\Kelas;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class SiswaImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $rows
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            // Stop jika ketemu baris kosong (nama dan nisn kosong)
            if ((empty($row['nama']) || trim($row['nama']) === '') && 
                (empty($row['nisn']) || trim($row['nisn']) === '')) {
                break; // Stop processing completely
            }

            // Validasi manual
            if (empty($row['nama']) || trim($row['nama']) === '') {
                throw new \Exception("Baris " . ($index + 2) . ": Nama wajib diisi");
            }
            if (empty($row['nisn']) || trim($row['nisn']) === '') {
                throw new \Exception("Baris " . ($index + 2) . ": NISN wajib diisi");
            }
            if (empty($row['kelas'])) {
                throw new \Exception("Baris " . ($index + 2) . ": Kelas wajib diisi");
            }
            if (empty($row['jurusan']) || trim($row['jurusan']) === '') {
                throw new \Exception("Baris " . ($index + 2) . ": Jurusan wajib diisi");
            }

            // NISN disimpan sebagai string untuk preserve leading zeros
            // Excel format TEXT sudah menjaga angka 0 di depan
            $nisn = trim((string) $row['nisn']);
            
            // Kontak ortu juga simpan sebagai string
            $kontakOrtu = isset($row['kontak_ortu']) ? trim((string) $row['kontak_ortu']) : null;

            // Cek NISN sudah ada atau belum
            if (Siswa::where('nisn', $nisn)->exists()) {
                throw new \Exception("Baris " . ($index + 2) . ": NISN {$nisn} sudah terdaftar di database");
            }

            // Cari kelas berdasarkan kelas dan jurusan
            $kelas = Kelas::where('kelas', $row['kelas'])
                          ->where('jurusan', trim($row['jurusan']))
                          ->first();

            if (!$kelas) {
                throw new \Exception("Baris " . ($index + 2) . ": Kelas {$row['kelas']} dengan jurusan {$row['jurusan']} tidak ditemukan di database");
            }

            // Insert data
            Siswa::create([
                'nama' => trim($row['nama']),
                'nisn' => $nisn,
                'kontak_ortu' => $kontakOrtu,
                'kelas' => $row['kelas'],
                'jurusan' => $kelas->id,
                'tahun_masuk' => $row['tahun_masuk'] ?? date('Y'),
                'tahun_lulus' => null,
                'status_siswa' => 'Aktif',
            ]);
        }
    }
}
