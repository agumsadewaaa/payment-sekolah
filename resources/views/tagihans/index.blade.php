@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1"><i class="fas fa-file-invoice-dollar me-2 text-primary"></i>Data Tagihan</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="fas fa-home"></i> Home</a></li>
                    <li class="breadcrumb-item active">Tagihan</li>
                </ol>
            </nav>
        </div>
        @hasanyrole('admin|super-admin')
        <div>
            <a class="btn btn-primary" href="{{ route('tagihans.create') }}">
                <i class="fas fa-plus me-1"></i>Tambah Tagihan
            </a>
        </div>
        @endhasanyrole
    </div>

    <div class="row">
        <div class="col-12">
            @include('flash::message')
            
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="fas fa-list me-2 text-primary"></i>Daftar Tagihan</h5>
                </div>
                <div class="card-body p-0">
                    @include('partials.bulk-delete', ['route' => 'tagihans.bulk-delete'])
                    @include('tagihans.table')
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
