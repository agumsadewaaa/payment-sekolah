<div class="row g-4">
    <div class="col-12">
        <div class="p-3 bg-light rounded">
            <label class="text-muted small mb-1"><i class="fas fa-calendar me-2"></i>Tanggal</label>
            <h5 class="mb-0">{{ \Carbon\Carbon::parse($kasSekolah->tanggal)->format('d F Y') }}</h5>
        </div>
    </div>

    <div class="col-12">
        <div class="p-3 bg-light rounded">
            <label class="text-muted small mb-1"><i class="fas fa-exchange-alt me-2"></i>Tipe Transaksi</label>
            <h5 class="mb-0">
                @if($kasSekolah->tipe == '1')
                    <span class="badge bg-success"><i class="fas fa-arrow-down me-1"></i>Pendapatan</span>
                @else
                    <span class="badge bg-danger"><i class="fas fa-arrow-up me-1"></i>Pengeluaran</span>
                @endif
            </h5>
        </div>
    </div>

    <div class="col-12">
        <div class="p-3 {{ $kasSekolah->tipe == '1' ? 'bg-success' : 'bg-danger' }} bg-opacity-10 rounded border border-{{ $kasSekolah->tipe == '1' ? 'success' : 'danger' }}">
            <label class="text-muted small mb-1"><i class="fas fa-money-bill-wave me-2"></i>Nominal</label>
            <h3 class="mb-0 text-{{ $kasSekolah->tipe == '1' ? 'success' : 'danger' }}">Rp {{ number_format($kasSekolah->nominal, 0, ',', '.') }}</h3>
        </div>
    </div>

    <div class="col-12">
        <div class="p-3 bg-light rounded">
            <label class="text-muted small mb-1"><i class="fas fa-sticky-note me-2"></i>Catatan</label>
            <p class="mb-0">{{ $kasSekolah->catatan ?: '-' }}</p>
        </div>
    </div>
</div>

<hr class="my-4">

<div class="row">
    <div class="col-md-6">
        <small class="text-muted">
            <i class="far fa-calendar-alt me-1"></i>
            Dibuat: {{ $kasSekolah->created_at->format('d F Y, H:i') }}
        </small>
    </div>
    <div class="col-md-6 text-md-end">
        <small class="text-muted">
            <i class="far fa-edit me-1"></i>
            Diperbarui: {{ $kasSekolah->updated_at->format('d F Y, H:i') }}
        </small>
    </div>
</div>

