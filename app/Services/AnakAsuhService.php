<?php

namespace App\Services;

use App\Models\AnakAsuh;
use App\Models\RumahHarapan;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * AnakAsuhService handles business logic for foster anakAsuh management.
 * Includes creation, update, and listing with filters.
 * 
 * SOFT DELETE & RESTORE: DIKOMENTARI UNTUK KEMUNGKINAN MASA DEPAN
 */
class AnakAsuhService
{
    /**
     * Create a new foster anakAsuh record.
     *
     * @param array $data Validated input data.
     * @param User $user Authenticated user.
     * @return AnakAsuh
     * @throws \Exception If RH code is invalid.
     */
    public function create(array $data, User $user): AnakAsuh
    {
        $rh = RumahHarapan::where('kode', $data['rh'])->first();
        if (!$rh) {
            throw new \Exception("Kode cabang '{$data['rh']}' tidak ditemukan.");
        }

        return AnakAsuh::create([
            'rumah_harapan_id' => $rh->id,
            'nama_anak' => $data['nama_anak'],
            'nik' => $data['nik'],
            'no_kartu_keluarga' => $data['no_kartu_keluarga'],
            'alamat_lengkap' => $data['alamat_lengkap'] ?? '',
            'jenis_kel' => strtoupper($data['jenis_kel']),
            'tempat_lahir' => $data['tempat_lahir'] ?? '',
            'tanggal_lahir' => $data['tanggal_lahir'],
            'status' => strtolower($data['status']),
            'grade' => strtoupper($data['grade']),
            'pendidikan_kelas' => $data['pendidikan_kelas'] ?? null,
            'nama_orang_tua' => $data['nama_orang_tua'],
            'no_handphone' => $data['no_handphone'] ?? null,
            'tanggal_masuk_rh' => $data['tanggal_masuk_rh'],
            'yang_mengasuh_sebelum_diasrama' => $data['yang_mengasuh_sebelum_diasrama'] ?? null,
            'rekomendasi' => $data['rekomendasi'] ?? null,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
    }

    /**
     * Update an existing foster anakAsuh record.
     *
     * @param AnakAsuh $anakAsuh
     * @param array $data
     * @param User $user
     * @return AnakAsuh
     */
    public function update(AnakAsuh $anakAsuh, array $data, User $user): AnakAsuh
    {
        if (isset($data['rh'])) {
            $rh = RumahHarapan::where('kode', $data['rh'])->first();
            if (!$rh) {
                throw new \Exception("Kode cabang '{$data['rh']}' tidak ditemukan.");
            }
            $anakAsuh->rumah_harapan_id = $rh->id;
        }

        $anakAsuh->fill([
            'nama_anak' => $data['nama_anak'] ?? $anakAsuh->nama_anak,
            'nik' => $data['nik'] ?? $anakAsuh->nik,
            'no_kartu_keluarga' => $data['no_kartu_keluarga'] ?? $anakAsuh->no_kartu_keluarga,
            'alamat_lengkap' => $data['alamat_lengkap'] ?? $anakAsuh->alamat_lengkap,
            'jenis_kel' => isset($data['jenis_kel']) ? strtoupper($data['jenis_kel']) : $anakAsuh->jenis_kel,
            'tempat_lahir' => $data['tempat_lahir'] ?? $anakAsuh->tempat_lahir,
            'tanggal_lahir' => $data['tanggal_lahir'] ?? $anakAsuh->tanggal_lahir,
            'status' => isset($data['status']) ? strtolower($data['status']) : $anakAsuh->status,
            'grade' => isset($data['grade']) ? strtoupper($data['grade']) : $anakAsuh->grade,
            'pendidikan_kelas' => $data['pendidikan_kelas'] ?? $anakAsuh->pendidikan_kelas,
            'nama_orang_tua' => $data['nama_orang_tua'] ?? $anakAsuh->nama_orang_tua,
            'no_handphone' => $data['no_handphone'] ?? $anakAsuh->no_handphone,
            'tanggal_masuk_rh' => $data['tanggal_masuk_rh'] ?? $anakAsuh->tanggal_masuk_rh,
            'yang_mengasuh_sebelum_diasrama' => $data['yang_mengasuh_sebelum_diasrama'] ?? $anakAsuh->yang_mengasuh_sebelum_diasrama,
            'rekomendasi' => $data['rekomendasi'] ?? $anakAsuh->rekomendasi,
            'updated_by' => $user->id,
        ]);

        $anakAsuh->save();
        return $anakAsuh;
    }

    // ========================================================================
    // SOFT DELETE & RESTORE - DIKOMENTARI UNTUK KEMUNGKINAN MASA DEPAN
    // ========================================================================
    /*
    /**
     * Soft delete a foster anakAsuh.
     *
     * @param AnakAsuh $anakAsuh
     * @return void
     *\/
    public function delete(AnakAsuh $anakAsuh): void
    {
        $anakAsuh->delete();
    }

    /**
     * Restore a soft-deleted foster anakAsuh.
     *
     * @param int $id
     * @return AnakAsuh|null
     *\/
    public function restore(int $id): ?AnakAsuh
    {
        $anakAsuh = AnakAsuh::withTrashed()->find($id);
        if ($anakAsuh) {
            $anakAsuh->restore();
        }
        return $anakAsuh;
    }
    */

    /**
     * Permanently delete a foster child (hard delete).
     *
     * @param int $id
     * @return bool
     */
    public function hardDelete(int $id): bool
    {
        // Hapus langsung tanpa pengecekan soft delete
        $child = AnakAsuh::find($id);
        if ($child) {
            return $child->delete(); // Gunakan delete() biasa karena tidak ada soft delete
        }
        return false;
    }

    /**
     * Get paginated list of children with optional filters.
     *
     * @param array $filters
     * @param int $perPage
     * @param bool $includeTrashed (TIDAK DIGUNAKAN karena tidak ada soft delete)
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getPaginated(array $filters, int $perPage = 15, bool $includeTrashed = false)
    {
        $query = AnakAsuh::with('rumahHarapan');

        // Apply filters
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('nama_anak', 'like', "%{$filters['search']}%")
                    ->orWhere('nik', 'like', "%{$filters['search']}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['grade'])) {
            $query->where('grade', $filters['grade']);
        }

        if (!empty($filters['rh'])) {
            $rh = RumahHarapan::where('kode', $filters['rh'])->first();
            if ($rh) {
                $query->where('rumah_harapan_id', $rh->id);
            }
        }

        // TIDAK DIGUNAKAN karena tidak ada soft delete
        // if ($includeTrashed) {
        //     $query->withTrashed();
        // }

        return $query->paginate($perPage);
    }

    /**
     * Find a anakAsuh by ID.
     *
     * @param int $id
     * @param bool $withTrashed (TIDAK DIGUNAKAN karena tidak ada soft delete)
     * @return AnakAsuh
     * @throws ModelNotFoundException
     */
    public function findById(int $id, bool $withTrashed = false): AnakAsuh
    {
        $query = AnakAsuh::with('rumahHarapan');

        // TIDAK DIGUNAKAN karena tidak ada soft delete
        // if ($withTrashed) {
        //     $query->withTrashed();
        // }

        return $query->findOrFail($id);
    }

    /**
     * Get total count of active anak asuh.
     *
     * @return int
     */
    public function getTotalCount(): int
    {
        return AnakAsuh::count();
    }

    /**
     * Get total count of anak asuh.
     *
     * @param bool $includeTrashed (TIDAK DIGUNAKAN karena tidak ada soft delete)
     * @return int
     */
    public function getTotalCountWithTrashed(bool $includeTrashed = false): int
    {
        $query = AnakAsuh::query();

        // TIDAK DIGUNAKAN karena tidak ada soft delete
        // if ($includeTrashed) {
        //     $query->withTrashed();
        // }

        return $query->count();
    }
}