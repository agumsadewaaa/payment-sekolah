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
                // 1. Ambil tagihan kelas saat ini
                $tagihanKelasSaatIni = Tagihan::where('kelas', $siswa->jurusan)->pluck('id');

                // 2. Ambil SEMUA tagihan dari kelas lama yang pernah dibayar (carry-over - termasuk yang lunas)
                $tagihanKelasLama = KasSiswa::where('siswa_id', $siswa->id)
                    ->select('tagihan_id')
                    ->groupBy('tagihan_id')
                    ->pluck('tagihan_id');

                // Gabungkan tagihan kelas saat ini + semua tagihan kelas lama
                $allTagihanIds = $tagihanKelasSaatIni->merge($tagihanKelasLama)->unique();

                $tagihans = Tagihan::whereIn('id', $allTagihanIds)->get()->map(function ($tagihan) use ($siswa) {
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
                        'pembayaran'   => $pembayaran,
                    ];
                });
            }

            // Hitung progress
            // Total tagihan = tagihan kelas saat ini (penuh) + sisa tagihan kelas lama
            $tagihanKelasSaatIni = Tagihan::where('kelas', $siswa->jurusan)->pluck('id');
            $totalTagihanKelasSaatIni = Tagihan::where('kelas', $siswa->jurusan)->sum('nominal');
            
            // Hitung sisa dari tagihan kelas lama (carry-over)
            $sisaTagihanKelasLama = $tagihans->filter(function($t) use ($tagihanKelasSaatIni) {
                return !$tagihanKelasSaatIni->contains($t['id']); // bukan tagihan kelas saat ini
            })->sum('sisa');
            
            $totalTagihan = $totalTagihanKelasSaatIni + $sisaTagihanKelasLama;
            
            // Total bayar = hanya pembayaran untuk tagihan kelas saat ini
            $totalBayar = KasSiswa::where('siswa_id', $siswa->id)
                ->whereIn('tagihan_id', $tagihanKelasSaatIni)
                ->sum('nominal');
            
            $progress = $totalTagihan > 0 ? round(($totalBayar / $totalTagihan) * 100, 2) : 0;
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

        // 1. Ambil tagihan kelas saat ini
        $tagihanKelasSaatIni = Tagihan::where('kelas', $siswa->jurusan)->pluck('id');

        // 2. Ambil SEMUA tagihan dari kelas lama yang pernah dibayar (carry-over - termasuk yang lunas)
        $tagihanKelasLama = KasSiswa::where('siswa_id', $siswa->id)
            ->select('tagihan_id')
            ->groupBy('tagihan_id')
            ->pluck('tagihan_id');

        // Gabungkan tagihan kelas saat ini + semua tagihan kelas lama
        $allTagihanIds = $tagihanKelasSaatIni->merge($tagihanKelasLama)->unique();

        $tagihans = Tagihan::whereIn('id', $allTagihanIds)
            ->orderBy('tagihan')
            ->get(['id','tagihan','nominal']);

        // total bayar per tagihan
        $bayarPerTagihan = KasSiswa::where('siswa_id', $siswa->id)
            ->whereNull('deleted_at')
            ->select('tagihan_id', DB::raw('SUM(nominal) AS total'))
            ->groupBy('tagihan_id')
            ->pluck('total','tagihan_id'); // [tagihan_id => total_bayar]

        // bentuk item
        $items = $tagihans->map(function ($t) use ($bayarPerTagihan, $siswa, $tagihanKelasSaatIni) {
            $totalBayar = (int) ($bayarPerTagihan[$t->id] ?? 0);
            $sisa = max(0, (int)$t->nominal - $totalBayar);

            // Ambil detail pembayaran
            $pembayaran = KasSiswa::where('siswa_id', $siswa->id)
                ->where('tagihan_id', $t->id)
                ->whereNull('deleted_at')
                ->orderBy('tanggal', 'asc')
                ->get();

            return (object)[
                'id'           => $t->id,
                'nama_tagihan' => $t->tagihan,
                'nominal'      => (int)$t->nominal,
                'total_bayar'  => $totalBayar,
                'sisa'         => $sisa,
                'status'       => $sisa > 0 ? 'Belum Lunas' : 'Lunas',
                'is_kelas_saat_ini' => $tagihanKelasSaatIni->contains($t->id),
                'pembayaran'   => $pembayaran,
            ];
        });

        // pecah status
        $belum = $items->where('sisa','>',0)->values();
        $lunas = $items->where('sisa','=',0)->values();

        // ringkasan - hitung total dengan benar
        $totalTagihanKelasSaatIni = $items->where('is_kelas_saat_ini', true)->sum('nominal');
        $sisaTagihanKelasLama = $items->where('is_kelas_saat_ini', false)->sum('sisa');
        
        $totalTagihan = $totalTagihanKelasSaatIni + $sisaTagihanKelasLama;
        $totalBayar = $items->where('is_kelas_saat_ini', true)->sum('total_bayar');
        $totalSisa = max(0, $totalTagihan - $totalBayar);
        $progress = $totalTagihan > 0 ? round(($totalBayar / $totalTagihan) * 100, 2) : 0;

        // Convert logo to base64 untuk print (DomPDF tidak bisa load external image)
        $logoPath = public_path('logo.jpg');
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoData = file_get_contents($logoPath);
            $logoBase64 = 'data:image/jpeg;base64,' . base64_encode($logoData);
        }

        $pdf = PDF::loadView('print.tagihan', [
            'siswa'        => $siswa,
            'belum'        => $belum,
            'lunas'        => $lunas,
            'totalTagihan' => $totalTagihan,
            'totalBayar'   => $totalBayar,
            'totalSisa'    => $totalSisa,
            'progress'     => $progress,
            'logoBase64'   => $logoBase64,
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
