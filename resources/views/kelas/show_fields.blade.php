<div class="row g-4">
    <div class="col-12">
        <div class="p-3 bg-light rounded">
            <label class="text-muted small mb-1"><i class="fas fa-barcode me-2"></i>Kode Kelas</label>
            <h5 class="mb-0">{{ $kelas->kode }}</h5>
        </div>
    </div>

    <div class="col-12">
        <div class="p-3 bg-light rounded">
            <label class="text-muted small mb-1"><i class="fas fa-school me-2"></i>Tingkat Kelas</label>
            <h5 class="mb-0">Kelas {{ $kelas->kelas }}</h5>
        </div>
    </div>

    <div class="col-12">
        <div class="p-3 bg-light rounded">
            <label class="text-muted small mb-1"><i class="fas fa-book me-2"></i>Jurusan</label>
            <h5 class="mb-0">{{ $kelas->jurusan }}</h5>
        </div>
    </div>
</div>

