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
        $siswa    = null;
        $tagihans = collect();
        $error    = null;

        $progress     = 0;
        $totalTagihan = 0;
        $totalBayar   = 0;

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;

            $siswa = Siswa::where('nama', $keyword)
                ->orWhere('nis', $keyword)
                ->first();

            if (!$siswa) {
                $error = 'Data siswa dengan keyword "' . $keyword . '" tidak ditemukan.';
                return view('cek-tagihan', compact('siswa', 'tagihans', 'progress', 'totalTagihan', 'totalBayar', 'error'));
            }

            // 1) Ambil semua pembayaran siswa SEKALI (anti N+1)
            $pembayaranSiswa = KasSiswa::where('siswa_id', $siswa->id)
                ->whereNull('deleted_at')
                ->get();

            // Group pembayaran per tagihan_id
            $pembayaranByTagihan = $pembayaranSiswa->groupBy('tagihan_id');

            // 2) Tagihan kelas saat ini (IDs)
            $tagihanKelasSaatIniIds = Tagihan::where('kelas', $siswa->jurusan)->pluck('id');

            // 3) Tagihan kelas lama = tagihan yang pernah dibayar (ambil dari keys groupBy)
            $tagihanKelasLamaIds = $pembayaranByTagihan->keys(); // collection of tagihan_id

            // 4) Gabungkan semua tagihan yang mau ditampilkan
            $allTagihanIds = $tagihanKelasSaatIniIds->merge($tagihanKelasLamaIds)->unique()->values();

            // 5) Ambil semua tagihan SEKALI
            $tagihanModels = Tagihan::whereIn('id', $allTagihanIds)->get();

            // 6) Bentuk data tagihan (tanpa query di dalam map)
            $tagihans = $tagihanModels->map(function ($tagihan) use ($pembayaranByTagihan) {
                $pembayaran = $pembayaranByTagihan->get($tagihan->id, collect());

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

            // 7) Hitung progress (tanpa query tambahan)
            $totalTagihanKelasSaatIni = $tagihanModels
                ->whereIn('id', $tagihanKelasSaatIniIds)
                ->sum('nominal');

            $sisaTagihanKelasLama = $tagihans
                ->filter(fn ($t) => !$tagihanKelasSaatIniIds->contains($t['id']))
                ->sum('sisa');

            $totalTagihan = $totalTagihanKelasSaatIni + $sisaTagihanKelasLama;

            // Total bayar hanya untuk tagihan kelas saat ini
            $totalBayar = $pembayaranSiswa
                ->whereIn('tagihan_id', $tagihanKelasSaatIniIds->all())
                ->sum('nominal');

            $progress = $totalTagihan > 0 ? round(($totalBayar / $totalTagihan) * 100, 2) : 0;
        }

        return view('cek-tagihan', compact('siswa', 'tagihans', 'progress', 'totalTagihan', 'totalBayar', 'error'));
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
        
        // Coba berbagai lokasi logo
        if (file_exists($logoPath)) {
            $logoData = file_get_contents($logoPath);
            $logoBase64 = 'data:image/jpeg;base64,' . base64_encode($logoData);
        } elseif (file_exists(public_path('images/logo.jpg'))) {
            $logoPath = public_path('images/logo.jpg');
            $logoData = file_get_contents($logoPath);
            $logoBase64 = 'data:image/jpeg;base64,' . base64_encode($logoData);
        } elseif (file_exists(public_path('logo.png'))) {
            $logoPath = public_path('logo.png');
            $logoData = file_get_contents($logoPath);
            $logoBase64 = 'data:image/png;base64,' . base64_encode($logoData);
        } elseif (file_exists(public_path('images/logo.png'))) {
            $logoPath = public_path('images/logo.png');
            $logoData = file_get_contents($logoPath);
            $logoBase64 = 'data:image/png;base64,' . base64_encode($logoData);
        }

        // Convert e-signature to base64
        $signatureBase64 = '';
        if (file_exists(public_path('e-signature.jpg'))) {
            $sigData = file_get_contents(public_path('e-signature.jpg'));
            $signatureBase64 = 'data:image/jpeg;base64,' . base64_encode($sigData);
        } elseif (file_exists(public_path('e-signature.jpeg'))) {
            $sigData = file_get_contents(public_path('e-signature.jpeg'));
            $signatureBase64 = 'data:image/jpeg;base64,' . base64_encode($sigData);
        } elseif (file_exists(public_path('e-signature.png'))) {
            $sigData = file_get_contents(public_path('e-signature.png'));
            $signatureBase64 = 'data:image/png;base64,' . base64_encode($sigData);
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
            'signatureBase64' => $signatureBase64,
        ])->setPaper('A4','portrait');

        return $pdf->stream("tagihan-{$siswa->nis}.pdf");
    }

    // Export detail siswa (Excel)
    public function export($id)
    {
        $siswa = Siswa::findOrFail($id);
        return Excel::download(new SiswaExport($siswa), "tagihan-{$siswa->nis}.xlsx");
    }
}
