<?php

namespace App\Services;

use App\Models\RumahHarapan;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * RumahHarapanService handles business logic for asrama management.
 */
class RumahHarapanService
{
    protected $auditLogService;

    public function __construct(AuditLogService $auditLogService)
    {
        $this->auditLogService = $auditLogService;
    }

    /**
     * Create a new asrama.
     *
     * @param array $data
     * @param User $user
     * @return RumahHarapan
     */
    public function create(array $data, User $user): RumahHarapan
    {
        $rumahHarapan = RumahHarapan::create([
            'kode'        => strtoupper(trim($data['kode'])),
            'nama'        => $data['nama'],
            'alamat'      => $data['alamat'],
            'kota'        => $data['kota'],
            'provinsi'    => $data['provinsi'],
            'telepon'     => $data['telepon'] ?? null,
            'email'       => $data['email'] ?? null,
            'koordinator' => $data['koordinator'] ?? null,
            'is_active'   => true,
            'created_by'  => $user->id,
            'updated_by'  => $user->id,
        ]);

        // Log audit untuk create
        $this->auditLogService->logCrud(
            RumahHarapan::class,
            $rumahHarapan->id,
            'created',
            [],
            $data
        );

        return $rumahHarapan;
    }

    /**
     * Update an existing asrama.
     *
     * @param RumahHarapan $rumahHarapan
     * @param array $data
     * @param User $user
     * @return RumahHarapan
     */
    public function update(RumahHarapan $rumahHarapan, array $data, User $user): RumahHarapan
    {
        $oldValues = $rumahHarapan->getOriginal();

        $rumahHarapan->fill([
            'kode'        => isset($data['kode']) ? strtoupper(trim($data['kode'])) : $rumahHarapan->kode,
            'nama'        => $data['nama'] ?? $rumahHarapan->nama,
            'alamat'      => $data['alamat'] ?? $rumahHarapan->alamat,
            'kota'        => $data['kota'] ?? $rumahHarapan->kota,
            'provinsi'    => $data['provinsi'] ?? $rumahHarapan->provinsi,
            'telepon'     => $data['telepon'] ?? $rumahHarapan->telepon,
            'email'       => $data['email'] ?? $rumahHarapan->email,
            'koordinator' => $data['koordinator'] ?? $rumahHarapan->koordinator,
            'is_active'   => isset($data['is_active']) ? (bool) $data['is_active'] : $rumahHarapan->is_active,
            'updated_by'  => $user->id,
        ]);

        $rumahHarapan->save();

        // Log audit untuk update
        $this->auditLogService->logCrud(
            RumahHarapan::class,
            $rumahHarapan->id,
            'updated',
            $oldValues,
            $data
        );

        return $rumahHarapan;
    }

    // ========================================================================
    // SOFT DELETE & RESTORE - DIKOMENTARI UNTUK KEMUNGKINAN MASA DEPAN
    // ========================================================================
    /*
    public function delete(RumahHarapan $rumahHarapan): void
    {
        $rumahHarapan->delete();
    }

    public function restore(int $id): ?RumahHarapan
    {
        $rumahHarapan = RumahHarapan::withTrashed()->find($id);
        if ($rumahHarapan) {
            $rumahHarapan->restore();
        }
        return $rumahHarapan;
    }
    */

    /**
     * Permanently delete an asrama (hard delete).
     *
     * @param int $id
     * @return bool
     */
    public function hardDelete(int $id): bool
    {
        $rumahHarapan = RumahHarapan::find($id);
        if ($rumahHarapan) {
            $oldValues = $rumahHarapan->toArray();

            $result = $rumahHarapan->forceDelete();

            // Log audit untuk delete
            $this->auditLogService->logCrud(
                RumahHarapan::class,
                $id,
                'deleted',
                $oldValues,
                []
            );

            return $result;
        }
        return false;
    }

    /**
     * Get paginated asrama with optional filters.
     *
     * @param array $filters
     * @param int $perPage
     * @param bool $includeTrashed (TIDAK DIGUNAKAN karena tidak ada soft delete)
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getPaginated(array $filters, int $perPage = 15, bool $includeTrashed = false)
    {
        $query = RumahHarapan::query();

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('kode', 'like', "%{$filters['search']}%")
                    ->orWhere('nama', 'like', "%{$filters['search']}%");
            });
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        // TIDAK DIGUNAKAN karena tidak ada soft delete
        // if ($includeTrashed) {
        //     $query->withTrashed();
        // }

        // Urutkan berdasarkan created_at descending (terbaru di atas)
        $query->orderBy('created_at', 'desc');

        return $query->paginate($perPage);
    }

    /**
     * Find an asrama by ID.
     *
     * @param int $id
     * @param bool $withTrashed (TIDAK DIGUNAKAN karena tidak ada soft delete)
     * @return RumahHarapan
     * @throws ModelNotFoundException
     */
    public function findById(int $id, bool $withTrashed = false): RumahHarapan
    {
        $query = RumahHarapan::query();

        // TIDAK DIGUNAKAN karena tidak ada soft delete
        // if ($withTrashed) {
        //     $query->withTrashed();
        // }

        return $query->findOrFail($id);
    }

    /**
     * Get total count of asrama.
     *
     * @return int
     */
    public function getTotalCount(): int
    {
        return RumahHarapan::count();
    }

    // /**
    //  * Get total count of asrama.
    //  *
    //  * @param bool $includeTrashed (BELUM DIGUNAKAN karena tidak ada soft delete)
    //  * @return int
    //  */
    // public function getTotalCountWithTrashed(bool $includeTrashed = false): int
    // {
    //     $query = RumahHarapan::query();

    //     // TIDAK DIGUNAKAN karena tidak ada soft delete
    //     // if ($includeTrashed) {
    //     //     $query->withTrashed();
    //     // }

    //     return $query->count();
    // }
}
