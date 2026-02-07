<?php

namespace App\Services;

use App\Models\RumahHarapan;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * RumahHarapanService handles business logic for branch management.
 */
class RumahHarapanService
{
    /**
     * Create a new branch.
     *
     * @param array $data
     * @param User $user
     * @return RumahHarapan
     */
    public function create(array $data, User $user): RumahHarapan
    {
        return RumahHarapan::create([
            'kode' => strtoupper(trim($data['kode'])),
            'nama' => $data['nama'],
            'alamat' => $data['alamat'],
            'kota' => $data['kota'],
            'provinsi' => $data['provinsi'],
            'telepon' => $data['telepon'] ?? null,
            'email' => $data['email'] ?? null,
            'koordinator' => $data['koordinator'] ?? null,
            'is_active' => $data['is_active'] ?? true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
    }

    /**
     * Update an existing branch.
     *
     * @param RumahHarapan $branch
     * @param array $data
     * @param User $user
     * @return RumahHarapan
     */
    public function update(RumahHarapan $branch, array $data, User $user): RumahHarapan
    {
        $branch->fill([
            'kode' => isset($data['kode']) ? strtoupper(trim($data['kode'])) : $branch->kode,
            'nama' => $data['nama'] ?? $branch->nama,
            'alamat' => $data['alamat'] ?? $branch->alamat,
            'kota' => $data['kota'] ?? $branch->kota,
            'provinsi' => $data['provinsi'] ?? $branch->provinsi,
            'telepon' => $data['telepon'] ?? $branch->telepon,
            'email' => $data['email'] ?? $branch->email,
            'koordinator' => $data['koordinator'] ?? $branch->koordinator,
            'is_active' => $data['is_active'] ?? $branch->is_active,
            'updated_by' => $user->id,
        ]);

        $branch->save();
        return $branch;
    }

    /**
     * Soft delete a branch.
     *
     * @param RumahHarapan $branch
     * @return void
     */
    public function delete(RumahHarapan $branch): void
    {
        $branch->delete();
    }

    /**
     * Restore a soft-deleted branch.
     *
     * @param int $id
     * @return RumahHarapan|null
     */
    public function restore(int $id): ?RumahHarapan
    {
        $branch = RumahHarapan::withTrashed()->find($id);
        if ($branch) {
            $branch->restore();
        }
        return $branch;
    }

    /**
     * Permanently delete a branch (hard delete).
     *
     * @param int $id
     * @return bool
     */
    public function hardDelete(int $id): bool
    {
        $branch = RumahHarapan::withTrashed()->where('id', $id)->whereNotNull('deleted_at')->first();
        if ($branch) {
            return $branch->forceDelete();
        }
        return false;
    }

    /**
     * Get paginated branches.
     *
     * @param array $filters
     * @param int $perPage
     * @param bool $includeTrashed
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

        if ($includeTrashed) {
            $query->withTrashed();
        }

        return $query->paginate($perPage);
    }

    /**
     * Find a branch by ID.
     *
     * @param int $id
     * @param bool $withTrashed
     * @return RumahHarapan
     * @throws ModelNotFoundException
     */
    public function findById(int $id, bool $withTrashed = false): RumahHarapan
    {
        $query = RumahHarapan::query();

        if ($withTrashed) {
            $query->withTrashed();
        }

        return $query->findOrFail($id);
    }

    /**
     * Get total count of active branches.
     *
     * @return int
     */
    public function getTotalCount(): int
    {
        return RumahHarapan::count();
    }

    /**
     * Get total count of branches (including soft-deleted if specified).
     *
     * @param bool $includeTrashed
     * @return int
     */
    public function getTotalCountWithTrashed(bool $includeTrashed = false): int
    {
        $query = RumahHarapan::query();

        if ($includeTrashed) {
            $query->withTrashed();
        }

        return $query->count();
    }
}
