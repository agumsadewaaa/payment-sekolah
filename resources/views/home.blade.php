@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">Dashboard</h1>

    {{-- Switch Range --}}
    <div class="d-flex flex-wrap gap-2 mb-3">
        @php $makeUrl = fn($r) => request()->fullUrlWithQuery(['range' => $r]); @endphp

        <a href="{{ $makeUrl('today') }}" class="btn btn-sm {{ $range==='today'?'btn-primary':'btn-outline-primary' }}">
            Hari Ini
        </a>
        <a href="{{ $makeUrl('week') }}" class="btn btn-sm {{ $range==='week'?'btn-primary':'btn-outline-primary' }}">
            Minggu Ini
        </a>
        <a href="{{ $makeUrl('month') }}" class="btn btn-sm {{ $range==='month'?'btn-primary':'btn-outline-primary' }}">
            Bulan Ini
        </a>

        <span class="ms-2 text-muted align-self-center">
            Periode: <strong>{{ $start->format('d M Y') }}</strong> s.d. <strong>{{ $end->format('d M Y') }}</strong>
        </span>
    </div>

    {{-- Statistik --}}
    <div class="row">
        <div class="col-xl-3 col-xxl-6 col-lg-6 col-sm-6">
            <div class="widget-stat card">
                <div class="card-body p-4">
                    <div class="media ai-icon">
                        <span class="me-3 bgl-primary text-primary">
                            <i class="fa fa-user" style="font-size: 28px;"></i>
                        </span>
                        <div class="media-body">
                            <p class="mb-1">Total Siswa</p>
                            <h4 class="mb-0">{{ $totalSiswa ?? 0 }}</h4>
                            <span>orang</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Kas (all-time) --}}
        <div class="col-xl-3 col-xxl-6 col-lg-6 col-sm-6">
            <div class="widget-stat card">
                <div class="card-body p-4">
                    <div class="media ai-icon">
                        <span class="me-3 bgl-danger text-warning">
                            <i class="fa fa-money-check-dollar" style="font-size: 28px;"></i>
                        </span>
                        <div class="media-body">
                            <p class="mb-1">Total Kas</p>
                            <h4 class="mb-0">@if(is_null($totalKas)) — @else Rp {{ number_format($totalKas, 0, ',', '.') }} @endif</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pendapatan (range terpilih) --}}
        <div class="col-xl-3 col-xxl-6 col-lg-6 col-sm-6">
            <div class="widget-stat card">
                <div class="card-body p-4">
                    <div class="media ai-icon">
                        <span class="me-3 bgl-danger text-success">
                            <i class="fa fa-download" style="font-size: 28px;"></i>
                        </span>
                        <div class="media-body">
                            <p class="mb-1">Pendapatan ({{ $rangeLabel }})</p>
                            <h4 class="mb-0 text-success">@if(is_null($pemasukanRange)) — @else Rp {{ number_format($pemasukanRange, 0, ',', '.') }} @endif</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pengeluaran (range terpilih) --}}
        <div class="col-xl-3 col-xxl-6 col-lg-6 col-sm-6">
            <div class="widget-stat card">
                <div class="card-body p-4">
                    <div class="media ai-icon">
                        <span class="me-3 bgl-danger text-danger">
                            <i class="fa fa-upload" style="font-size: 28px;"></i>
                        </span>
                        <div class="media-body">
                            <p class="mb-1">Pengeluaran ({{ $rangeLabel }})</p>
                            <h4 class="mb-0 text-danger">@if(is_null($pengeluaranRange)) — @else Rp {{ number_format($pengeluaranRange, 0, ',', '.') }} @endif</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Grafik Kas --}}
    <div class="card shadow-sm my-4">
        <div class="card-header">
            Grafik Kas ({{ $rangeLabel }}) — {{ $start->format('d M Y') }} s.d. {{ $end->format('d M Y') }}
        </div>
        <div class="card-body">
            <canvas id="kasChart" height="100"></canvas>
        </div>
    </div>

    <div class="row">
        {{-- List siswa progress rendah --}}
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header">Siswa Progress &lt; 50%</div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Kelas</th>
                                <th>Progress</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(is_null($siswaProgress) || $siswaProgress->isEmpty())
                                <tr><td colspan="3" class="text-center text-muted">Tidak ada data</td></tr>
                            @else
                                @foreach($siswaProgress as $siswa)
                                <tr>
                                    <td>{{ $siswa->nama }}</td>
                                    <td>{{ $siswa->kode }}</td>
                                    <td>
                                        <div class="progress" style="height:10px;">
                                            <div class="progress-bar bg-danger" style="width: {{ $siswa->progress }}%"></div>
                                        </div>
                                        <small>{{ $siswa->progress }}%</small>
                                    </td>
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Latest Pengeluaran --}}
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header">Latest Pengeluaran</div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Catatan</th>
                                <th>Nominal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(is_null($latestPengeluaran) || $latestPengeluaran->isEmpty())
                                <tr><td colspan="3" class="text-center text-muted">Tidak ada data</td></tr>
                            @else
                                @foreach($latestPengeluaran as $out)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($out->tanggal)->format('d-M-Y') }}</td>
                                    <td>{{ $out->catatan }}</td>
                                    <td class="text-danger">Rp {{ number_format($out->nominal, 0, ',', '.') }}</td>
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
