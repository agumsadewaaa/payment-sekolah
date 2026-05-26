@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1"><i class="fas fa-cash-register me-2 text-primary"></i>Laporan Kas Sekolah</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="fas fa-home"></i> Home</a></li>
                    <li class="breadcrumb-item active">Cek Kas</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- Form Time Range --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0"><i class="fas fa-filter me-2 text-primary"></i>Filter Periode</h5>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('cek-kas') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Tanggal Awal</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                        <input type="text" name="from" class="form-control datepicker-default" value="{{ $from }}" placeholder="Pilih tanggal" required autocomplete="off">
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tanggal Akhir</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                        <input type="text" name="to" class="form-control datepicker-default" value="{{ $to }}" placeholder="Pilih tanggal" required autocomplete="off">
                    </div>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i>Tampilkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if($from && $to)
    {{-- Ringkasan --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-secondary bg-gradient d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="fas fa-wallet text-white" style="font-size: 22px;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1 small">Saldo Sebelumnya</p>
                            <h5 class="mb-0 fw-bold">Rp {{ number_format($summary['saldo_sebelumnya'], 0, ',', '.') }}</h5>
                            <small class="text-muted">s.d. {{ \Carbon\Carbon::parse($from)->subDay()->format('d M Y') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-success bg-gradient d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="fas fa-arrow-down text-white" style="font-size: 22px;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1 small">Total Pendapatan</p>
                            <h5 class="mb-0 fw-bold text-success">Rp {{ number_format($summary['total_pendapatan'], 0, ',', '.') }}</h5>
                            <small class="text-muted">Periode ini</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-danger bg-gradient d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="fas fa-arrow-up text-white" style="font-size: 22px;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1 small">Total Pengeluaran</p>
                            <h5 class="mb-0 fw-bold text-danger">Rp {{ number_format($summary['total_pengeluaran'], 0, ',', '.') }}</h5>
                            <small class="text-muted">Periode ini</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-primary bg-gradient d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="fas fa-coins text-white" style="font-size: 22px;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1 small">Saldo Akhir</p>
                            <h5 class="mb-0 fw-bold text-primary">Rp {{ number_format($summary['saldo_akhir'], 0, ',', '.') }}</h5>
                            <small class="text-muted">Per {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- DataTable --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-list me-2 text-primary"></i>Transaksi ({{ \Carbon\Carbon::parse($from)->format('d M Y') }} - {{ \Carbon\Carbon::parse($to)->format('d M Y') }})</h5>
            <div>
                <a href="{{ route('kas.export', ['from' => $from, 'to' => $to]) }}" class="btn btn-success btn-sm">
                    <i class="fas fa-file-excel me-1"></i>Export Excel
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="kasTable" class="table table-hover mb-0">
                    <thead class="table-primary">
                        <tr>
                            <th>Tanggal</th>
                            <th>Catatan</th>
                            <th>Tipe</th>
                            <th>Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rows as $r)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($r->tanggal)->format('d-m-Y H:i:s') }}</td>
                                <td>{{ $r->catatan }}</td>
                                <td>
                                    @if($r->tipe == '1')
                                        <span class="badge bg-success">Pendapatan</span>
                                    @else
                                        <span class="badge bg-danger">Pengeluaran</span>
                                    @endif
                                </td>
                                <td>Rp {{ number_format($r->nominal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="3" class="text-end">Total Pendapatan:</th>
                            <th>Rp {{ number_format($summary['total_pendapatan'], 0, ',', '.') }}</th>
                        </tr>
                        <tr>
                            <th colspan="3" class="text-end">Total Pengeluaran:</th>
                            <th>Rp {{ number_format($summary['total_pengeluaran'], 0, ',', '.') }}</th>
                        </tr>
                        <tr>
                            <th colspan="3" class="text-end">Saldo Sebelumnya:</th>
                            <th>Rp {{ number_format($summary['saldo_sebelumnya'], 0, ',', '.') }}</th>
                        </tr>
                        <tr>
                            <th colspan="3" class="text-end">Saldo Akhir:</th>
                            <th>Rp {{ number_format($summary['saldo_akhir'], 0, ',', '.') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('styles')
    {{-- DataTables + Buttons CSS (CDN) --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/v/bs5/dt-2.1.8/b-3.1.2/r-3.0.3/datatables.min.css"/>
@endpush

@push('scripts')
    {{-- DataTables + Buttons JS (CDN) --}}
    <script src="https://cdn.datatables.net/v/bs5/dt-2.1.8/b-3.1.2/r-3.0.3/datatables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

    <script>
        $(function() {
            // Initialize datepicker
            $('input[name="from"], input[name="to"]').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                locale: {
                    format: 'YYYY-MM-DD',
                    applyLabel: 'Pilih',
                    cancelLabel: 'Batal'
                }
            });

            // DataTable initialization
            const table = new DataTable('#kasTable', {
                responsive: true,
                pageLength: 25,
                ordering: true,
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Kas-Sekolah-{{ $from ?? "" }}-{{ $to ?? "" }}',
                        exportOptions: { columns: [0,1,2,3] }
                    },
                    { extend: 'print', title: 'Kas Sekolah' },
                    { extend: 'copy' }
                ],
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/2.1.8/i18n/id.json'
                }
            });
        });
    </script>
@endpush
