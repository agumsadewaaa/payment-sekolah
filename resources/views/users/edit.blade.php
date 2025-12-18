@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1"><i class="fas fa-user-edit me-2 text-primary"></i>Edit User</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="fas fa-home"></i> Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">User</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            @include('flash::message')
            @include('adminlte-templates::common.errors')
            
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="fas fa-edit me-2 text-primary"></i>Form Edit User</h5>
                </div>
                <form action="{{ route('users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email<span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password Baru</label>
                            <input type="password" class="form-control" id="password" name="password" minlength="8">
                            <small class="text-muted">Kosongkan jika tidak ingin mengubah password</small>
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" minlength="8">
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">Role<span class="text-danger">*</span></label>
                            <select class="form-select" id="role" name="role" required {{ isset($isEditingSelf) && $isEditingSelf ? 'disabled' : '' }}>
                                <option value="">Pilih Role</option>
                                @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                    {{ ucfirst($role->name) }}
                                </option>
                                @endforeach
                                @if($user->hasRole('super-admin'))
                                <option value="super-admin" selected>Super Admin</option>
                                @endif
                            </select>
                            @if(isset($isEditingSelf) && $isEditingSelf)
                            <small class="text-info"><i class="fas fa-info-circle me-1"></i>Anda tidak dapat mengubah role Anda sendiri sebagai Super Admin</small>
                            <input type="hidden" name="role" value="super-admin">
                            @else
                            <small class="text-muted">Pilih role untuk user ini</small>
                            @endif
                        </div>
                    </div>

                    <div class="card-footer bg-white border-top">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Update
                        </button>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i>Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
