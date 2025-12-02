<div class="row g-4">
    <div class="col-12">
        <div class="p-3 bg-light rounded">
            <label class="text-muted small mb-1"><i class="fas fa-school me-2"></i>Kelas</label>
            <h5 class="mb-0">{{ $tagihan->kelass->kode ?? '-' }}</h5>
        </div>
    </div>

    <div class="col-12">
        <div class="p-3 bg-light rounded">
            <label class="text-muted small mb-1"><i class="fas fa-file-invoice me-2"></i>Nama Tagihan</label>
            <h5 class="mb-0">{{ $tagihan->tagihan }}</h5>
        </div>
    </div>

    <div class="col-12">
        <div class="p-3 bg-success bg-opacity-10 rounded border border-success">
            <label class="text-muted small mb-1"><i class="fas fa-money-bill-wave me-2"></i>Nominal</label>
            <h3 class="mb-0 text-success">Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}</h3>
        </div>
    </div>
</div>

<hr class="my-4">

<div class="row">
    <div class="col-md-6">
        <small class="text-muted">
            <i class="far fa-calendar-alt me-1"></i>
            Dibuat: {{ $tagihan->created_at->format('d F Y, H:i') }}
        </small>
    </div>
    <div class="col-md-6 text-md-end">
        <small class="text-muted">
            <i class="far fa-edit me-1"></i>
            Diperbarui: {{ $tagihan->updated_at->format('d F Y, H:i') }}
        </small>
    </div>
</div>

