@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4 text-black-50">Cek Tagihan Siswa</h1>

    {{-- Form Pencarian --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('cek-tagihan') }}" method="GET" class="row g-3">
                <div class="col-md-5">
                    <input type="text" name="keyword" class="form-control" placeholder="Masukkan Nama atau NISN" value="{{ request('keyword') }}" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Cari</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Jika ada hasil --}}
    @if($siswa)
        <div class="card shadow-sm">
            <div class="card-header"><strong>Detail Siswa</strong></div>
            <div class="card-body">
                <p><strong>Nama:</strong> {{ $siswa->nama }}</p>
                <p><strong>NISN:</strong> {{ $siswa->nisn }}</p>
                <p><strong>Kelas:</strong> {{ $siswa->kelas }}</p>
                <p>
                    <b>Progress Pembayaran:</b> 
                    {{ $progress }}% 
                    (Rp {{ number_format($totalBayar, 0, ',', '.') }} / Rp {{ number_format($totalTagihan, 0, ',', '.') }})
                </p>

                {{-- Progress Bar --}}
                <div style="background:#eee; border-radius:5px; width:300px; height:20px; overflow:hidden;">
                    <div style="background:#28a745; width:{{ $progress }}%; height:100%;"></div>
                </div>
        </div>

        <div class="card mt-3 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Daftar Tagihan</strong>
                <div>
                    <a href="{{ route('tagihan.print', $siswa->id) }}" class="btn btn-sm btn-secondary" target="_blank">🖨️ Print</a>
                    <a href="{{ route('tagihan.export', $siswa->id) }}" class="btn btn-sm btn-success">⬇️ Export</a>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
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
                                                    <td>{{ \Carbon\Carbon::parse($p->tanggal)->format('d-M-Y') }}</td>
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

    @elseif(request('keyword'))
        <div class="alert alert-danger mt-3">
            Data siswa dengan keyword <strong>{{ request('keyword') }}</strong> tidak ditemukan.
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
