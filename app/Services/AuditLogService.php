<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AuditLogService
{
    public function logCrud(
        string $modelType,
        int $modelId,
        string $action,
        array $oldValues = [],
        array $newValues = []
    ): void {
        if (!Auth::check()) {
            return;
        }

        AuditLog::create([
            'user_id'    => Auth::id(),
            'model_type' => $modelType,
            'model_id'   => $modelId,
            'action'     => $action,
            'old_values' => $oldValues,
            'new_values' => $newValues,
        ]);
    }

    public function logAuth(string $action, array $additionalData = []): void
    {
        if (!Auth::check()) {
            return;
        }

        AuditLog::create([
            'user_id'    => Auth::id(),
            'model_type' => 'Autentikasi',
            'model_id'   => Auth::id(),
            'action'     => $action,
            'new_values' => $additionalData,
        ]);
    }

    /**
     * Mencatat aktivitas custom.
     *
     * Mendukung dua mode:
     * 1. User sudah login   → user_id dari Auth::id() (default)
     * 2. User belum login   → user_id dari parameter $user (untuk reset password, dll)
     *
     * Jika tidak ada user sama sekali (tidak login & $user null) → log diabaikan,
     * kecuali $allowAnonymous = true (untuk kasus log tanpa user seperti request OTP
     * yang emailnya tidak ditemukan di DB).
     *
     * @param string    $modelType
     * @param string    $action
     * @param array     $data
     * @param User|null $user           User eksplisit — untuk aktivitas unauthenticated
     * @param bool      $allowAnonymous Jika true, log tetap disimpan meski tidak ada user
     */
    public function logCustom(
        string $modelType,
        string $action,
        array $data = [],
        ?User $user = null,
        bool $allowAnonymous = false
    ): void {
        // Tentukan user_id
        if (Auth::check()) {
            $userId = Auth::id();
        } elseif ($user !== null) {
            $userId = $user->id;
        } elseif ($allowAnonymous) {
            $userId = null;
        } else {
            // Tidak ada user → abaikan log
            return;
        }

        AuditLog::create([
            'user_id'    => $userId,
            'model_type' => $modelType,
            'model_id'   => $userId ?? 0,
            'action'     => $action,
            'new_values' => $data,
        ]);
    }

    public function getPaginated(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = AuditLog::with('user')->orderBy('created_at', 'desc');

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->whereHas('user', fn($u) => $u->where('name', 'like', "%{$filters['search']}%"))
                    ->orWhere('model_type', 'like', "%{$filters['search']}%")
                    ->orWhere('action', 'like', "%{$filters['search']}%");
            });
        }

        if (!empty($filters['model_type'])) {
            $query->where('model_type', 'like', "%{$filters['model_type']}%");
        }

        if (!empty($filters['action'])) {
            $query->where('action', $filters['action']);
        }

        return $query->paginate($perPage);
    }

    public function findById(int $id): ?AuditLog
    {
        return AuditLog::with('user')->find($id);
    }
}
