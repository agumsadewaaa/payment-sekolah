@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1"><i class="fas fa-money-bill-wave me-2 text-primary"></i>Kas Sekolah</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="fas fa-home"></i> Home</a></li>
                    <li class="breadcrumb-item active">Kas Sekolah</li>
                </ol>
            </nav>
        </div>
        @hasanyrole('admin|super-admin')
        <div>
            <a class="btn btn-primary" href="{{ route('kas-sekolahs.create') }}">
                <i class="fas fa-plus me-1"></i>Tambah Kas Sekolah
            </a>
        </div>
        @endhasanyrole
    </div>

    <div class="row">
        <div class="col-12">
            @include('flash::message')
            
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="fas fa-list me-2 text-primary"></i>Daftar Transaksi</h5>
                </div>
                <div class="card-body p-0">
                    @include('partials.bulk-delete', ['route' => 'kas-sekolahs.bulk-delete'])
                    @include('kas_sekolahs.table')
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
