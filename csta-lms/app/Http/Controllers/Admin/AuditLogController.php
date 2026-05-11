<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->latest();

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($qq) use ($q) {
                $qq->where('action', 'like', "%$q%")
                   ->orWhere('description', 'like', "%$q%")
                   ->orWhere('ip_address', 'like', "%$q%")
                   ->orWhereHas('user', fn($u) => $u->where('full_name', 'like', "%$q%")
                       ->orWhere('id_number', 'like', "%$q%"));
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(25)->withQueryString();

        // For filter dropdowns
        $distinctActions = AuditLog::distinct()->orderBy('action')->pluck('action');

        return view('admin.audit-logs.index', compact('logs', 'distinctActions'));
    }

    public function export(Request $request)
    {
        $query = AuditLog::with('user')->latest();

        if ($request->filled('role'))     { $query->where('role', $request->role); }
        if ($request->filled('action'))   { $query->where('action', $request->action); }
        if ($request->filled('date_from')){ $query->whereDate('created_at', '>=', $request->date_from); }
        if ($request->filled('date_to'))  { $query->whereDate('created_at', '<=', $request->date_to); }

        $logs = $query->limit(5000)->get();

        $csvRows = [['#', 'User', 'ID Number', 'Role', 'Action', 'Description', 'IP Address', 'Date & Time']];
        foreach ($logs as $i => $log) {
            $csvRows[] = [
                $i + 1,
                $log->user->full_name ?? 'System',
                $log->user->id_number ?? '—',
                ucfirst($log->role ?? '—'),
                $log->action,
                $log->description ?? '—',
                $log->ip_address,
                $log->created_at->format('Y-m-d H:i:s'),
            ];
        }

        $filename = 'audit_logs_' . now()->format('Ymd_His') . '.csv';
        $handle   = fopen('php://temp', 'r+');
        foreach ($csvRows as $row) { fputcsv($handle, $row); }
        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return Response::make($content, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
