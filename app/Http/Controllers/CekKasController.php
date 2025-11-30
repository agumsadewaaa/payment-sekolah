<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\KasRangeExport;
use Carbon\Carbon;
use App\Models\KasSekolah;

class CekKasController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->get('from');
        $to   = $request->get('to');

        $rows = collect();
        $summary = [
            'saldo_sebelumnya' => 0,
            'total_pendapatan' => 0,
            'total_pengeluaran'=> 0,
            'saldo_akhir'      => 0,
        ];

        if ($from && $to) {
            $fromDate = Carbon::parse($from)->startOfDay();
            $toDate   = Carbon::parse($to)->endOfDay();

            // Saldo sebelum 'from'
            $saldoSebelumnya = KasSekolah::where('tanggal', '<', $fromDate)
                ->selectRaw("
                    SUM(CASE WHEN tipe = '1' THEN nominal 
                             WHEN tipe = '2' THEN -nominal 
                             ELSE 0 END) as saldo
                ")
                ->value('saldo') ?? 0;

            // Data pada rentang
            $rows = KasSekolah::whereBetween('tanggal', [$fromDate, $toDate])
                ->orderBy('tanggal', 'asc')
                ->get();

            $totalPendapatan = $rows->where('tipe', '1')->sum('nominal');
            $totalPengeluaran = $rows->where('tipe', '2')->sum('nominal');
            $saldoAkhir = $saldoSebelumnya + $totalPendapatan - $totalPengeluaran;

            $summary = [
                'saldo_sebelumnya' => (int)$saldoSebelumnya,
                'total_pendapatan' => (int)$totalPendapatan,
                'total_pengeluaran'=> (int)$totalPengeluaran,
                'saldo_akhir'      => (int)$saldoAkhir,
            ];
        }

        return view('cek-kas', [
            'from' => $from,
            'to'   => $to,
            'rows' => $rows,
            'summary' => $summary,
        ]);
    }

    public function export(Request $request)
    {
        $from = $request->query('from');
        $to   = $request->query('to');

        abort_if(!$from || !$to, 400, 'Parameter from & to wajib diisi.');

        $fileName = 'BukuKas_'.Carbon::parse($from)->format('d-M-Y')
            .'_s.d._'.Carbon::parse($to)->format('d-M-Y').'.xlsx';

        return Excel::download(new KasRangeExport($from, $to), $fileName);
    }
}
