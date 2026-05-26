<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateKasSekolahRequest;
use App\Http\Requests\UpdateKasSekolahRequest;
use App\Http\Controllers\AppBaseController;
use App\Repositories\KasSekolahRepository;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Tagihan;
use App\Models\KasSiswa;
use Illuminate\Http\Request;
use Flash;

class KasSekolahController extends AppBaseController
{
    /** @var KasSekolahRepository $kasSekolahRepository*/
    private $kasSekolahRepository;

    public function __construct(KasSekolahRepository $kasSekolahRepo)
    {
        $this->kasSekolahRepository = $kasSekolahRepo;
        // only admin and super-admin may create / edit / delete kas sekolah
        $this->middleware('role:admin|super-admin')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    /**
     * Display a listing of the KasSekolah.
     */
    public function index(Request $request)
    {
        $kasSekolahs = $this->kasSekolahRepository->paginate(10);

        return view('kas_sekolahs.index')
            ->with('kasSekolahs', $kasSekolahs);
    }

    /**
     * Show the form for creating a new KasSekolah.
     */
    public function create()
    {
        $semuaKelas = \App\Models\Kelas::orderBy('kode', 'asc')->get();
        $kelas = $semuaKelas->pluck('kode', 'id');

        // Tambah opsi "Lulus" untuk setiap jurusan (pakai ID kelas 12)
        $kelasLulus = $semuaKelas->where('kelas', '12');
        foreach ($kelasLulus as $k) {
            $kelas->put($k->id, 'Lulus - ' . $k->jurusan);
        }

        return view('kas_sekolahs.create', compact('kelas'));
    }

    /**
     * Store a newly created KasSekolah in storage.
     */
    public function store(CreateKasSekolahRequest $request)
    {
        $input = $request->all();
        
        // Gabungkan tanggal dengan waktu sekarang menggunakan timezone yang sesuai
        if(isset($input['tanggal'])) {
            $timezone = config('app.timezone', 'Asia/Jakarta');
            $currentTime = \Carbon\Carbon::now($timezone)->format('H:i:s');
            $input['tanggal'] = $input['tanggal'] . ' ' . $currentTime;
        }
        
        // Bersihkan format nominal (hapus Rp, titik, koma)
        if(isset($input['nominal'])) {
            $input['nominal'] = preg_replace('/[^0-9]/', '', $input['nominal']);
        }

        if($input['tipe'] == '1') {
            // Ambil kelas, siswa, dan tagihan
            $kelas = Kelas::find($request->kelas);
            $siswa = Siswa::find($request->siswa_id);
            $tagihan = Tagihan::find($request->tagihan_id);

            // Validasi: cek sisa tagihan
            $totalBayar = KasSiswa::where('siswa_id', $siswa->id)
                ->where('tagihan_id', $tagihan->id)
                ->sum('nominal');
            
            $sisaTagihan = $tagihan->nominal - $totalBayar;

            // Cek apakah pembayaran melebihi sisa
            if ($input['nominal'] > $sisaTagihan) {
                Flash::error('Pembayaran melebihi sisa tagihan! Sisa tagihan: Rp ' . number_format($sisaTagihan, 0, ',', '.'));
                
                // Kembalikan dengan data kelas untuk dropdown
                $kelasOptions = \App\Models\Kelas::orderBy('kode', 'asc')->pluck('kode', 'id');
                return redirect()->back()->withInput()->with('kelas', $kelasOptions);
            }

            $kodeKelas = $kelas->kode ?? '';
            $namaSiswa = $siswa->nama ?? '';
            $namaTagihan = $tagihan->tagihan ?? '';

            // Buat catatan gabungan
            if($kelas && $namaSiswa && $namaTagihan) {
                $input['catatan'] = $namaSiswa . ' - Kelas ' . $kodeKelas . ' - ' . $namaTagihan;
            }

            // ===== VALIDATION: Check for unpaid bills from previous classes =====
            $hasOutstandingFromPrevious = false;
            $totalOutstandingFromPrevious = 0;
            $previousClasses = collect();
            $previousTagihans = collect();

            if ($kelas && (int)$kelas->kelas >= 11) {
                $previousClasses = Kelas::where('jurusan', $kelas->jurusan)
                    ->where('kelas', '<', $kelas->kelas)
                    ->pluck('id');
                
                if ($previousClasses->count() > 0) {
                    $previousTagihans = Tagihan::whereIn('kelas', $previousClasses)->get();
                    $previousTagihanIds = $previousTagihans->pluck('id');
                    
                    // Pre-load all payments for this student (one aggregate query)
                    $previousPembayaran = KasSiswa::where('siswa_id', $siswa->id)
                        ->whereIn('tagihan_id', $previousTagihanIds)
                        ->groupBy('tagihan_id')
                        ->selectRaw('tagihan_id, SUM(nominal) as total')
                        ->pluck('total', 'tagihan_id');
                    
                    foreach ($previousTagihans as $prevTagihan) {
                        $totalBayarPrev = (int)($previousPembayaran[$prevTagihan->id] ?? 0);
                        
                        $sisaPrevTagihan = $prevTagihan->nominal - $totalBayarPrev;
                        if ($sisaPrevTagihan > 0) {
                            $hasOutstandingFromPrevious = true;
                            $totalOutstandingFromPrevious += $sisaPrevTagihan;
                        }
                    }
                }
            }
            
            // ===== AUTO-PAY OUTSTANDING BILLS FROM PREVIOUS CLASSES =====
            $nominalForCurrentClass = $input['nominal'];
            $hadAutoPayment = false;
            
            if ($hasOutstandingFromPrevious) {
                $previousPembayaran = KasSiswa::where('siswa_id', $siswa->id)
                    ->whereIn('tagihan_id', $previousTagihans->pluck('id'))
                    ->groupBy('tagihan_id')
                    ->selectRaw('tagihan_id, SUM(nominal) as total')
                    ->pluck('total', 'tagihan_id');
                
                $sisaForPayment = $input['nominal'];
                
                foreach ($previousTagihans as $prevTagihan) {
                    $totalBayarPrev = (int)($previousPembayaran[$prevTagihan->id] ?? 0);
                    
                    $sisaPrevTagihan = $prevTagihan->nominal - $totalBayarPrev;
                    
                    if ($sisaPrevTagihan > 0 && $sisaForPayment > 0) {
                            // Tentukan nominal yang digunakan untuk bayar tunggakan ini
                            $nominalTunggakan = min($sisaPrevTagihan, $sisaForPayment);
                            
                            // Buat KasSekolah record untuk bayar tunggakan
                            $inputTunggakan = [
                                'tanggal' => $input['tanggal'],
                                'catatan' => $namaSiswa . ' - Bayar Tunggakan Kelas Lama (' . ($prevTagihan->kelass ? $prevTagihan->kelass->kode : 'Unknown') . ') - ' . $prevTagihan->tagihan,
                                'tipe' => 1, // income
                                'metode_pembayaran' => $input['metode_pembayaran'],
                                'nominal' => (int)$nominalTunggakan
                            ];
                            
                            $kasSekolahTunggakan = $this->kasSekolahRepository->create($inputTunggakan);
                            
                            // Buat KasSiswa record untuk tunggakan
                            $statusTunggakan = $nominalTunggakan >= $sisaPrevTagihan ? 'lunas' : 'belum_lunas';
                            
                            KasSiswa::create([
                                'kas_sekolah_id' => $kasSekolahTunggakan->id,
                                'siswa_id' => $siswa->id,
                                'tagihan_id' => $prevTagihan->id,
                                'tanggal' => $input['tanggal'],
                                'metode_pembayaran' => $input['metode_pembayaran'],
                                'nominal' => (int)$nominalTunggakan,
                                'status' => $statusTunggakan
                            ]);
                            
                            // Kurangi sisa nominal yang tersedia untuk kelas saat ini
                            $sisaForPayment -= $nominalTunggakan;
                            $hadAutoPayment = true;
                        }
                    }
                    
                    // Nominal yang tersisa untuk kelas saat ini
                    $nominalForCurrentClass = max(0, $sisaForPayment);
                }
                // ===== END AUTO-PAY =====

            // Update nominal untuk kelas saat ini dengan sisa yang tersedia setelah auto-pay
            $input['nominal'] = (int)$nominalForCurrentClass;

            // Jika masih ada nominal untuk kelas saat ini, simpan
            if ($nominalForCurrentClass > 0) {
                // Simpan ke kas sekolah untuk tagihan kelas saat ini
                $kasSekolah = $this->kasSekolahRepository->create($input);

                // Record untuk tagihan kelas saat ini
                $status = ($totalBayar + $input['nominal']) >= $tagihan->nominal ? 'lunas' : 'belum_lunas';

                KasSiswa::create([
                    'kas_sekolah_id' => $kasSekolah->id,
                    'siswa_id' => $siswa->id,
                    'tagihan_id' => $tagihan->id,
                    'tanggal' => $input['tanggal'],
                    'metode_pembayaran' => $input['metode_pembayaran'],
                    'nominal' => $input['nominal'],
                    'status' => $status
                ]);
            }
            // Jika tidak ada nominal untuk kelas saat ini, hanya auto-pay yang disimpan (tidak ada record untuk current class)
        } else {
            // Simpan ke kas sekolah
            $kasSekolah = $this->kasSekolahRepository->create($input);
        }

        // Generate success message based on whether auto-payment happened
        $successMessage = 'Kas Sekolah saved successfully.';
        if ($input['tipe'] == '1') {
            if (isset($hadAutoPayment) && $hadAutoPayment) {
                if ($nominalForCurrentClass <= 0) {
                    // Pembayaran hanya cukup untuk tunggakan saja
                    $totalOutstandingFormatted = number_format($totalOutstandingFromPrevious, 0, ',', '.');
                    $successMessage = "✓ Pembayaran diterima! Anda hanya melunasi pembayaran tagihan kelas sebelumnya sebesar Rp $totalOutstandingFormatted. Sisa pembayaran untuk tagihan kelas saat ini masih diperlukan.";
                } else {
                    // Auto-payment terjadi dan ada sisa untuk current class
                    $successMessage = "✓ Pembayaran berhasil! Tunggakan kelas lama telah dibayarkan dan sisa nominal dialokasikan untuk tagihan kelas saat ini.";
                }
            }
        }

        Flash::success($successMessage);

        return redirect(route('kas-sekolahs.index'));
    }

    /**
     * Display the specified KasSekolah.
     */
    public function show($id)
    {
        $kasSekolah = $this->kasSekolahRepository->find($id);

        if (empty($kasSekolah)) {
            Flash::error('Kas Sekolah not found');

            return redirect(route('kas-sekolahs.index'));
        }

        return view('kas_sekolahs.show')->with('kasSekolah', $kasSekolah);
    }

    /**
     * Show the form for editing the specified KasSekolah.
     */
    public function edit($id)
    {
        $kasSekolah = $this->kasSekolahRepository->find($id);

        if (empty($kasSekolah)) {
            Flash::error('Kas Sekolah not found');

            return redirect(route('kas-sekolahs.index'));
        }

        $semuaKelas = \App\Models\Kelas::orderBy('kode', 'asc')->get();
        $kelas = $semuaKelas->pluck('kode', 'id');

        // Tambah opsi "Lulus" untuk setiap jurusan (pakai ID kelas 12)
        $kelasLulus = $semuaKelas->where('kelas', '12');
        foreach ($kelasLulus as $k) {
            $kelas->put($k->id, 'Lulus - ' . $k->jurusan);
        }

        // Jika tipe pendapatan, ambil data siswa dan tagihan dari KasSiswa
        $kasSiswa = null;
        $siswaOptions = [];
        $tagihanOptions = [];

        if ($kasSekolah->tipe == 1) {
            $kasSiswa = \App\Models\KasSiswa::where('kas_sekolah_id', $kasSekolah->id)->first();
            if ($kasSiswa) {
                $siswa = \App\Models\Siswa::find($kasSiswa->siswa_id);
                if ($siswa) {
                    $siswaOptions = \App\Models\Siswa::where('jurusan', $siswa->jurusan)
                        ->orderBy('nama', 'asc')
                        ->pluck('nama', 'id');

                    // Ambil tagihan untuk kelas siswa ini (hanya yang belum lunas)
                    $tagihanList = \App\Models\Tagihan::where('kelas', $siswa->jurusan)
                        ->orderBy('tagihan', 'asc')
                        ->get()
                        ->filter(function ($tagihan) use ($kasSiswa) {
                            // Hitung total pembayaran untuk tagihan ini
                            $totalBayar = \App\Models\KasSiswa::where('siswa_id', $kasSiswa->siswa_id)
                                ->where('tagihan_id', $tagihan->id)
                                ->sum('nominal');

                            // Tampilkan jika belum lunas atau jika ini adalah tagihan saat ini
                            return ($totalBayar < $tagihan->nominal) || ($tagihan->id == $kasSiswa->tagihan_id);
                        })
                        ->pluck('tagihan', 'id');

                    $tagihanOptions = $tagihanList;
                }
            }
        }

        // Format tanggal untuk form (hanya tanggal, tanpa waktu)
        $kasSekolah->tanggal = $kasSekolah->tanggal->format('Y-m-d');

        return view('kas_sekolahs.edit', compact('kasSekolah', 'kelas', 'kasSiswa', 'siswaOptions', 'tagihanOptions'));
    }

    /**
     * Update the specified KasSekolah in storage.
     */
    public function update($id, UpdateKasSekolahRequest $request)
    {
        $kasSekolah = $this->kasSekolahRepository->find($id);

        if (empty($kasSekolah)) {
            Flash::error('Kas Sekolah not found');

            return redirect(route('kas-sekolahs.index'));
        }
        
        $input = $request->all();
        
        // Gabungkan tanggal dengan waktu sekarang
        if(isset($input['tanggal'])) {
            $input['tanggal'] = $input['tanggal'] . ' ' . now()->format('H:i:s');
        }
        
        // Bersihkan format nominal (hapus Rp, titik, koma)
        if(isset($input['nominal'])) {
            $input['nominal'] = preg_replace('/[^0-9]/', '', $input['nominal']);
        }

        // Jika tipe pendapatan, update juga KasSiswa
        if($input['tipe'] == '1') {
            $kasSiswa = \App\Models\KasSiswa::where('kas_sekolah_id', $kasSekolah->id)->first();
            if($kasSiswa) {
                // Hitung ulang total bayar setelah update
                $oldNominal = $kasSekolah->nominal;
                $newNominal = $input['nominal'];
                
                // Update total bayar (kurangi old, tambah new)
                $totalBayar = \App\Models\KasSiswa::where('siswa_id', $kasSiswa->siswa_id)
                    ->where('tagihan_id', $kasSiswa->tagihan_id)
                    ->sum('nominal') - $oldNominal + $newNominal;
                
                // Update status
                $tagihan = \App\Models\Tagihan::find($kasSiswa->tagihan_id);
                $status = ($totalBayar >= $tagihan->nominal) ? 'lunas' : 'belum_lunas';
                
                $kasSiswa->update([
                    'tanggal' => $input['tanggal'],
                    'metode_pembayaran' => $input['metode_pembayaran'],
                    'nominal' => $newNominal,
                    'status' => $status
                ]);
            }
        }

        $kasSekolah = $this->kasSekolahRepository->update($input, $id);

        Flash::success('Kas Sekolah updated successfully.');

        return redirect(route('kas-sekolahs.index'));
    }

    /**
     * Remove the specified KasSekolah from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $kasSekolah = $this->kasSekolahRepository->find($id);

        if (empty($kasSekolah)) {
            Flash::error('Kas Sekolah not found');

            return redirect(route('kas-sekolahs.index'));
        }

        $this->kasSekolahRepository->delete($id);

        Flash::success('Kas Sekolah deleted successfully.');

        return redirect(route('kas-sekolahs.index'));
    }
}
