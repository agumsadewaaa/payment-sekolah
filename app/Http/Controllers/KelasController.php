<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateKelasRequest;
use App\Http\Requests\UpdateKelasRequest;
use App\Http\Controllers\AppBaseController;
use App\Repositories\KelasRepository;
use Illuminate\Http\Request;
use Flash;

class KelasController extends AppBaseController
{
    /** @var KelasRepository $kelasRepository*/
    private $kelasRepository;

    public function __construct(KelasRepository $kelasRepo)
    {
        $this->kelasRepository = $kelasRepo;
        // only admin can create/update/delete kelas
        $this->middleware('role:admin')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    /**
     * Display a listing of the Kelas.
     */
    public function index(Request $request)
    {
        $kelass = $this->kelasRepository->getAllOrderedByKode();

        return view('kelas.index')
            ->with('kelass', $kelass);
    }

    /**
     * Show the form for creating a new Kelas.
     */
    public function create()
    {
        return view('kelas.create');
    }

    /**
     * Store a newly created Kelas in storage.
     */
    public function store(CreateKelasRequest $request)
    {
        $input = $request->all();

        $kelas = $this->kelasRepository->create($input);

        Flash::success('Kelas saved successfully.');

        return redirect(route('kelas.index'));
    }

    /**
     * Display the specified Kelas.
     */
    public function show($id)
    {
        $kelas = $this->kelasRepository->find($id);

        if (empty($kelas)) {
            Flash::error('Kelas not found');

            return redirect(route('kelas.index'));
        }

        return view('kelas.show')->with('kelas', $kelas);
    }

    /**
     * Show the form for editing the specified Kelas.
     */
    public function edit($id)
    {
        $kelas = $this->kelasRepository->find($id);

        if (empty($kelas)) {
            Flash::error('Kelas not found');

            return redirect(route('kelas.index'));
        }

        return view('kelas.edit')->with('kelas', $kelas);
    }

    /**
     * Update the specified Kelas in storage.
     */
    public function update($id, UpdateKelasRequest $request)
    {
        $kelas = $this->kelasRepository->find($id);

        if (empty($kelas)) {
            Flash::error('Kelas not found');

            return redirect(route('kelas.index'));
        }

        $kelas = $this->kelasRepository->update($request->all(), $id);

        Flash::success('Kelas updated successfully.');

        return redirect(route('kelas.index'));
    }

    /**
     * Remove the specified Kelas from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $kelas = $this->kelasRepository->find($id);

        if (empty($kelas)) {
            Flash::error('Kelas not found');

            return redirect(route('kelas.index'));
        }

        $this->kelasRepository->delete($id);

        Flash::success('Kelas deleted successfully.');

        return redirect(route('kelas.index'));
    }

    public function getJurusan($kelas)
    {
        $jurusan = $this->kelasRepository->getJurusanByKelas($kelas);
        return response()->json($jurusan);
    }

}
