@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1"><i class="fas fa-user-graduate me-2 text-primary"></i>Detail Siswa</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="fas fa-home"></i> Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('siswas.index') }}">Siswa</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>
        <div>
            @hasanyrole('admin|super-admin')
            <a class="btn btn-warning me-2" href="{{ route('siswas.edit', $siswa->id) }}">
                <i class="fas fa-edit me-1"></i>Edit
            </a>
            @endhasanyrole
            <a class="btn btn-secondary" href="{{ route('siswas.index') }}">
                <i class="fas fa-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2 text-primary"></i>Informasi Siswa</h5>
                </div>
                <div class="card-body p-4">
                    @include('siswas.show_fields')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
