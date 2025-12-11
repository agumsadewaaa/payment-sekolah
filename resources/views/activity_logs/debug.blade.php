@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1>Activity Log Debug</h1>
    
    <div class="card mb-3">
        <div class="card-header">Raw Data Test</div>
        <div class="card-body">
            <p>Total Logs in DB: <strong>{{ \App\Models\ActivityLog::count() }}</strong></p>
            <p>Total Users: <strong>{{ \App\Models\User::count() }}</strong></p>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Latest 10 Logs</div>
        <div class="card-body">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Model</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $logs = \App\Models\ActivityLog::with('user')->orderBy('created_at', 'desc')->limit(10)->get();
                    @endphp
                    @foreach($logs as $log)
                    <tr>
                        <td>{{ $log->id }}</td>
                        <td>{{ $log->user ? $log->user->name : 'N/A' }}</td>
                        <td>{{ $log->action }}</td>
                        <td>{{ class_basename($log->model_type) }}</td>
                        <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
