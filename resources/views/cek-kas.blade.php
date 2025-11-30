@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4 text-black-50">Kas Sekolah</h1>

    {{-- Form Time Range --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('cek-kas') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">From</label>
                    <input type="date" name="from" class="form-control" value="{{ $from }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">To</label>
                    <input type="date" name="to" class="form-control" value="{{ $to }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Cari</button>
                </div>
            </form>
        </div>
    </div>

    @if($from && $to)
    {{-- Ringkasan --}}
    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="fw-bold text-muted">Saldo Sebelumnya (s.d. {{ \Carbon\Carbon::parse($from)->subDay()->format('d M Y') }})</div>
                    <div class="fs-4">Rp {{ number_format($summary['saldo_sebelumnya'], 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="fw-bold text-muted">Total Pendapatan</div>
                    <div class="fs-4 text-success">Rp {{ number_format($summary['total_pendapatan'], 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="fw-bold text-muted">Total Pengeluaran</div>
                    <div class="fs-4 text-danger">Rp {{ number_format($summary['total_pengeluaran'], 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="fw-bold text-muted">Saldo Akhir</div>
                    <div class="fs-4">Rp {{ number_format($summary['saldo_akhir'], 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- DataTable --}}
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong>Transaksi ({{ \Carbon\Carbon::parse($from)->format('d M Y') }} - {{ \Carbon\Carbon::parse($to)->format('d M Y') }})</strong>
            <div>
                <a href="{{ route('kas.export', ['from' => $from, 'to' => $to]) }}" class="btn btn-success btn-sm">
                    ⬇️ Export Excel (Format Buku Kas)
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="kasTable" class="table table-bordered table-striped w-100">
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
                                <td>{{ \Carbon\Carbon::parse($r->tanggal)->format('d-m-Y') }}</td>
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
    {{-- jQuery (jika belum ada), DataTables + Buttons JS (CDN) --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/v/bs5/dt-2.1.8/b-3.1.2/r-3.0.3/datatables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js" integrity="sha512-wZq9cC9b6mCw3z3BzCz+0jWyyM0KBLiKfD6w3m4mV2q2GQ4v3Cw2p8c0m9QW2+QJz0Z8JwN3xTksd7Hq0mJQFQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
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
