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
            <i class="fas fa-exclamation-circle me-2"></i>{!! session('error') !!}
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
            <h5 class="mb-0"><i class="fas fa-file-excel me-2"></i>Import Data Siswa dari Excel</h5>
        </div>
        <div class="card-body">
            <p class="mb-3">Upload file Excel untuk menambahkan data siswa secara massal.</p>

            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Petunjuk:</strong>
                <ol class="mb-0 mt-2">
                    <li>Download template Excel terlebih dahulu</li>
                    <li>Isi data siswa sesuai format: <strong>Nama, NISN, Kontak Ortu, Kelas, Jurusan, Tahun Masuk</strong></li>
                    <li>Pastikan kombinasi <strong>Kelas dan Jurusan</strong> sudah ada di database</li>
                    <li>NISN harus unik (10 digit) dan belum terdaftar</li>
                    <li>Hapus 3 baris contoh data sebelum upload</li>
                    <li>Upload file yang sudah diisi</li>
                </ol>
            </div>

            <div class="mb-3">
                <a href="{{ route('admin.download-template') }}" class="btn btn-info me-2">
                    <i class="fas fa-download me-2"></i>Download Template Excel
                </a>
                <a href="{{ route('admin.generate-template') }}" class="btn btn-outline-info">
                    <i class="fas fa-sync-alt me-2"></i>Re-generate Template
                </a>
                <small class="d-block mt-2 text-muted">
                    <i class="fas fa-info-circle me-1"></i>Klik "Re-generate" jika template rusak atau belum ada
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
