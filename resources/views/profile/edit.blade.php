@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <!-- Page Title & Breadcrumb -->
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4><i class="fas fa-user-circle me-2"></i>Profil Saya</h4>
                    <p class="mb-0">Kelola informasi profil dan password Anda</p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Profil</li>
                </ol>
            </div>
        </div>
    </div>
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title"><i class="fas fa-user-cog me-2 text-primary"></i>Pengaturan Profil</h4>
                    </div>
                    <div class="card-body">
                        {{-- Form: Update Profile Info --}}
                        <form action="{{ route('profile.update') }}" method="POST" class="mb-4">
                            @csrf
                            @method('PUT')

                            <div class="form-group mb-3">
                                <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" placeholder="Masukkan nama lengkap" required>
                                </div>
                                @error('name')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="email" class="form-label">Alamat Email <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" placeholder="Masukkan alamat email" required>
                                </div>
                                @error('email')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">Role</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-shield-alt"></i></span>
                                    <input type="text" class="form-control bg-light" value="{{ $user->roles->pluck('name')->join(', ') ?: 'Tidak Ada Role' }}" readonly>
                                </div>
                            </div>

                            <div class="form-group mb-4">
                                <label class="form-label">Terdaftar Sejak</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                    <input type="text" class="form-control bg-light" value="{{ $user->created_at->format('d F Y, H:i') }}" readonly>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('home') }}" class="btn btn-secondary"><i class="fas fa-times me-1"></i>Batal</a>
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Simpan Perubahan</button>
                            </div>
                        </form>

                        <hr>

                        {{-- Form: Change Password --}}
                        <form action="{{ route('profile.password') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="form-group mb-3">
                                <label for="current_password" class="form-label">Password Saat Ini <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                                    <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" placeholder="Masukkan password saat ini" required>
                                </div>
                                @error('current_password')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="password" class="form-label">Password Baru <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Masukkan password baru" required>
                                </div>
                                <small class="text-muted">Minimal 8 karakter</small>
                                @error('password')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="password_confirmation" class="form-label">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Ulangi password baru" required>
                                </div>
                            </div>

                            <div class="alert alert-warning alert-dismissible fade show">
                                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                                <strong>Penting!</strong> Setelah mengganti password, Anda harus login kembali menggunakan password baru.
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-warning"><i class="fas fa-key me-1"></i>Ganti Password</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>

        <script>
            (function() {
                var shouldLogout = {{ session('password_changed') ? 'true' : 'false' }};
                if (shouldLogout) {
                    var msg = @json(session('password_changed_message', 'Password berhasil diganti. Silakan login kembali menggunakan password baru Anda.'));
                    if (window.Swal && typeof window.Swal.fire === 'function') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: msg,
                            confirmButtonText: 'OK'
                        }).then(function(){
                            document.getElementById('logout-form').submit();
                        });
                    } else {
                        alert(msg);
                        document.getElementById('logout-form').submit();
                    }
                }
            })();
        </script>
    </div>
</div>
@endsection
