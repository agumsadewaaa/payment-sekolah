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
        // only admin and super-admin can create/update/delete kelas
        $this->middleware('role:admin|super-admin')->only(['create', 'store', 'edit', 'update', 'destroy', 'bulkDestroy']);
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

        // Auto-create kelas=0 (Lulus) untuk jurusan ini hanya ketika kelas 12 dibuat
        if ($kelas && $kelas->kelas == '12') {
            $existingLulus = \App\Models\Kelas::where('kelas', '0')
                ->where('jurusan', $kelas->jurusan)
                ->first();
            if (!$existingLulus) {
                $kodeParts = explode('-', $kelas->kode);
                $kodeJurusan = count($kodeParts) > 1 ? $kodeParts[1] : strtoupper(substr($kelas->jurusan, 0, 3));
                \App\Models\Kelas::create([
                    'kode' => 'LULUS-' . $kodeJurusan,
                    'kelas' => '0',
                    'jurusan' => $kelas->jurusan,
                ]);
            }
        }

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

        if ($kelas->kelas == '0') {
            Flash::error('Kelas tidak tersedia');

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

        if ($kelas->kelas == '0') {
            Flash::error('Kelas tidak tersedia');

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

        if ($kelas->kelas == '0') {
            Flash::error('Kelas tidak tersedia');

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

        if ($kelas->kelas == '0') {
            Flash::error('Kelas tidak tersedia');

            return redirect(route('kelas.index'));
        }

        $this->kelasRepository->delete($id);

        Flash::success('Kelas deleted successfully.');

        return redirect(route('kelas.index'));
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            Flash::error('Tidak ada data yang dipilih.');
            return redirect()->back();
        }

        $count = 0;
        foreach ($ids as $id) {
            $kelas = $this->kelasRepository->find($id);
            if ($kelas && $kelas->kelas !== '0') {
                $this->kelasRepository->delete($id);
                $count++;
            }
        }

        Flash::success($count . ' data kelas berhasil dihapus.');

        return redirect()->back();
    }

    public function getJurusan($kelas)
    {
        $jurusan = $this->kelasRepository->getJurusanByKelas($kelas);
        return response()->json($jurusan);
    }

}
