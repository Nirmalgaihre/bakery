<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActivityLogController extends Controller
{
    /**
     * Display structural system logs stream.
     */
    public function index(Request $request)
    {
        // Uses default spatie activity_log structure or standard systems audit mapping
        $query = DB::table('activity_log')
            ->leftJoin('users', 'activity_log.causer_id', '=', 'users.id')
            ->select('activity_log.*', 'users.name as operator_name');

        if ($request->filled('type')) {
            $query->where('activity_log.log_name', $request->type);
        }

        $logs = $query->orderBy('activity_log.created_at', 'DESC')->paginate(25);

        return view('admin.logs.index', compact('logs'));
    }

    /**
     * Inspect single track record block metrics.
     */
    public function show($id)
    {
        $log = DB::table('activity_log')
            ->leftJoin('users', 'activity_log.causer_id', '=', 'users.id')
            ->select('activity_log.*', 'users.name as operator_name', 'users.email as operator_email')
            ->where('activity_log.id', $id)
            ->first();

        if (!$log) {
            abort(404, 'System trail audit record not found.');
        }

        return view('admin.logs.show', compact('log'));
    }
}