<?php

namespace App\Http\Controllers\Set;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:log-activity.index')->only('index', 'list');
        $this->middleware('permission:log-activity.create')->only('create', 'store');
        $this->middleware('permission:log-activity.edit')->only('edit', 'update');
        $this->middleware('permission:log-activity.destroy')->only('destroy');
    }
    public function list(Request $request)
    {
        if ($request->ajax()) {
            $activityLog = ActivityLog::with('user')->get();
            return DataTables::of($activityLog)
                ->addIndexColumn()
                ->editColumn('created_at', function ($log) {
                    return $log->created_at->format('d M Y H:i'); // Contoh: 23 Nov 2025 13:07
                })
                ->make(true);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('activity-logs.index');
    }

    public function show(string $id)
    {
        $activityLog = ActivityLog::with('user')->findOrFail($id);
        return response()->json([
            'status' => 'success',
            'data' => $activityLog
        ]);
    }
}
