@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1"><i class="fas fa-file-alt me-2 text-primary"></i>Detail Log Aktivitas</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="fas fa-home"></i> Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('activity-logs.index') }}">Activity Log</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('activity-logs.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2 text-primary"></i>Informasi Umum</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="200">Waktu</th>
                            <td>{{ $log->created_at->format('d M Y H:i:s') }} ({{ $log->created_at->diffForHumans() }})</td>
                        </tr>
                        <tr>
                            <th>User</th>
                            <td>
                                @if($log->user)
                                    <strong>{{ $log->user->name }}</strong> ({{ $log->user->email }})
                                    <br>
                                    <small class="text-muted">
                                        Role: 
                                        @foreach($log->user->roles as $role)
                                            <span class="badge bg-primary">{{ $role->name }}</span>
                                        @endforeach
                                    </small>
                                @else
                                    <span class="text-muted">System</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Aksi</th>
                            <td>
                                <span class="badge bg-{{ $log->action === 'created' ? 'success' : ($log->action === 'updated' ? 'warning' : 'danger') }}">
                                    {{ strtoupper($log->action) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Model Type</th>
                            <td>
                                <code>{{ $log->model_type }}</code>
                                <br><small class="text-muted">Class: {{ class_basename($log->model_type) }}</small>
                            </td>
                        </tr>
                        <tr>
                            <th>Model ID</th>
                            <td><strong>{{ $log->model_id }}</strong></td>
                        </tr>
                        <tr>
                            <th>Deskripsi</th>
                            <td>{{ $log->description }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            @if($log->old_values)
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="fas fa-history me-2 text-warning"></i>Nilai Lama</h5>
                </div>
                <div class="card-body">
                    <pre class="bg-light p-3 rounded"><code>{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                </div>
            </div>
            @endif

            @if($log->new_values)
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="fas fa-plus-circle me-2 text-success"></i>Nilai Baru</h5>
                </div>
                <div class="card-body">
                    <pre class="bg-light p-3 rounded"><code>{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2 text-primary"></i>Metadata</h5>
                </div>
                <div class="card-body">
                    <dl class="mb-0">
                        <dt>Log ID</dt>
                        <dd><code>#{{ $log->id }}</code></dd>

                        <dt>IP Address</dt>
                        <dd>{{ $log->ip_address ?? '-' }}</dd>

                        <dt>User Agent</dt>
                        <dd><small>{{ $log->user_agent ?? '-' }}</small></dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
