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
        // only admin may create / edit / delete kas sekolah
        $this->middleware('role:admin')->only(['create', 'store', 'edit', 'update', 'destroy']);
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

            // Simpan ke kas sekolah
            $kasSekolah = $this->kasSekolahRepository->create($input);

            // Record pertama: nominal yang dibayar → selalu lunas jika pembayaran = sisa
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

        $kelas = \App\Models\Kelas::orderBy('kode', 'asc')->pluck('kode', 'id');
        return view('kas_sekolahs.edit', compact('kasSekolah', 'kelas'));
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
        
        // Bersihkan format nominal (hapus Rp, titik, koma)
        if(isset($input['nominal'])) {
            $input['nominal'] = preg_replace('/[^0-9]/', '', $input['nominal']);
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
