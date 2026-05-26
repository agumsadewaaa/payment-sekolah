@extends('layouts.app')

@section('content')
<div class="container">
    @if (session('status'))
        <div class="alert alert-success mb-3">
            {{ session('status') }}
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3">
            <i class="fas fa-exclamation-circle me-2"></i>{!! nl2br(e(session('error'))) !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-user-graduate me-2"></i>Kenaikan Kelas & Kelulusan</h5>
        </div>
        <div class="card-body">
            <p>Gunakan tombol ini untuk otomatisasi kenaikan kelas & kelulusan siswa.</p>

            <form method="POST" action="{{ route('admin.promote') }}">
                @csrf
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-arrow-up me-2"></i>Proses Kenaikan & Kelulusan
                </button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-file-excel me-2"></i>Import Data Siswa & Pembayaran dari Excel</h5>
        </div>
        <div class="card-body">
            <p class="mb-3">Upload file Excel untuk menambahkan data siswa dan pembayaran tagihan secara massal.</p>

            @if(isset($totalTagihan) && $totalTagihan == 0)
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Perhatian!</strong> Belum ada data tagihan di database.<br>
                Kolom pembayaran tidak akan muncul di template Excel.<br>
                Silakan tambahkan data tagihan terlebih dahulu di menu <strong>Kelola Tagihan</strong>, 
                kemudian klik <strong>Re-generate Template</strong> untuk update template dengan kolom pembayaran.
            </div>
            @elseif(isset($totalTagihan))
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i>
                Ditemukan <strong>{{ $totalTagihan }}</strong> tagihan. Template Excel akan include kolom pembayaran.
            </div>
            @endif

            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Petunjuk:</strong>
                <ol class="mb-0 mt-2">
                    <li>Download template Excel terlebih dahulu</li>
                    <li>Isi data siswa: <strong>Nama, NIS, Kontak Ortu, Kelas, Jurusan, Tahun Masuk</strong></li>
                    @if(isset($totalTagihan) && $totalTagihan > 0)
                    <li><strong>FITUR PEMBAYARAN:</strong> Isi kolom tagihan (hijau) untuk pembayaran yang sudah dibayar
                        <ul>
                            <li>Isi dengan nominal pembayaran (tanpa titik/koma)</li>
                            <li>Kosongkan jika belum ada pembayaran</li>
                            <li>Sistem akan otomatis mencatat pembayaran ke database</li>
                        </ul>
                    </li>
                    @endif
                    <li>Pastikan kombinasi <strong>Kelas dan Jurusan</strong> sudah ada di database</li>
                    <li>NIS harus unik (10 digit) dan belum terdaftar</li>
                    <li>Hapus 2 baris contoh data sebelum upload</li>
                    <li>Upload file yang sudah diisi</li>
                </ol>
            </div>
            
            @if(isset($totalTagihan) && $totalTagihan > 0)
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i>
                <strong>Keuntungan fitur import pembayaran:</strong>
                <ul class="mb-0 mt-2">
                    <li>Siswa kelas 12 bisa langsung diinput dengan pembayaran kelas 10 & 11</li>
                    <li>Tidak perlu input pembayaran manual satu per satu</li>
                    <li>Data pembayaran akan tersimpan dengan tanggal import</li>
                    <li>Status pembayaran otomatis "Lunas" untuk nominal yang diisi</li>
                </ul>
            </div>
            @endif

            <div class="mb-3">
                <a href="{{ route('admin.download-template') }}?t={{ time() }}" class="btn btn-info me-2">
                    <i class="fas fa-download me-2"></i>Download Template Excel
                </a>
                <a href="{{ route('admin.generate-template') }}" class="btn btn-outline-info">
                    <i class="fas fa-sync-alt me-2"></i>Re-generate Template
                </a>
                <small class="d-block mt-2 text-muted">
                    <i class="fas fa-info-circle me-1"></i>Klik "Re-generate" jika template rusak atau belum ada, atau ada perubahan tagihan baru
                </small>
            </div>

            <hr>

            <form method="POST" action="{{ route('admin.import-siswa') }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="file" class="form-label">Pilih File Excel</label>
                    <input type="file" class="form-control @error('file') is-invalid @enderror" 
                           id="file" name="file" accept=".xlsx,.xls" required>
                    @error('file')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Format: .xlsx atau .xls (Max: 2MB)</small>
                </div>

                <button type="submit" class="btn btn-success">
                    <i class="fas fa-upload me-2"></i>Upload & Import Data
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
