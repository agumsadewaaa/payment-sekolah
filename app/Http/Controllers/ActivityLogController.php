<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function __construct()
    {
        // Only super-admin can access
        $this->middleware('role:super-admin');
    }

    /**
     * Display a listing of activity logs.
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->orderBy('created_at', 'desc');

        // Debug: show raw count
        $totalLogs = ActivityLog::count();
        \Log::info('Total Activity Logs in DB: ' . $totalLogs);

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by model
        if ($request->filled('model_type')) {
            $query->where('model_type', 'like', '%' . $request->model_type . '%');
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(20);

        // Debug: check if logs exist
        \Log::info('Activity Logs Count: ' . $logs->total());
        \Log::info('Current Page: ' . $logs->currentPage());

        // Get unique users for filter
        $users = \App\Models\User::orderBy('name')->get();

        return view('activity_logs.index', compact('logs', 'users'));
    }

    /**
     * Display the specified activity log.
     */
    public function show($id)
    {
        $log = ActivityLog::with('user')->findOrFail($id);
        return view('activity_logs.show', compact('log'));
    }
}
