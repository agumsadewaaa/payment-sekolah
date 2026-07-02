@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
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
        <div class="col-12">
            <div class="card shadow-sm border-0 mb-2">
                <div class="card-header bg-white border-bottom py-2">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2 text-primary"></i>Informasi Umum</h5>
                </div>
                <div class="card-body py-2">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-2">
                                <strong class="text-muted small">Waktu</strong>
                                <p class="mb-0 small">{{ $log->created_at->format('d M Y H:i:s') }} ({{ $log->created_at->diffForHumans() }})</p>
                            </div>
                            <div class="mb-0">
                                <strong class="text-muted small">Aksi</strong>
                                <p class="mb-0">
                                    <span class="badge bg-{{ $log->action === 'created' ? 'success' : ($log->action === 'updated' ? 'warning' : 'danger') }} small">
                                        <i class="fas fa-{{ $log->action === 'created' ? 'plus-circle' : ($log->action === 'updated' ? 'edit' : 'trash-alt') }} me-1"></i>{{ strtoupper($log->action) }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <strong class="text-muted small">User</strong>
                                <p class="mb-0 small">
                                    @if($log->user)
                                        {{ $log->user->name }} ({{ $log->user->email }})
                                    @else
                                        <span class="text-muted">System</span>
                                    @endif
                                </p>
                            </div>
                            <div class="mb-0">
                                <strong class="text-muted small">Model</strong>
                                <p class="mb-0 small">{{ class_basename($log->model_type) }} (ID: {{ $log->model_id }})</p>
                            </div>
                        </div>
                    </div>
                    <div class="row g-3 mt-0">
                        <div class="col-md-6">
                            <div class="mb-0">
                                <strong class="text-muted small">Durasi</strong>
                                <p class="mb-0 small">
                                    @if(!is_null($log->duration_ms))
                                        @php
                                            $dur = $log->duration_ms;
                                            $color = $dur < 100 ? 'secondary' : ($dur < 500 ? 'warning' : 'danger');
                                        @endphp
                                        <span class="badge bg-{{ $color }}">{{ $dur }} ms</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($log->action === 'created' && $log->new_values)
            <div class="card shadow-sm border-0 mb-2">
                <div class="card-header bg-white border-bottom py-2">
                    <h5 class="mb-0"><i class="fas fa-plus-circle me-2 text-success"></i>Nilai yang Dibuat</h5>
                </div>
                <div class="card-body py-2">
                    <pre class="bg-light p-2 rounded mb-0" style="font-size: 0.8rem; max-height: 300px; overflow-y: auto;"><code>{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                </div>
            </div>
            @elseif($log->action === 'updated')
                @if($log->old_values && $log->new_values)
                <div class="row g-2">
                    <div class="col-md-6">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white border-bottom py-2">
                                <h5 class="mb-0"><i class="fas fa-history me-2 text-warning"></i>Nilai Lama</h5>
                            </div>
                            <div class="card-body py-2">
                                <pre class="bg-light p-2 rounded mb-0" style="font-size: 0.8rem; max-height: 300px; overflow-y: auto;"><code>{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white border-bottom py-2">
                                <h5 class="mb-0"><i class="fas fa-edit me-2 text-info"></i>Nilai Baru</h5>
                            </div>
                            <div class="card-body py-2">
                                <pre class="bg-light p-2 rounded mb-0" style="font-size: 0.8rem; max-height: 300px; overflow-y: auto;"><code>{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                            </div>
                        </div>
                    </div>
                </div>
                @elseif($log->old_values)
                <div class="card shadow-sm border-0 mb-2">
                    <div class="card-header bg-white border-bottom py-2">
                        <h5 class="mb-0"><i class="fas fa-history me-2 text-warning"></i>Nilai Lama</h5>
                    </div>
                    <div class="card-body py-2">
                        <pre class="bg-light p-2 rounded mb-0" style="font-size: 0.8rem; max-height: 300px; overflow-y: auto;"><code>{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                    </div>
                </div>
                @elseif($log->new_values)
                <div class="card shadow-sm border-0 mb-2">
                    <div class="card-header bg-white border-bottom py-2">
                        <h5 class="mb-0"><i class="fas fa-edit me-2 text-info"></i>Nilai Baru</h5>
                    </div>
                    <div class="card-body py-2">
                        <pre class="bg-light p-2 rounded mb-0" style="font-size: 0.8rem; max-height: 300px; overflow-y: auto;"><code>{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                    </div>
                </div>
                @endif
            @elseif($log->action === 'deleted' && $log->old_values)
            <div class="card shadow-sm border-0 mb-2">
                <div class="card-header bg-white border-bottom py-2">
                    <h5 class="mb-0"><i class="fas fa-trash-alt me-2 text-danger"></i>Nilai yang Dihapus</h5>
                </div>
                <div class="card-body py-2">
                    <pre class="bg-light p-2 rounded mb-0" style="font-size: 0.8rem; max-height: 300px; overflow-y: auto;"><code>{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
