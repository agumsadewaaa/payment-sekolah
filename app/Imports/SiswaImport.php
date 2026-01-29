<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Tagihan;
use App\Models\KasSiswa;
use App\Models\KasSekolah;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class SiswaImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $rows
    */
    public function collection(Collection $rows)
    {
        // Ambil semua tagihan untuk mapping header ke tagihan_id
        $allTagihan = Tagihan::orderBy('kelas')->orderBy('tagihan')->get();
        $tagihanMap = [];
        foreach ($allTagihan as $index => $tagihan) {
            $kelasInfo = Kelas::find($tagihan->kelas);
            $kelasName = $kelasInfo ? $kelasInfo->kelas . ' ' . $kelasInfo->jurusan : 'Kelas ' . $tagihan->kelas;
            $headerText = strtolower(str_replace(' ', '_', $tagihan->tagihan . '_(' . $kelasName . ')'));
            $tagihanMap[$headerText] = $tagihan->id;
        }
        
        \Log::info("Tagihan Map: " . json_encode($tagihanMap));
        
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

            // Insert data siswa
            $siswa = Siswa::create([
                'nama' => trim($row['nama']),
                'nisn' => $nisn,
                'kontak_ortu' => $kontakOrtu,
                'kelas' => $row['kelas'],
                'jurusan' => $kelas->id,
                'tahun_masuk' => $row['tahun_masuk'] ?? date('Y'),
                'tahun_lulus' => null,
                'status_siswa' => 'Aktif',
            ]);
            
            // Proses pembayaran dari kolom Total Bayar
            $tanggalImport = Carbon::now();
            
            if (isset($row['total_bayar']) && is_numeric($row['total_bayar']) && $row['total_bayar'] > 0) {
                $totalBayar = (int) $row['total_bayar'];
                
                // Log untuk debug
                \Log::info("Import Kas - Siswa: {$siswa->nama}, Total Bayar: {$totalBayar}");
                
                // Ambil semua tagihan untuk kelas siswa ini (dari kelas 10 sampai kelas siswa saat ini)
                $kelasRange = range(10, $row['kelas']);
                $kelasIds = Kelas::where('jurusan', $kelas->jurusan)
                    ->whereIn('kelas', $kelasRange)
                    ->pluck('id')
                    ->toArray();
                
                // Ambil semua tagihan untuk kelas-kelas tersebut, urutkan dari kelas rendah ke tinggi
                $tagihanList = Tagihan::whereIn('kelas', $kelasIds)
                    ->orderBy('kelas')
                    ->get();
                
                \Log::info("Tagihan List Count: " . $tagihanList->count() . ", Kelas IDs: " . json_encode($kelasIds));
                
                // Alokasikan pembayaran ke tagihan secara berurutan (kelas 10 -> 11 -> 12, dst)
                $sisaBayar = $totalBayar;
                
                foreach ($tagihanList as $tagihan) {
                    if ($sisaBayar <= 0) {
                        break; // Tidak ada sisa bayar lagi
                    }
                    
                    $nominalTagihan = $tagihan->nominal ?? 0;
                    
                    // Hitung berapa yang akan dibayar untuk tagihan ini
                    $nominalBayar = min($sisaBayar, $nominalTagihan);
                    
                    if ($nominalBayar > 0) {
                        // Simpan ke kas_sekolah
                        $kasSekolah = KasSekolah::create([
                            'tanggal' => $tanggalImport,
                            'catatan' => 'Import Pembayaran: ' . $tagihan->tagihan . ' (Kelas ' . $siswa->kelas . ' ' . $kelas->jurusan . ') - ' . $siswa->nama,
                            'tipe' => 1, // 1 = Pemasukan
                            'metode_pembayaran' => 'Import Excel',
                            'nominal' => $nominalBayar,
                        ]);
                        
                        \Log::info("Kas Sekolah Created - ID: {$kasSekolah->id}, Tagihan: {$tagihan->tagihan}");
                        
                        // Simpan pembayaran ke kas_siswa
                        $statusPembayaran = ($nominalBayar >= $nominalTagihan) ? 'lunas' : 'belum_lunas';
                        
                        $kasSiswa = KasSiswa::create([
                            'kas_sekolah_id' => $kasSekolah->id,
                            'siswa_id' => $siswa->id,
                            'tagihan_id' => $tagihan->id,
                            'tanggal' => $tanggalImport,
                            'metode_pembayaran' => 'Import Excel',
                            'nominal' => $nominalBayar,
                            'status' => $statusPembayaran,
                        ]);
                        
                        \Log::info("Kas Siswa Created - ID: {$kasSiswa->id}, Nominal: {$nominalBayar}, Status: {$statusPembayaran}");
                        
                        // Kurangi sisa bayar
                        $sisaBayar -= $nominalBayar;
                    }
                }
                
                if ($sisaBayar > 0) {
                    \Log::warning("Sisa bayar tidak teralokasi untuk siswa {$siswa->nama}: Rp " . number_format($sisaBayar));
                }
            }
        }
    }
}
