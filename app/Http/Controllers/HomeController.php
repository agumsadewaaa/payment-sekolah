<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LowProgressSiswaExport;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        // ===== 1) RANGE =====
        $range = $request->query('range', 'month'); // today|week|month
        [$start, $end, $rangeLabel] = $this->resolveRange($range);

        // ===== 2) KARTU LAIN (tetap) =====
        $totalSiswa = DB::table('tb_siswa')
            ->whereNull('deleted_at')
            ->count();

        // Saldo total kas (all-time)
        $totalKas = DB::table('tb_kas_sekolah')
            ->select(DB::raw("
                SUM(CASE WHEN tipe=1 THEN nominal
                         WHEN tipe=2 THEN -nominal
                         ELSE 0 END) AS saldo
            "))->value('saldo');

        // make sure we always have numeric value (if DB returned null)
        $totalKas = is_null($totalKas) ? 0 : $totalKas;

        // ===== 3) DATA BERDASARKAN RANGE (untuk kartu & grafik) =====
        $kasPerHari = DB::table('tb_kas_sekolah')
            ->select(
                DB::raw("DATE(tanggal) as tgl"),
                DB::raw("SUM(CASE WHEN tipe=1 THEN nominal ELSE 0 END) as pemasukan"),
                DB::raw("SUM(CASE WHEN tipe=2 THEN nominal ELSE 0 END) as pengeluaran")
            )
            ->whereBetween('tanggal', [$start->copy()->startOfDay(), $end->copy()->endOfDay()])
            ->groupBy(DB::raw("DATE(tanggal)"))
            ->orderBy('tgl')
            ->get();

        $pemasukanRange   = $kasPerHari->isEmpty() ? 0 : (int) $kasPerHari->sum('pemasukan');
        $pengeluaranRange = $kasPerHari->isEmpty() ? 0 : (int) $kasPerHari->sum('pengeluaran');

        // Buat label harian lengkap agar bar/line sejajar
        $period = new \DatePeriod($start->copy()->startOfDay(), new \DateInterval('P1D'), $end->copy()->addDay());
        $byDate = $kasPerHari->keyBy('tgl');
        $tanggalBulan = [];
        $dataPemasukan = [];
        $dataPengeluaran = [];
        foreach ($period as $d) {
            $key = $d->format('Y-m-d');
            $tanggalBulan[] = $key;
            $dataPemasukan[]   = isset($byDate[$key]) ? (int)$byDate[$key]->pemasukan   : 0;
            $dataPengeluaran[] = isset($byDate[$key]) ? (int)$byDate[$key]->pengeluaran : 0;
        }

        // ===== 4) Siswa progress < 50% =====
        // Ambil semua siswa dengan progress menggunakan attribute dari model
        $allSiswa = \App\Models\Siswa::with('jurusans')->get();
        
        $siswaProgress = $allSiswa->filter(function($siswa) {
            return $siswa->progress < 50;
        })->map(function($siswa) {
            return (object)[
                'id' => $siswa->id,
                'nama' => $siswa->nama,
                'kode' => $siswa->jurusans ? $siswa->jurusans->kode : '-',
                'progress' => $siswa->progress
            ];
        });

        if ($siswaProgress->isEmpty()) {
            $siswaProgress = null;
        }

        // ===== 5) Latest pengeluaran (tetap) =====
        $latestPengeluaran = DB::table('tb_kas_sekolah')
            ->where('tipe',2)
            ->orderBy('tanggal','desc')
            ->limit(5)
            ->get(['tanggal','catatan','nominal']);

        if ($latestPengeluaran->isEmpty()) {
            $latestPengeluaran = null;
        }

        return view('home', compact(
            'totalSiswa',
            'totalKas',
            'pemasukanRange',
            'pengeluaranRange',
            'tanggalBulan',
            'dataPemasukan',
            'dataPengeluaran',
            'siswaProgress',
            'latestPengeluaran',
            'range',
            'rangeLabel',
            'start',
            'end'
        ));
    }

    /**
     * Resolve time range helper
     * @return array [Carbon $start, Carbon $end, string $label]
     */
    private function resolveRange(string $range): array
    {
        $today = Carbon::today();

        switch ($range) {
            case 'today':
                $start = $today->copy();
                $end   = $today->copy();
                $label = 'Hari Ini';
                break;
            case 'week':
                $start = $today->copy()->startOfWeek(); // Senin
                $end   = $today->copy()->endOfWeek();   // Minggu
                $label = 'Minggu Ini';
                break;
            default: // 'month'
                $start = $today->copy()->startOfMonth();
                $end   = $today->copy()->endOfMonth();
                $label = 'Bulan Ini';
        }
        return [$start, $end, $label];
    }

    public function exportLowProgress()
    {
        return Excel::download(new LowProgressSiswaExport(), 'siswa-progress-rendah-' . date('Y-m-d') . '.xlsx');
    }
}
