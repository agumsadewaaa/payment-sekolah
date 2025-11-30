<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\KasSiswa;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use App\Exports\SiswaExport;
use Illuminate\Support\Facades\DB;

class CekTagihanController extends Controller
{
    public function index(Request $request)
    {
        $siswa = null;
        $tagihans = collect();

        if ($request->has('keyword')) {
            $keyword = $request->keyword;
            $siswa = Siswa::where('nama', $keyword)
                        ->orWhere('nisn', $keyword)
                        ->first();

            if ($siswa) {
                $tagihans = Tagihan::where('kelas', $siswa->jurusan)->get()->map(function ($tagihan) use ($siswa) {
                    // Ambil semua pembayaran siswa untuk tagihan ini
                    $pembayaran = KasSiswa::where('siswa_id', $siswa->id)
                        ->where('tagihan_id', $tagihan->id)
                        ->whereNull('deleted_at')
                        ->get();

                    $totalBayar = $pembayaran->sum('nominal');
                    $sisa       = $tagihan->nominal - $totalBayar;

                    return [
                        'id'           => $tagihan->id,
                        'nama_tagihan' => $tagihan->tagihan,
                        'nominal'      => $tagihan->nominal,
                        'total_bayar'  => $totalBayar,
                        'sisa'         => $sisa > 0 ? $sisa : 0,
                        'status'       => $sisa <= 0 ? 'Lunas' : 'Belum Lunas',
                        'pembayaran'   => $pembayaran, // ✅ masukkan riwayat pembayaran
                    ];
                });
            }

            // Hitung progress
            $totalTagihan = $tagihans->sum('nominal');
            $totalBayar   = $tagihans->sum('total_bayar');
            $progress     = $totalTagihan > 0 ? round(($totalBayar / $totalTagihan) * 100, 2) : 0;
        } else {
            $progress = 0;
            $totalTagihan = 0;
            $totalBayar = 0;
        }

        return view('cek-tagihan', compact('siswa', 'tagihans','progress', 'totalTagihan', 'totalBayar'));
    }
    
    // Print detail siswa (PDF)
    public function print($id)
    {
        $siswa = Siswa::findOrFail($id);

        $tagihans = Tagihan::where('kelas', $siswa->jurusan)  
            ->orderBy('tagihan')
            ->get(['id','tagihan','nominal']);

        // total bayar per tagihan
        $bayarPerTagihan = KasSiswa::where('siswa_id', $siswa->id)
            ->whereNull('deleted_at')
            ->select('tagihan_id', DB::raw('SUM(nominal) AS total'))
            ->groupBy('tagihan_id')
            ->pluck('total','tagihan_id'); // [tagihan_id => total_bayar]

        // bentuk item
        $items = $tagihans->map(function ($t) use ($bayarPerTagihan) {
            $totalBayar = (int) ($bayarPerTagihan[$t->id] ?? 0);
            $sisa = max(0, (int)$t->nominal - $totalBayar);

            return (object)[
                'id'           => $t->id,
                'nama_tagihan' => $t->tagihan,
                'nominal'      => (int)$t->nominal,
                'total_bayar'  => $totalBayar,
                'sisa'         => $sisa,
                'status'       => $sisa > 0 ? 'Belum Lunas' : 'Lunas',
            ];
        });

        // pecah status
        $belum = $items->where('sisa','>',0)->values();
        $lunas = $items->where('sisa','=',0)->values();

        // ringkasan
        $totalTagihan = (int) $items->sum('nominal');
        $totalBayar   = (int) $items->sum('total_bayar');
        $totalSisa    = max(0, $totalTagihan - $totalBayar);
        $progress     = $totalTagihan > 0 ? round(($totalBayar / $totalTagihan) * 100, 2) : 0;

        $pdf = PDF::loadView('print.tagihan', [
            'siswa'        => $siswa,
            'belum'        => $belum,
            'lunas'        => $lunas,
            'totalTagihan' => $totalTagihan,
            'totalBayar'   => $totalBayar,
            'totalSisa'    => $totalSisa,
            'progress'     => $progress,
        ])->setPaper('A4','portrait');

        return $pdf->stream("tagihan-{$siswa->nisn}.pdf");
    }

    // Export detail siswa (Excel)
    public function export($id)
    {
        $siswa = Siswa::findOrFail($id);
        return Excel::download(new SiswaExport($siswa), "tagihan-{$siswa->nisn}.xlsx");
    }
}
