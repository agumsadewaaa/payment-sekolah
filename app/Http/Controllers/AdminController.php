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
            DB::table('tb_siswa')->update([
                'kelas' => DB::raw("CASE kelas
                    WHEN 10 THEN 11
                    WHEN 11 THEN 12
                    WHEN 12 THEN 0
                    ELSE kelas
                END"),
                'tahun_lulus' => DB::raw("CASE
                    WHEN kelas = 12 THEN {$year}
                    ELSE tahun_lulus
                END"),
                'status_siswa' => DB::raw("CASE
                    WHEN kelas = 12 THEN 'Aktif-Lulus'
                    ELSE status_siswa
                END"),
                'updated_at' => now(),
            ]);
        });

        return redirect()
            ->route('admin.index')
            ->with('status', "Kenaikan kelas & kelulusan siswa berhasil diproses untuk tahun {$year}.");
    }
}
