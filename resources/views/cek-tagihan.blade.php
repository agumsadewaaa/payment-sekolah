@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1"><i class="fas fa-search-dollar me-2 text-primary"></i>Cek Tagihan Siswa</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="fas fa-home"></i> Home</a></li>
                    <li class="breadcrumb-item active">Cek Tagihan</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- Form Pencarian --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0"><i class="fas fa-search me-2 text-primary"></i>Pencarian Siswa</h5>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('cek-tagihan') }}" method="GET" class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Nama atau NIS</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" name="keyword" class="form-control" placeholder="Masukkan Nama atau NIS" value="{{ request('keyword') }}" required>
                    </div>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i>Cari
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Jika ada hasil --}}
    @if($siswa)
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0"><i class="fas fa-user-circle me-2 text-primary"></i>Detail Siswa</h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-user text-primary me-3" style="font-size: 20px;"></i>
                            <div>
                                <small class="text-muted d-block">Nama Siswa</small>
                                <strong>{{ $siswa->nama }}</strong>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-id-card text-primary me-3" style="font-size: 20px;"></i>
                            <div>
                                <small class="text-muted d-block">NIS</small>
                                <strong style="font-family: monospace;">{{ $siswa->nis }}</strong>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-school text-primary me-3" style="font-size: 20px;"></i>
                            <div>
                                <small class="text-muted d-block">Kelas</small>
                                <strong>{{ $siswa->kelas == 0 ? 'Lulus' : $siswa->kelas }}</strong>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-book text-primary me-3" style="font-size: 20px;"></i>
                            <div>
                                <small class="text-muted d-block">Jurusan</small>
                                <strong>{{ $siswa->jurusans ? $siswa->jurusans->jurusan : '-' }}</strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light border-0 h-100">
                            <div class="card-body">
                                <h6 class="text-muted mb-3">Progress Pembayaran</h6>
                                @php
                                    $isTunggakan = is_string($progress) && strpos((string)$progress, '-') === 0;
                                    $progressValue = $isTunggakan ? ltrim($progress, '-') : $progress;
                                @endphp
                                @if($isTunggakan)
                                    <div class="alert alert-danger mb-0" role="alert">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <strong>Tunggakan {{ str_replace('.', ',', $progressValue) }}%</strong>
                                        <p class="mb-0 mt-2">Anda memiliki tunggakan tagihan dari kelas sebelumnya sebesar {{ str_replace('.', ',', $progressValue) }}% yang belum lunas. Harap lunasi semua tagihan sebelum melanjutkan pembayaran kelas baru.</p>
                                    </div>
                                @else
                                    <div class="progress mb-2" style="height: 30px;">
                                        <div class="progress-bar {{ $progress >= 75 ? 'bg-success' : ($progress >= 50 ? 'bg-warning' : 'bg-danger') }}" 
                                             role="progressbar" 
                                             style="width: {{ $progress }}%" 
                                             aria-valuenow="{{ $progress }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                            <strong>{{ round($progress) }}%</strong>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-success"><strong>Terbayar: Rp {{ number_format($totalBayar, 0, ',', '.') }}</strong></span>
                                        <span class="text-danger"><strong>Total: Rp {{ number_format($totalTagihan, 0, ',', '.') }}</strong></span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-file-invoice-dollar me-2 text-primary"></i>Daftar Tagihan</h5>
                <div>
                    <a href="{{ route('tagihan.print', $siswa->id) }}" class="btn btn-sm btn-secondary" target="_blank">
                        <i class="fas fa-print me-1"></i>Print
                    </a>
                    <a href="{{ route('tagihan.export', $siswa->id) }}" class="btn btn-sm btn-success">
                        <i class="fas fa-file-excel me-1"></i>Export
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                    <thead class="table-primary">
                        <tr>
                            <th></th>
                            <th>Nama Tagihan</th>
                            <th>Nominal</th>
                            <th>Total Bayar</th>
                            <th>Sisa</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tagihans as $tagihan)
                            {{-- Row utama --}}
                            <tr>
                                <td style="width:50px;">
                                    <button class="btn btn-sm btn-outline-primary toggle-btn"
                                            type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#detail{{ $loop->index }}"
                                            aria-expanded="false"
                                            aria-controls="detail{{ $loop->index }}">
                                        +
                                    </button>
                                </td>
                                <td>{{ $tagihan['nama_tagihan'] }}</td>
                                <td>Rp {{ number_format($tagihan['nominal'], 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($tagihan['total_bayar'], 0, ',', '.') }}</td>
                                <td>
                                    @if($tagihan['sisa'] > 0)
                                        Rp {{ number_format($tagihan['sisa'], 0, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($tagihan['status'] === 'Lunas')
                                        <span class="badge bg-success">Lunas</span>
                                    @else
                                        <span class="badge bg-danger">Belum Lunas</span>
                                        {{-- Tombol kirim WA --}}
                                        @php
                                            $nohp = $siswa->kontak_ortu;
                                            if (substr($nohp, 0, 1) === '0') {
                                                $nohp = '62' . substr($nohp, 1);
                                            }

                                            $pesan = "Assalamu’alaikum warahmatullahi wabarakatuh,\n\n" .
                                                    "Bapak/Ibu Orang Tua/Wali dari *{$siswa->nama}*,\n\n" .
                                                    "Berdasarkan data administrasi sekolah, berikut rincian tagihan biaya pendidikan:\n" .
                                                    "• Periode Tagihan: *1 Tahun Pelajaran 2025/2026*\n" .
                                                    "• Total Biaya: Rp " . number_format($tagihan['nominal'], 0, ',', '.') . "\n" .
                                                    "• Pembayaran Terkumpul: Rp " . number_format($tagihan['total_bayar'], 0, ',', '.') . "\n" .
                                                    "• Sisa Tagihan: Rp " . number_format($tagihan['sisa'], 0, ',', '.') . "\n\n" .
                                                    "Mohon Bapak/Ibu dapat melakukan pembayaran tersebut dalam waktu dekat.\n\n" .
                                                    "Atas perhatian dan kerja samanya, kami ucapkan terima kasih.\n\n" .
                                                    "Wassalamu’alaikum warahmatullahi wabarakatuh.\n\n" .
                                                    "Hormat kami,\n" .
                                                    "Tata Usaha SMK YPE Sampang";
                                        @endphp

                                        <a href="https://api.whatsapp.com/send?phone={{ $nohp }}&text={{ urlencode($pesan) }}"
                                           target="_blank"
                                           class="btn btn-success btn-sm">
                                           <i class="fab fa-whatsapp"></i> Send Reminder
                                        </a>
                                    @endif
                                </td>
                            </tr>

                            {{-- Row detail --}}
                            <tr class="collapse" id="detail{{ $loop->index }}">
                                <td colspan="6" class="p-0">
                                    <table class="table table-sm table-bordered mb-0">
                                        <thead class="table-info">
                                            <tr>
                                                <th>Detail</th>
                                                <th>Tanggal Bayar</th>
                                                <th>Metode</th>
                                                <th>Jumlah Bayar</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($tagihan['pembayaran'] as $i => $p)
                                                <tr>
                                                    <td>Angsuran {{ $i+1 }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($p->tanggal)->format('d-M-Y H:i:s') }}</td>
                                                    <td>{{ $p->metode_pembayaran ?? '-' }}</td>
                                                    <td>Rp {{ number_format($p->nominal, 0, ',', '.') }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted">Belum ada pembayaran</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada tagihan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @elseif(isset($error) && $error)
        <div class="alert alert-danger mt-3">
            {{ $error }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
  document.querySelectorAll('.toggle-btn').forEach(btn => {
    btn.addEventListener('click', function () {
      // toggle teks +/-
      this.innerText = (this.innerText === "+") ? "-" : "+";
    });
  });

  // jika collapse ditutup, kembalikan tombol ke "+"
  document.querySelectorAll('.collapse').forEach(collapse => {
    collapse.addEventListener('hidden.bs.collapse', function () {
      let btn = document.querySelector(`[data-bs-target="#${this.id}"]`);
      if (btn) btn.innerText = "+";
    });
    collapse.addEventListener('shown.bs.collapse', function () {
      let btn = document.querySelector(`[data-bs-target="#${this.id}"]`);
      if (btn) btn.innerText = "-";
    });
  });
</script>
@endpush
