<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')
            ->orderBy('created_at', 'desc');

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by model
        if ($request->filled('model')) {
            $query->where('model', $request->model);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search in description
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $logs = $query->paginate(20)->withQueryString();

        // Get unique values for filters
        $users = \App\Models\User::orderBy('name')->get();
        $actions = ActivityLog::distinct()->pluck('action');
        $models = ActivityLog::whereNotNull('model')->distinct()->pluck('model');

        return view('activity-logs.index', compact('logs', 'users', 'actions', 'models'));
    }

    public function show(ActivityLog $activityLog)
    {
        $activityLog->load('user');
        $modelInstance = $activityLog->model_instance;

        return view('activity-logs.show', compact('activityLog', 'modelInstance'));
    }

    public function destroy(ActivityLog $activityLog)
    {
        // Optional: Check permission
        // $this->authorize('delete', $activityLog);

        $activityLog->delete();

        return redirect()->route('activity-logs.index')
            ->with('success', 'Activity log deleted successfully');
    }

    public function statistics()
    {
        $stats = [
            'total_logs' => ActivityLog::count(),
            'logs_today' => ActivityLog::whereDate('created_at', today())->count(),
            'logs_this_week' => ActivityLog::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
            'logs_this_month' => ActivityLog::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        $userStats = ActivityLog::select('user_id', DB::raw('count(*) as total'))
            ->with('user')
            ->groupBy('user_id')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        $actionStats = ActivityLog::select('action', DB::raw('count(*) as total'))
            ->groupBy('action')
            ->orderBy('total', 'desc')
            ->get();

        $modelStats = ActivityLog::select('model', DB::raw('count(*) as total'))
            ->whereNotNull('model')
            ->groupBy('model')
            ->orderBy('total', 'desc')
            ->get();

        $dailyStats = ActivityLog::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('count(*) as total')
        )
            ->whereBetween('created_at', [now()->subDays(30), now()])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('activity-logs.statistics', compact(
            'stats',
            'userStats',
            'actionStats',
            'modelStats',
            'dailyStats'
        ));
    }
}