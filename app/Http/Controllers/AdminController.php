<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Imports\SiswaImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use App\Models\Tagihan;
use App\Models\Kelas;
use App\Models\Siswa;

class AdminController extends Controller
{
    public function index()
    {
        $totalTagihan = Tagihan::count();
        return view('admin', compact('totalTagihan'));
    }

    public function promoteAndGraduate(Request $request)
    {
        $year = Carbon::now()->year;
        $processedCount = 0;
        $errorCount = 0;

        try {
            DB::transaction(function () use ($year, &$processedCount, &$errorCount) {
                // Ambil semua siswa aktif yang perlu diproses
                $siswa = DB::table('tb_siswa')
                    ->whereNull('deleted_at')
                    ->whereIn('kelas', [10, 11, 12])
                    ->get();

                foreach ($siswa as $s) {
                    try {
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

                                if ($kelasBaru_obj) {
                                    // Update siswa dengan kelas dan jurusan baru
                                    DB::table('tb_siswa')
                                        ->where('id', $s->id)
                                        ->update([
                                            'kelas' => $kelasBaru,
                                            'jurusan' => $kelasBaru_obj->id,
                                            'updated_at' => now(),
                                        ]);
                                    $processedCount++;
                                } else {
                                    // Log jika jurusan tidak ditemukan
                                    \Log::warning("Jurusan tidak ditemukan untuk siswa ID {$s->id}, kelas {$kelasBaru}, jurusan {$kelasLama->jurusan}");
                                    $errorCount++;
                                }
                            } else {
                                \Log::warning("Kelas lama tidak ditemukan untuk siswa ID {$s->id}");
                                $errorCount++;
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
                            $processedCount++;
                        }
                    } catch (\Exception $e) {
                        \Log::error("Error processing siswa ID {$s->id}: " . $e->getMessage());
                        $errorCount++;
                    }
                }
            });

            $message = "Kenaikan kelas & kelulusan siswa berhasil diproses untuk tahun {$year}. ";
            $message .= "Total diproses: {$processedCount} siswa.";
            if ($errorCount > 0) {
                $message .= " Terdapat {$errorCount} siswa yang tidak dapat diproses (lihat log).";
            }

            return redirect()
                ->route('admin.index')
                ->with('status', $message);
        } catch (\Exception $e) {
            \Log::error('Error in promoteAndGraduate: ' . $e->getMessage());
            return redirect()
                ->route('admin.index')
                ->with('error', 'Terjadi kesalahan saat memproses kenaikan kelas: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $filePath = public_path('templates/template_import_siswa.xlsx');
        
        // Hapus file lama jika ada
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        
        // Generate template baru (tanpa redirect)
        $this->generateTemplateFile();
        
        // Pastikan file sudah ter-generate
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'Gagal generate template!');
        }

        // Download dengan nama file yang unik agar tidak ter-cache browser
        $downloadName = 'Template_Import_Siswa_' . date('YmdHis') . '.xlsx';
        
        return response()->download($filePath, $downloadName, [
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ])->deleteFileAfterSend(false);
    }

    public function generateTemplate()
    {
        // Generate file
        $this->generateTemplateFile();
        
        return redirect()->back()->with('success', 'Template berhasil di-generate!');
    }

    private function generateTemplateFile()
    {
        // Pastikan folder templates ada
        $templatesDir = public_path('templates');
        if (!file_exists($templatesDir)) {
            mkdir($templatesDir, 0755, true);
        }

        // Jalankan file generate_template.php
        $generateFilePath = public_path('templates/generate_template.php');
        
        if (!file_exists($generateFilePath)) {
            throw new \Exception('File generate_template.php tidak ditemukan!');
        }

        // Execute the generate_template.php file
        ob_start();
        include $generateFilePath;
        $output = ob_get_clean();
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
