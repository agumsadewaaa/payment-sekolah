@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0"><i class="fas fa-chart-line me-2"></i>Dashboard</h1>
        <div class="text-muted">
            <i class="fas fa-calendar-alt me-1"></i>
            {{ now()->format('d F Y') }}
        </div>
    </div>

    {{-- Switch Range --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body py-3">
            <div class="d-flex flex-wrap align-items-center gap-2">
                <span class="text-muted me-2"><i class="fas fa-filter me-1"></i>Filter Periode:</span>
                @php $makeUrl = fn($r) => request()->fullUrlWithQuery(['range' => $r]); @endphp

                <a href="{{ $makeUrl('today') }}" class="btn btn-sm {{ $range==='today'?'btn-primary':'btn-outline-primary' }}">
                    <i class="fas fa-calendar-day me-1"></i>Hari Ini
                </a>
                <a href="{{ $makeUrl('week') }}" class="btn btn-sm {{ $range==='week'?'btn-primary':'btn-outline-primary' }}">
                    <i class="fas fa-calendar-week me-1"></i>Minggu Ini
                </a>
                <a href="{{ $makeUrl('month') }}" class="btn btn-sm {{ $range==='month'?'btn-primary':'btn-outline-primary' }}">
                    <i class="fas fa-calendar-alt me-1"></i>Bulan Ini
                </a>

                <div class="ms-auto">
                    <span class="badge bg-light text-dark border">
                        <i class="fas fa-clock me-1"></i>
                        {{ $start->format('d M Y') }} - {{ $end->format('d M Y') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistik --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-primary bg-gradient d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="fas fa-users text-white" style="font-size: 24px;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1 small text-uppercase">Total Siswa</p>
                            <h3 class="mb-0 fw-bold">{{ number_format($totalSiswa ?? 0) }}</h3>
                            <small class="text-muted">Siswa aktif</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Kas (all-time) --}}
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-warning bg-gradient d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="fas fa-wallet text-white" style="font-size: 24px;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1 small text-uppercase">Total Kas</p>
                            <h3 class="mb-0 fw-bold text-warning">
                                @if(is_null($totalKas)) 
                                    —
                                @else 
                                    Rp {{ number_format($totalKas, 0, ',', '.') }}
                                @endif
                            </h3>
                            <small class="text-muted">Saldo keseluruhan</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pendapatan (range terpilih) --}}
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-success bg-gradient d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="fas fa-arrow-down text-white" style="font-size: 24px;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1 small text-uppercase">Pendapatan</p>
                            <h3 class="mb-0 fw-bold text-success">
                                @if(is_null($pemasukanRange)) 
                                    —
                                @else 
                                    Rp {{ number_format($pemasukanRange, 0, ',', '.') }}
                                @endif
                            </h3>
                            <small class="text-muted">{{ $rangeLabel }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pengeluaran (range terpilih) --}}
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-danger bg-gradient d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="fas fa-arrow-up text-white" style="font-size: 24px;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1 small text-uppercase">Pengeluaran</p>
                            <h3 class="mb-0 fw-bold text-danger">
                                @if(is_null($pengeluaranRange)) 
                                    —
                                @else 
                                    Rp {{ number_format($pengeluaranRange, 0, ',', '.') }}
                                @endif
                            </h3>
                            <small class="text-muted">{{ $rangeLabel }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Grafik Kas --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-bottom">
            <div class="d-flex align-items-center">
                <i class="fas fa-chart-bar me-2 text-primary"></i>
                <h5 class="mb-0">Grafik Kas - {{ $rangeLabel }}</h5>
                <span class="ms-auto badge bg-light text-dark">
                    {{ $start->format('d M Y') }} - {{ $end->format('d M Y') }}
                </span>
            </div>
        </div>
        <div class="card-body p-4">
            <canvas id="kasChart" height="80"></canvas>
        </div>
    </div>

    <div class="row g-3">
        {{-- List siswa progress rendah --}}
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                        <strong>Siswa Progress &lt; 50%</strong>
                    </div>
                    <a href="{{ route('home.export-low-progress') }}" class="btn btn-sm btn-success">
                        <i class="fas fa-file-excel me-1"></i> Download Excel
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th class="px-3">Nama Siswa</th>
                                    <th class="px-3">Kelas</th>
                                    <th class="px-3" style="min-width: 150px;">Progress</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(is_null($siswaProgress) || $siswaProgress->isEmpty())
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">
                                            <i class="fas fa-check-circle fa-2x mb-2 d-block text-success"></i>
                                            Semua siswa memiliki progress baik
                                        </td>
                                    </tr>
                                @else
                                    @foreach($siswaProgress as $siswa)
                                    <tr>
                                        <td class="px-3 align-middle">
                                            <i class="fas fa-user-circle text-muted me-1"></i>
                                            {{ $siswa->nama }}
                                        </td>
                                        <td class="px-3 align-middle">
                                            <span class="badge bg-info">{{ $siswa->kode }}</span>
                                        </td>
                                        <td class="px-3 align-middle">
                                            @php
                                                $isTunggakan = is_string($siswa->progress) && strpos((string)$siswa->progress, '-') === 0;
                                                $progressValue = $isTunggakan ? ltrim($siswa->progress, '-') : $siswa->progress;
                                            @endphp
                                            @if($isTunggakan)
                                                <div class="d-flex align-items-center">
                                                    <span class="badge bg-danger text-white" style="min-width: 90px; font-size: 0.9rem;">
                                                        <i class="fas fa-exclamation-circle me-1"></i> -{{ str_replace('.', ',', $progressValue) }}%
                                                    </span>
                                                    <small class="text-muted ms-2">(Tunggakan kelas lama)</small>
                                                </div>
                                            @else
                                                <div class="d-flex align-items-center">
                                                    <div class="progress flex-grow-1 me-2" style="height: 18px;">
                                                        <div class="progress-bar {{ round($siswa->progress) < 25 ? 'bg-danger' : 'bg-warning' }}" 
                                                             role="progressbar"
                                                             style="width: {{ round($siswa->progress) }}%"
                                                             aria-valuenow="{{ round($siswa->progress) }}" 
                                                             aria-valuemin="0" 
                                                             aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                    <span class="badge {{ round($siswa->progress) < 25 ? 'bg-danger' : 'bg-warning' }} text-white" style="min-width: 45px;">
                                                        {{ round($siswa->progress) }}%
                                                    </span>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Latest Pengeluaran --}}
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom">
                    <i class="fas fa-receipt text-danger me-2"></i>
                    <strong>Pengeluaran Terbaru</strong>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th class="px-3">Tanggal</th>
                                    <th class="px-3">Catatan</th>
                                    <th class="px-3 text-end">Nominal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(is_null($latestPengeluaran) || $latestPengeluaran->isEmpty())
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                            Tidak ada data pengeluaran
                                        </td>
                                    </tr>
                                @else
                                    @foreach($latestPengeluaran as $out)
                                    <tr>
                                        <td class="px-3 align-middle">
                                            <i class="far fa-calendar-alt text-muted me-1"></i>
                                            <small>{{ \Carbon\Carbon::parse($out->tanggal)->format('d M Y') }}</small>
                                        </td>
                                        <td class="px-3 align-middle">
                                            <div class="text-truncate" style="max-width: 250px;" title="{{ $out->catatan }}">
                                                {{ $out->catatan }}
                                            </div>
                                        </td>
                                        <td class="px-3 align-middle text-end">
                                            <span class="badge bg-danger">
                                                Rp {{ number_format($out->nominal, 0, ',', '.') }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('kasChart').getContext('2d');

    const labels   = {!! json_encode($tanggalBulan ?? []) !!};
    const pemasukan = {!! json_encode($dataPemasukan ?? []) !!};
    const pengeluaran = {!! json_encode($dataPengeluaran ?? []) !!};

    new Chart(ctx, {
        data: {
            labels: labels,
            datasets: [
                {
                    type: 'bar',
                    label: 'Pendapatan',
                    data: pemasukan,
                    backgroundColor: 'rgba(75, 192, 192, 0.7)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                },
                {
                    type: 'bar',
                    label: 'Pengeluaran',
                    data: pengeluaran,
                    backgroundColor: 'rgba(255, 99, 132, 0.7)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                },
                {
                    type: 'line',
                    label: 'Pendapatan (Line)',
                    data: pemasukan,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.3)',
                    fill: false,
                    tension: 0.3,
                    pointRadius: 4,
                    pointBackgroundColor: 'rgba(75, 192, 192, 1)'
                },
                {
                    type: 'line',
                    label: 'Pengeluaran (Line)',
                    data: pengeluaran,
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.3)',
                    fill: false,
                    tension: 0.3,
                    pointRadius: 4,
                    pointBackgroundColor: 'rgba(255, 99, 132, 1)'
                }
            ]
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: (value) => new Intl.NumberFormat('id-ID').format(value)
                    }
                },
                x: {
                    ticks: {
                        callback: (val, idx) => {
                            const d = labels[idx] || '';
                            // tampilkan ringkas: dd/MM
                            return d ? d.slice(8,10) + '/' + d.slice(5,7) : '';
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: (ctx) => {
                            const v = ctx.parsed.y ?? 0;
                            return `${ctx.dataset.label}: Rp ${new Intl.NumberFormat('id-ID').format(v)}`;
                        }
                    }
                },
                legend: { position: 'top' }
            }
        }
    });
</script>
@endpush
