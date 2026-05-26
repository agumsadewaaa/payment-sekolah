@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="mb-1"><i class="fas fa-history me-2 text-primary"></i>Log Aktivitas</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="fas fa-home"></i> Home</a></li>
                    <li class="breadcrumb-item active">Activity Log</li>
                </ol>
            </nav>
        </div>
        <div>
            <span class="badge bg-info fs-6">Total: {{ $logs->total() }} log</span>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <!-- Filter Form -->
            <form method="GET" action="{{ route('activity-logs.index') }}" class="mb-3">
                <div class="row g-2 align-items-center">
                    <div class="col-auto filter-user">
                        <select class="selectpicker" id="user_id" name="user_id" data-width="300px" data-live-search="true" data-style="btn-light">
                            <option value="">👤 Semua User</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto filter-action">
                        <select class="selectpicker" id="action" name="action" data-width="300px" data-live-search="true" data-style="btn-light">
                            <option value="">⚡ Semua Aksi</option>
                            <option value="created" {{ request('action') == 'created' ? 'selected' : '' }}>Created</option>
                            <option value="updated" {{ request('action') == 'updated' ? 'selected' : '' }}>Updated</option>
                            <option value="deleted" {{ request('action') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}" style="min-width: 180px;">
                    </div>
                    <div class="col-auto">
                        <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}" style="min-width: 180px;">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary px-5">
                            <i class="fas fa-search me-2"></i>Filter
                        </button>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('activity-logs.index') }}" class="btn btn-outline-secondary px-5">
                            <i class="fas fa-redo me-2"></i>Reset
                        </a>
                    </div>
                </div>
            </form>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-gradient-primary text-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>Daftar Aktivitas 
                        <span class="badge bg-light text-dark ms-2">{{ $logs->total() }} log</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="60" class="text-center">#</th>
                                    <th width="140">Waktu</th>
                                    <th width="200">User</th>
                                    <th width="100" class="text-center">Aksi</th>
                                    <th width="150">Model</th>
                                    <th>Deskripsi</th>
                                    <th width="80" class="text-center">Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                <tr>
                                    <td class="text-center fw-semibold">{{ $loop->iteration + ($logs->currentPage() - 1) * $logs->perPage() }}</td>
                                    <td>
                                        <div class="small">
                                            <i class="far fa-calendar me-1 text-primary"></i>{{ $log->created_at->format('d M Y') }}<br>
                                            <i class="far fa-clock me-1 text-muted"></i>{{ $log->created_at->format('H:i:s') }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 14px;">
                                                {{ strtoupper(substr($log->user ? $log->user->name : 'S', 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-semibold small">{{ $log->user ? $log->user->name : 'System' }}</div>
                                                <div class="text-muted" style="font-size: 0.75rem;">{{ $log->user ? $log->user->email : '-' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if($log->action === 'created')
                                            <span class="badge bg-success"><i class="fas fa-plus-circle me-1"></i>Created</span>
                                        @elseif($log->action === 'updated')
                                            <span class="badge bg-warning text-dark"><i class="fas fa-edit me-1"></i>Updated</span>
                                        @else
                                            <span class="badge bg-danger"><i class="fas fa-trash-alt me-1"></i>Deleted</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ class_basename($log->model_type) }}</span>
                                        <div class="text-muted small">ID: {{ $log->model_id }}</div>
                                    </td>
                                    <td class="small">{{ Str::limit($log->description, 80) }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('activity-logs.show', $log->id) }}" class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                        <p class="text-muted mb-0">Tidak ada log aktivitas</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($logs->hasPages())
                <div class="card-footer bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Menampilkan {{ $logs->firstItem() }} - {{ $logs->lastItem() }} dari {{ $logs->total() }} log
                        </div>
                        <div>
                            {{ $logs->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Fix bootstrap-select dropdown width */
    .filter-user .bootstrap-select,
    .filter-action .bootstrap-select {
        width: 300px !important;
    }
    
    .filter-user .dropdown-toggle,
    .filter-action .dropdown-toggle {
        width: 100% !important;
    }
</style>
@endpush

@endsection
