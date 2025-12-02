@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1"><i class="fas fa-money-bill-wave me-2 text-primary"></i>Detail Transaksi</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="fas fa-home"></i> Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('kas-sekolahs.index') }}">Kas Sekolah</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>
        <div>
            @role('admin')
            <a class="btn btn-warning me-2" href="{{ route('kas-sekolahs.edit', $kasSekolah->id) }}">
                <i class="fas fa-edit me-1"></i>Edit
            </a>
            @endrole
            <a class="btn btn-secondary" href="{{ route('kas-sekolahs.index') }}">
                <i class="fas fa-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 mx-auto">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2 text-primary"></i>Informasi Transaksi</h5>
                </div>
                <div class="card-body p-4">
                    @include('kas_sekolahs.show_fields')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
