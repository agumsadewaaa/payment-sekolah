<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateSiswaRequest;
use App\Http\Requests\UpdateSiswaRequest;
use App\Http\Controllers\AppBaseController;
use App\Repositories\SiswaRepository;
use App\Models\Siswa;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Flash;

class SiswaController extends AppBaseController
{
    /** @var SiswaRepository $siswaRepository*/
    private $siswaRepository;

    public function __construct(SiswaRepository $siswaRepo)
    {
        $this->siswaRepository = $siswaRepo;
        // only admin may create/update/delete siswa records
        $this->middleware('role:admin')->only(['create', 'store', 'edit', 'update', 'destroy']);
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
        $siswa = Siswa::findOrFail($siswa_id);

        // Ambil tagihan yang belum lunas untuk siswa ini
        // Filter berdasarkan kelas (jurusan) siswa saat ini
        $tagihanBelumLunas = Tagihan::where('kelas', $siswa->jurusan)
            ->get()
            ->filter(function ($tagihan) use ($siswa_id) {
                // Hitung total pembayaran untuk tagihan ini
                $totalBayar = $tagihan->kasSiswa()
                    ->where('siswa_id', $siswa_id)
                    ->sum('nominal');

                // Tampilkan jika belum lunas
                return $totalBayar < $tagihan->nominal;
            })
            ->pluck('tagihan', 'id');

        return response()->json($tagihanBelumLunas);
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
}
