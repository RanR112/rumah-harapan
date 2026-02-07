<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /**
     * Display a listing of audit logs.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $modelType = $request->input('model_type');
        $action = $request->input('action');

        $query = AuditLog::with('user')->orderBy('created_at', 'desc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"))
                    ->orWhere('model_type', 'like', "%{$search}%");
            });
        }

        if ($modelType) {
            $query->where('model_type', 'like', "%{$modelType}%");
        }

        if ($action) {
            $query->where('action', $action);
        }

        $logs = $query->paginate(20)->appends($request->query());

        // Daftar model yang umum
        $modelTypes = [
            'App\Models\User',
            'App\Models\AnakAsuh',
            'App\Models\RumahHarapan',
        ];

        // Daftar aksi
        $actions = ['created', 'updated', 'deleted', 'restored'];

        return view('audit-logs.index', compact('logs', 'modelTypes', 'actions'));
    }
}
