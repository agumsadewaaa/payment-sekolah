<div class="row g-4">
    <div class="col-md-6">
        <div class="p-3 bg-light rounded">
            <label class="text-muted small mb-1"><i class="fas fa-user me-2"></i>Nama Lengkap</label>
            <h5 class="mb-0">{{ $siswa->nama }}</h5>
        </div>
    </div>

    <div class="col-md-6">
        <div class="p-3 bg-light rounded">
            <label class="text-muted small mb-1"><i class="fas fa-id-card me-2"></i>NIS</label>
            <h5 class="mb-0" style="font-family: monospace;">{{ $siswa->nis }}</h5>
        </div>
    </div>

    <div class="col-md-6">
        <div class="p-3 bg-light rounded">
            <label class="text-muted small mb-1"><i class="fas fa-school me-2"></i>Kelas</label>
            <h5 class="mb-0">{{ $siswa->kelas == 0 ? 'Lulus' : $siswa->kelas }}</h5>
        </div>
    </div>

    <div class="col-md-6">
        <div class="p-3 bg-light rounded">
            <label class="text-muted small mb-1"><i class="fas fa-book me-2"></i>Jurusan</label>
            <h5 class="mb-0">{{ $siswa->jurusans ? $siswa->jurusans->jurusan : '-' }}</h5>
        </div>
    </div>

    <div class="col-md-6">
        <div class="p-3 bg-light rounded">
            <label class="text-muted small mb-1"><i class="fas fa-phone me-2"></i>Kontak Orang Tua</label>
            <h5 class="mb-0" style="font-family: monospace;">{{ $siswa->kontak_ortu }}</h5>
        </div>
    </div>

    <div class="col-md-6">
        <div class="p-3 bg-light rounded">
            <label class="text-muted small mb-1"><i class="fas fa-toggle-on me-2"></i>Status Siswa</label>
            <h5 class="mb-0">
                @if($siswa->status_siswa == 'aktif')
                    <span class="badge bg-success">Aktif</span>
                @else
                    <span class="badge bg-secondary">{{ ucfirst($siswa->status_siswa) }}</span>
                @endif
            </h5>
        </div>
    </div>

    <div class="col-md-6">
        <div class="p-3 bg-light rounded">
            <label class="text-muted small mb-1"><i class="fas fa-calendar-plus me-2"></i>Tahun Masuk</label>
            <h5 class="mb-0">{{ $siswa->tahun_masuk }}</h5>
        </div>
    </div>

    <div class="col-md-6">
        <div class="p-3 bg-light rounded">
            <label class="text-muted small mb-1"><i class="fas fa-graduation-cap me-2"></i>Tahun Lulus</label>
            <h5 class="mb-0">{{ $siswa->tahun_lulus ?? '-' }}</h5>
        </div>
    </div>
</div>

<hr class="my-4">

<div class="row">
    <div class="col-md-6">
        <small class="text-muted">
            <i class="far fa-calendar-alt me-1"></i>
            Dibuat: {{ $siswa->created_at->format('d F Y, H:i') }}
        </small>
    </div>
    <div class="col-md-6 text-md-end">
        <small class="text-muted">
            <i class="far fa-edit me-1"></i>
            Diperbarui: {{ $siswa->updated_at->format('d F Y, H:i') }}
        </small>
    </div>
</div>

