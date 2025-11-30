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

        // Ambil semua tagihan kelas siswa
        $tagihanBelumLunas = Tagihan::where('kelas', $siswa->jurusan)
            ->whereHas('kasSiswa', function ($q) use ($siswa_id) {
                $q->where('siswa_id', $siswa_id);
            }, '>=', 0) // hanya untuk pastikan ada relasi
            ->get()
            ->filter(function ($tagihan) use ($siswa_id) {
                // Hitung total pembayaran di kas siswa
                $totalBayar = $tagihan->kasSiswa()
                    ->where('siswa_id', $siswa_id)
                    ->sum('nominal');

                // Tagihan tetap muncul kalau total bayar < nominal tagihan
                return $totalBayar < $tagihan->nominal;
            })
            ->pluck('tagihan', 'id');

        return response()->json($tagihanBelumLunas);
    }
}
