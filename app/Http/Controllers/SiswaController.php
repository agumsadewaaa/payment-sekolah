<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateSiswaRequest;
use App\Http\Requests\UpdateSiswaRequest;
use App\Http\Controllers\AppBaseController;
use App\Repositories\SiswaRepository;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\Kelas;
use App\Models\KasSiswa;
use Illuminate\Http\Request;
use Flash;

class SiswaController extends AppBaseController
{
    /** @var SiswaRepository $siswaRepository*/
    private $siswaRepository;

    public function __construct(SiswaRepository $siswaRepo)
    {
        $this->siswaRepository = $siswaRepo;
        // only admin and super-admin may create/update/delete siswa records
        $this->middleware('role:admin|super-admin')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    /**
     * Display a listing of the Siswa.
     */
    public function index(Request $request)
    {
        $siswas = $this->siswaRepository->all();
        
        return view('siswas.index')
            ->with('siswas', $siswas);
    }

    /**
     * Show the form for creating a new Siswa.
     */
    public function create()
    {
        return view('siswas.create');
    }

    /**
     * Store a newly created Siswa in storage.
     */
    public function store(CreateSiswaRequest $request)
    {
        $input = $request->all();

        $siswa = $this->siswaRepository->create($input);

        Flash::success('Siswa saved successfully.');

        return redirect(route('siswas.index'));
    }

    /**
     * Display the specified Siswa.
     */
    public function show($id)
    {
        $siswa = $this->siswaRepository->find($id);

        if (empty($siswa)) {
            Flash::error('Siswa not found');

            return redirect(route('siswas.index'));
        }

        return view('siswas.show')->with('siswa', $siswa);
    }

    /**
     * Show the form for editing the specified Siswa.
     */
    public function edit($id)
    {
        $siswa = $this->siswaRepository->find($id);

        if (empty($siswa)) {
            Flash::error('Siswa not found');

            return redirect(route('siswas.index'));
        }

        return view('siswas.edit')->with('siswa', $siswa);
    }

    /**
     * Update the specified Siswa in storage.
     */
    public function update($id, UpdateSiswaRequest $request)
    {
        $siswa = $this->siswaRepository->find($id);

        if (empty($siswa)) {
            Flash::error('Siswa not found');

            return redirect(route('siswas.index'));
        }

        $siswa = $this->siswaRepository->update($request->all(), $id);

        Flash::success('Siswa updated successfully.');

        return redirect(route('siswas.index'));
    }

    /**
     * Remove the specified Siswa from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $siswa = $this->siswaRepository->find($id);

        if (empty($siswa)) {
            Flash::error('Siswa not found');

            return redirect(route('siswas.index'));
        }

        $this->siswaRepository->delete($id);

        Flash::success('Siswa deleted successfully.');

        return redirect(route('siswas.index'));
    }

    public function getSiswaByKelas($kelas)
    {
        $siswas = Siswa::where('jurusan', $kelas)->pluck('nama', 'id');
        return response()->json($siswas);
    }

    public function getTagihanBySiswa($siswa_id)
    {
        try {
            $siswa = Siswa::findOrFail($siswa_id);

            // Pre-load all payments for this student (one query instead of N)
            $allTagihanIdsForSiswa = Tagihan::where('kelas', $siswa->jurusan)->pluck('id');

            $previousClasses = collect();
            if ($siswa->jurusans) {
                $previousClasses = Kelas::where('jurusan', $siswa->jurusans->jurusan)
                    ->where('kelas', '<', $siswa->jurusans->kelas)
                    ->pluck('id');
                if ($previousClasses->count() > 0) {
                    $previousTagihanIds = Tagihan::whereIn('kelas', $previousClasses)->pluck('id');
                    $allTagihanIdsForSiswa = $allTagihanIdsForSiswa->merge($previousTagihanIds)->unique();
                }
            }

            $pembayaranPerTagihan = KasSiswa::where('siswa_id', $siswa_id)
                ->whereIn('tagihan_id', $allTagihanIdsForSiswa)
                ->groupBy('tagihan_id')
                ->selectRaw('tagihan_id, SUM(nominal) as total')
                ->pluck('total', 'tagihan_id');

            // 1) Tagihan kelas saat ini yang belum lunas
            $tagihanKelasSaatIni = Tagihan::where('kelas', $siswa->jurusan)
                ->get()
                ->filter(function ($tagihan) use ($pembayaranPerTagihan) {
                    $totalBayar = (int)($pembayaranPerTagihan[$tagihan->id] ?? 0);
                    return $totalBayar < $tagihan->nominal;
                })
                ->map(function ($t) {
                    return [
                        'id' => $t->id,
                        'label' => $t->tagihan
                    ];
                });

            // 2) Tagihan kelas lama (carry-over)
            $tagihanKelasLama = collect();
            if ($previousClasses->count() > 0) {
                $tagihanKelasLama = Tagihan::whereIn('kelas', $previousClasses)
                    ->get()
                    ->filter(function ($tagihan) use ($pembayaranPerTagihan, $siswa) {
                        $totalBayar = (int)($pembayaranPerTagihan[$tagihan->id] ?? 0);
                        if ($siswa->kelas == 0) {
                            $status = $totalBayar < $tagihan->nominal;
                        } else {
                            $status = $totalBayar > 0 && $totalBayar < $tagihan->nominal;
                        }
                        return $status;
                    })
                    ->map(function ($t) {
                        return [
                            'id' => $t->id,
                            'label' => $t->tagihan . ' [TUNGGAKAN]'
                        ];
                    });
            }

            // Combine semua tagihan
            $allTagihans = $tagihanKelasSaatIni->merge($tagihanKelasLama);
            
            // Convert ke format untuk dropdown
            $result = [];
            foreach ($allTagihans as $tagihan) {
                $result[$tagihan['id']] = $tagihan['label'];
            }

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getSisaTagihan($siswa_id, $tagihan_id)
    {
        $tagihan = Tagihan::findOrFail($tagihan_id);
        
        $totalBayar = KasSiswa::where('siswa_id', $siswa_id)
            ->where('tagihan_id', $tagihan_id)
            ->sum('nominal');
        
        $sisa = $tagihan->nominal - $totalBayar;

        return response()->json([
            'sisa' => $sisa > 0 ? $sisa : 0,
            'nominal_tagihan' => $tagihan->nominal,
            'total_bayar' => $totalBayar
        ]);
    }

    public function getTagihanInfo($siswa_id, $kelas_id, $tagihan_id)
    {
        $siswa = Siswa::findOrFail($siswa_id);
        $kelas = Kelas::findOrFail($kelas_id);
        $tagihan = Tagihan::findOrFail($tagihan_id);
        
        // Format: nama siswa - kode kelas - nama tagihan
        $catatan = $siswa->nama . ' - Kelas ' . $kelas->kode . ' - ' . $tagihan->tagihan;

        return response()->json([
            'catatan' => $catatan
        ]);
    }
    
    // NEW: Get outstanding bills information for a student
    public function getOutstandingBills($siswa_id)
    {
        $siswa = Siswa::findOrFail($siswa_id);
        
        $outstandingBills = [];
        $totalOutstanding = 0;
        
        // Check for previous classes with unpaid bills
        if ((int)$siswa->jurusans?->kelas >= 11) {
            $previousClasses = Kelas::where('jurusan', $siswa->jurusans?->jurusan)
                ->where('kelas', '<', $siswa->jurusans?->kelas)
                ->pluck('id');
            
            if ($previousClasses->count() > 0) {
                $previousTagihans = Tagihan::whereIn('kelas', $previousClasses)->get();
                
                // Pre-load all payments for this student (one query instead of N)
                $previousTagihanIds = $previousTagihans->pluck('id');
                $pembayaranPerTagihan = KasSiswa::where('siswa_id', $siswa_id)
                    ->whereIn('tagihan_id', $previousTagihanIds)
                    ->groupBy('tagihan_id')
                    ->selectRaw('tagihan_id, SUM(nominal) as total')
                    ->pluck('total', 'tagihan_id');
                
                foreach ($previousTagihans as $prevTagihan) {
                    $totalBayar = (int)($pembayaranPerTagihan[$prevTagihan->id] ?? 0);
                    
                    $sisa = max(0, $prevTagihan->nominal - $totalBayar);
                    
                    if ($sisa > 0) {
                        $outstandingBills[] = [
                            'tagihan' => $prevTagihan->tagihan,
                            'nominal' => $sisa,
                            'kode_kelas' => $prevTagihan->kelass?->kode ?? 'Unknown'
                        ];
                        $totalOutstanding += $sisa;
                    }
                }
            }
        }
        
        return response()->json([
            'has_outstanding' => count($outstandingBills) > 0,
            'outstanding_bills' => $outstandingBills,
            'total_outstanding' => $totalOutstanding
        ]);
    }
}