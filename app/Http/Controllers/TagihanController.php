<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTagihanRequest;
use App\Http\Requests\UpdateTagihanRequest;
use App\Http\Controllers\AppBaseController;
use App\Repositories\TagihanRepository;
use Illuminate\Http\Request;
use Flash;

class TagihanController extends AppBaseController
{
    /** @var TagihanRepository $tagihanRepository*/
    private $tagihanRepository;

    public function __construct(TagihanRepository $tagihanRepo)
    {
        $this->tagihanRepository = $tagihanRepo;
        // only admin can manage tagihan (create / update / destroy)
        $this->middleware('role:admin')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    /**
     * Display a listing of the Tagihan.
     */
    public function index(Request $request)
    {
        $tagihans = $this->tagihanRepository->paginate(10);

        return view('tagihans.index')
            ->with('tagihans', $tagihans);
    }

    /**
     * Show the form for creating a new Tagihan.
     */
    public function create()
    {
        $kelas = \App\Models\Kelas::orderBy('kode', 'asc')->pluck('kode', 'id');
        return view('tagihans.create', compact('kelas'));
    }

    /**
     * Store a newly created Tagihan in storage.
     */
    public function store(CreateTagihanRequest $request)
    {
        $input = $request->all();

        $tagihan = $this->tagihanRepository->create($input);

        Flash::success('Tagihan saved successfully.');

        return redirect(route('tagihans.index'));
    }

    /**
     * Display the specified Tagihan.
     */
    public function show($id)
    {
        $tagihan = $this->tagihanRepository->find($id);

        if (empty($tagihan)) {
            Flash::error('Tagihan not found');

            return redirect(route('tagihans.index'));
        }

        return view('tagihans.show')->with('tagihan', $tagihan);
    }

    /**
     * Show the form for editing the specified Tagihan.
     */
    public function edit($id)
    {
        $tagihan = $this->tagihanRepository->find($id);

        if (empty($tagihan)) {
            Flash::error('Tagihan not found');

            return redirect(route('tagihans.index'));
        }

        return view('tagihans.edit')->with('tagihan', $tagihan);
    }

    /**
     * Update the specified Tagihan in storage.
     */
    public function update($id, UpdateTagihanRequest $request)
    {
        $tagihan = $this->tagihanRepository->find($id);

        if (empty($tagihan)) {
            Flash::error('Tagihan not found');

            return redirect(route('tagihans.index'));
        }

        $tagihan = $this->tagihanRepository->update($request->all(), $id);

        Flash::success('Tagihan updated successfully.');

        return redirect(route('tagihans.index'));
    }

    /**
     * Remove the specified Tagihan from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $tagihan = $this->tagihanRepository->find($id);

        if (empty($tagihan)) {
            Flash::error('Tagihan not found');

            return redirect(route('tagihans.index'));
        }

        $this->tagihanRepository->delete($id);

        Flash::success('Tagihan deleted successfully.');

        return redirect(route('tagihans.index'));
    }
}
