<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
}
