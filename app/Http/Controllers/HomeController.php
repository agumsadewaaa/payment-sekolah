<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
        $totalSiswa = DB::table('tb_siswa')->count();

        // Saldo total kas (all-time)
        $totalKas = DB::table('tb_kas_sekolah')
            ->select(DB::raw("
                SUM(CASE WHEN tipe=1 THEN nominal
                         WHEN tipe=2 THEN -nominal
                         ELSE 0 END) AS saldo
            "))->value('saldo');

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

        $pemasukanRange   = (int) $kasPerHari->sum('pemasukan');
        $pengeluaranRange = (int) $kasPerHari->sum('pengeluaran');

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

        // ===== 4) Siswa progress < 50% (punyamu, tanpa ubah) =====
        $subKas = DB::table('tb_kas_siswa')
            ->select('siswa_id', DB::raw('SUM(nominal) as total_bayar'))
            ->whereNull('deleted_at')
            ->groupBy('siswa_id');

        $subTagihan = DB::table('tb_tagihan_siswa')
            ->select('kelas', DB::raw('SUM(nominal) as total_tagihan'))
            ->groupBy('kelas');

        $siswaProgress = DB::table('tb_siswa')
            ->join('tb_kelas', 'tb_siswa.jurusan', '=', 'tb_kelas.id')
            ->leftJoinSub($subKas, 'kas', fn($join) => $join->on('tb_siswa.id','=','kas.siswa_id'))
            ->leftJoinSub($subTagihan, 'tagihan', fn($join) => $join->on('tb_siswa.jurusan','=','tagihan.kelas'))
            ->select(
                'tb_siswa.id','tb_siswa.nama','tb_kelas.kode',
                DB::raw('COALESCE(kas.total_bayar,0) as total_bayar'),
                DB::raw('COALESCE(tagihan.total_tagihan,0) as total_tagihan'),

                // progress selalu angka (0..100), 2 desimal
                DB::raw("
                    COALESCE(
                        ROUND(
                            CASE 
                                WHEN COALESCE(tagihan.total_tagihan,0) > 0 
                                THEN (COALESCE(kas.total_bayar,0) / NULLIF(tagihan.total_tagihan,0)) * 100
                                ELSE 0
                            END
                        , 2)
                    , 0) as progress
                ")
            )
            ->having('progress','<',50)
            ->get();

        // ===== 5) Latest pengeluaran (tetap) =====
        $latestPengeluaran = DB::table('tb_kas_sekolah')
            ->where('tipe',2)
            ->orderBy('tanggal','desc')
            ->limit(5)
            ->get(['tanggal','catatan','nominal']);

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
}
