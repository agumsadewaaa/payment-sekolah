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
        $kelas = \App\Models\Kelas::orderBy('kode', 'asc')->pluck('kode', 'id');
        return view('kas_sekolahs.create', compact('kelas'));
    }

    /**
     * Store a newly created KasSekolah in storage.
     */
    public function store(CreateKasSekolahRequest $request)
    {
        $input = $request->all();

        if($input['tipe'] == '1') {
            // Ambil kelas, siswa, dan tagihan
            $kelas = Kelas::find($request->kelas);
            $siswa = Siswa::find($request->siswa_id);
            $tagihan = Tagihan::find($request->tagihan_id);

            $kodeKelas = $kelas->kode ?? '';
            $namaSiswa = $siswa->nama ?? '';
            $namaTagihan = $tagihan->tagihan ?? '';
            $nominalTagihan = $tagihan->nominal ?? 0;

            // Buat catatan gabungan
            if($kelas && $namaSiswa && $namaTagihan) {
                $input['catatan'] = $namaSiswa . ' - Kelas ' . $kodeKelas . ' - ' . $namaTagihan;
            }

            // Simpan ke kas sekolah
            $kasSekolah = $this->kasSekolahRepository->create($input);

            // Record pertama: nominal yang dibayar → selalu lunas
            KasSiswa::create([
                'kas_sekolah_id' => $kasSekolah->id,
                'siswa_id' => $siswa->id,
                'tagihan_id' => $tagihan->id,
                'tanggal' => $input['tanggal'],
                'metode_pembayaran' => $input['metode_pembayaran'],
                'nominal' => $input['nominal'],
                'status' => 'lunas'
            ]);
        } else {
            // Simpan ke kas sekolah
            $kasSekolah = $this->kasSekolahRepository->create($input);
        }

        Flash::success('Kas Sekolah saved successfully.');

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

        return view('kas_sekolahs.edit')->with('kasSekolah', $kasSekolah);
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

        $kasSekolah = $this->kasSekolahRepository->update($request->all(), $id);

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
