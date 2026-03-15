<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        // Handle AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            $filters = [
                'search' => $request->input('search'),
                'model_type' => $request->input('model_type'),
                'action' => $request->input('action'),
            ];

            $perPage = $request->input('per_page', 7);
            $page = $request->input('page', 1);

            $logs = $this->getPaginatedLogs($filters, $perPage);

            $rawData = [];
            foreach ($logs->items() as $log) {
                $rawData[] = [
                    'id' => $log->id,
                    'user_name' => $log->user?->name ?? 'N/A',
                    'model_type' => $log->human_readable_model,
                    'action' => $log->human_readable_action,
                    'created_at' => $log->created_at->format('d/m/Y H:i:s'),
                    'detail_url' => route('audit-log.show', $log->id),
                ];
            }

            return response()->json([
                'data' => $rawData,
                'current_page' => $logs->currentPage(),
                'last_page' => $logs->lastPage(),
                'total' => $logs->total(),
                'per_page' => $logs->perPage(),
                'first_item' => $logs->firstItem(),
            ]);
        }

        // Traditional request - Dapatkan data dinamis dari database
        $filters = [
            'search' => $request->input('search'),
            'model_type' => $request->input('model_type'),
            'action' => $request->input('action'),
        ];

        // Ambil model_type yang benar-benar ada di database (tanpa CustomActivity)
        $modelTypes = AuditLog::select('model_type')
            ->distinct()
            ->whereNotNull('model_type')
            ->where('model_type', '!=', '')
            ->orderBy('model_type')
            ->pluck('model_type')
            ->map(function ($type) {
                $dummyLog = new AuditLog(['model_type' => $type]);
                return [
                    'value' => $type,
                    'label' => $dummyLog->human_readable_model,
                ];
            })
            ->sortBy('label')
            ->values()
            ->all();

        // Bangun modelActionMap: model_type => array actions yang valid
        $modelActionMap = [];
        foreach ($modelTypes as $model) {
            if (empty($model['value'])) continue;
            
            $actions = AuditLog::where('model_type', $model['value'])
                ->distinct('action')
                ->pluck('action')
                ->filter(fn($action) => !empty($action) && $action !== 'custom')
                ->map(function ($action) {
                    $dummyLog = new AuditLog(['action' => $action]);
                    return [
                        'value' => $action,
                        'label' => $dummyLog->human_readable_action,
                    ];
                })
                ->sortBy('label')
                ->values()
                ->all();

            $modelActionMap[$model['value']] = $actions;
        }

        // Tambahkan opsi "Semua Model"
        array_unshift($modelTypes, ['value' => '', 'label' => 'Semua Model']);

        // Ambil semua action yang ada (tanpa hardcode 'custom')
        $allActions = AuditLog::select('action')
            ->distinct()
            ->whereNotNull('action')
            ->where('action', '!=', '')
            ->where('action', '!=', 'custom')
            ->orderBy('action')
            ->pluck('action')
            ->map(function ($action) {
                $dummyLog = new AuditLog(['action' => $action]);
                return [
                    'value' => $action,
                    'label' => $dummyLog->human_readable_action,
                ];
            })
            ->sortBy('label')
            ->values()
            ->all();

        array_unshift($allActions, ['value' => '', 'label' => 'Semua Aksi']);

        return view('pages.audit-log.index', compact('filters', 'modelTypes', 'allActions', 'modelActionMap'));
    }

    public function show(int $id)
    {
        $log = AuditLog::with('user')->findOrFail($id);
        return view('pages.audit-log.show', compact('log'));
    }

    private function getPaginatedLogs(array $filters, int $perPage = 20)
    {
        $query = AuditLog::with('user')->orderBy('created_at', 'desc');

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->whereHas('user', fn($u) => $u->where('name', 'like', "%{$filters['search']}%"))
                    ->orWhere('model_type', 'like', "%{$filters['search']}%")
                    ->orWhere('action', 'like', "%{$filters['search']}%");
            });
        }

        // Exact match untuk filter (bukan like)
        if (!empty($filters['model_type'])) {
            $query->where('model_type', $filters['model_type']);
        }

        if (!empty($filters['action'])) {
            $query->where('action', $filters['action']);
        }

        return $query->paginate($perPage);
    }
}
