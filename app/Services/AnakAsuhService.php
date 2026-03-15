<?php

namespace App\Services;

use App\Models\AnakAsuh;
use App\Models\RumahHarapan;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AnakAsuhService
{
    protected $auditLogService;

    public function __construct(AuditLogService $auditLogService)
    {
        $this->auditLogService = $auditLogService;
    }

    public function create(array $data, User $user): AnakAsuh
    {
        $rh = RumahHarapan::where('kode', $data['rh'])->first();
        if (!$rh) {
            throw new \Exception("Kode asrama '{$data['rh']}' tidak ditemukan.");
        }

        $anakAsuh = AnakAsuh::create([
            'rumah_harapan_id'               => $rh->id,
            'nama_anak'                      => $data['nama_anak'],
            'nik'                            => $data['nik'],
            'no_kartu_keluarga'              => $data['no_kartu_keluarga'],
            'alamat_lengkap'                 => $data['alamat_lengkap'] ?? '',
            'jenis_kel'                      => strtoupper($data['jenis_kel']),
            'tempat_lahir'                   => $data['tempat_lahir'] ?? '',
            'tanggal_lahir'                  => $data['tanggal_lahir'],
            'status'                         => $data['status'],
            'is_active'                      => true,
            'grade'                          => strtoupper($data['grade']),
            'pendidikan_kelas'               => $data['pendidikan_kelas'] ?? null,
            'nama_orang_tua'                 => $data['nama_orang_tua'],
            'no_handphone'                   => $data['no_handphone'] ?? null,
            'tanggal_masuk_rh'               => $data['tanggal_masuk_rh'],
            'yang_mengasuh_sebelum_diasrama' => $data['yang_mengasuh_sebelum_diasrama'] ?? null,
            'rekomendasi'                    => $data['rekomendasi'] ?? null,
            'foto_path'                      => null,
            'created_by'                     => $user->id,
            'updated_by'                     => $user->id,
        ]);

        $this->auditLogService->logCrud(
            AnakAsuh::class,
            $anakAsuh->id,
            'created',
            [],
            $this->buildAuditData($data, $rh->id, null)
        );

        return $anakAsuh;
    }

    /**
     * Update anak asuh.
     *
     * $oldFotoPath dan $newFotoPath dikirim eksplisit dari controller
     * karena handleFotoUpload() sudah mengubah foto_path di DB sebelum
     * method ini dipanggil. Jika kita andalkan getOriginal() di sini,
     * nilainya sudah berubah dan audit log tidak akan mendeteksi perubahan foto.
     *
     * @param AnakAsuh    $anakAsuh
     * @param array       $data
     * @param User        $user
     * @param string|null $oldFotoPath  foto_path sebelum handleFotoUpload
     * @param string|null $newFotoPath  foto_path setelah handleFotoUpload
     */
    public function update(
        AnakAsuh $anakAsuh,
        array $data,
        User $user,
        ?string $oldFotoPath = null,
        ?string $newFotoPath = null
    ): AnakAsuh {
        // Simpan old values data (bukan foto) sebelum fill
        $oldValues         = $anakAsuh->getOriginal();
        $oldRumahHarapanId = $oldValues['rumah_harapan_id'] ?? null;

        if (isset($data['rh'])) {
            $rh = RumahHarapan::where('kode', $data['rh'])->first();
            if (!$rh) {
                throw new \Exception("Kode asrama '{$data['rh']}' tidak ditemukan.");
            }
            $anakAsuh->rumah_harapan_id = $rh->id;
        }

        $anakAsuh->fill([
            'nama_anak'                      => $data['nama_anak']                      ?? $anakAsuh->nama_anak,
            'nik'                            => $data['nik']                            ?? $anakAsuh->nik,
            'no_kartu_keluarga'              => $data['no_kartu_keluarga']              ?? $anakAsuh->no_kartu_keluarga,
            'alamat_lengkap'                 => $data['alamat_lengkap']                 ?? $anakAsuh->alamat_lengkap,
            'jenis_kel'                      => isset($data['jenis_kel']) ? strtoupper($data['jenis_kel']) : $anakAsuh->jenis_kel,
            'tempat_lahir'                   => $data['tempat_lahir']                   ?? $anakAsuh->tempat_lahir,
            'tanggal_lahir'                  => $data['tanggal_lahir']                  ?? $anakAsuh->tanggal_lahir,
            'status'                         => $data['status']                         ?? $anakAsuh->status,
            'is_active'                      => $data['is_active']                      ?? $anakAsuh->is_active,
            'grade'                          => isset($data['grade']) ? strtoupper($data['grade']) : $anakAsuh->grade,
            'pendidikan_kelas'               => $data['pendidikan_kelas']               ?? $anakAsuh->pendidikan_kelas,
            'nama_orang_tua'                 => $data['nama_orang_tua']                 ?? $anakAsuh->nama_orang_tua,
            'no_handphone'                   => $data['no_handphone']                   ?? $anakAsuh->no_handphone,
            'tanggal_masuk_rh'               => $data['tanggal_masuk_rh']               ?? $anakAsuh->tanggal_masuk_rh,
            'yang_mengasuh_sebelum_diasrama' => $data['yang_mengasuh_sebelum_diasrama'] ?? $anakAsuh->yang_mengasuh_sebelum_diasrama,
            'rekomendasi'                    => $data['rekomendasi']                    ?? $anakAsuh->rekomendasi,
            'updated_by'                     => $user->id,
        ]);

        $anakAsuh->save();

        // Audit log — foto menggunakan nilai eksplisit dari controller,
        // bukan dari model, karena foto sudah diproses sebelum method ini
        $this->auditLogService->logCrud(
            AnakAsuh::class,
            $anakAsuh->id,
            'updated',
            $this->buildAuditData($oldValues, $oldRumahHarapanId, $oldFotoPath),
            $this->buildAuditData($data, $anakAsuh->rumah_harapan_id, $newFotoPath)
        );

        return $anakAsuh;
    }

    public function hardDelete(int $id): bool
    {
        $child = AnakAsuh::find($id);
        if ($child) {
            $oldValues         = $child->toArray();
            $oldRumahHarapanId = $oldValues['rumah_harapan_id'] ?? null;
            $oldFotoPath       = $oldValues['foto_path'] ?? null;

            $result = $child->delete();

            $this->auditLogService->logCrud(
                AnakAsuh::class,
                $id,
                'deleted',
                $this->buildAuditData($oldValues, $oldRumahHarapanId, $oldFotoPath),
                []
            );

            return $result;
        }
        return false;
    }

    public function getPaginated(array $filters, int $perPage = 15)
    {
        $query = AnakAsuh::with('rumahHarapan')
            ->withCount('berkasAnak');

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('nama_anak', 'like', "%{$filters['search']}%")
                    ->orWhere('nik', 'like', "%{$filters['search']}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', (bool) $filters['is_active']);
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

        $query->orderBy('created_at', 'desc');

        return $query->paginate($perPage);
    }

    public function findById(int $id): AnakAsuh
    {
        return AnakAsuh::with('rumahHarapan')->findOrFail($id);
    }

    public function getTotalCount(): int
    {
        return AnakAsuh::count();
    }

    // /**
    //  * Get total count of anak-asuh.
    //  *
    //  * @param bool $includeTrashed (BELUM DIGUNAKAN karena tidak ada soft delete)
    //  * @return int
    //  */
    // public function getTotalCountWithTrashed(bool $includeTrashed = false): int
    // {
    //     $query = AnakAsuh::query();

    //     // TIDAK DIGUNAKAN karena tidak ada soft delete
    //     // if ($includeTrashed) {
    //     //     $query->withTrashed();
    //     // }

    //     return $query->count();
    // }

    // ========================================================================
    // PRIVATE HELPERS
    // ========================================================================

    /**
     * Build data audit yang sudah dinormalisasi untuk AnakAsuh.
     *
     * @param array       $source          Data mentah
     * @param int|null    $rumahHarapanId  ID RH → di-resolve ke nama
     * @param string|null $fotoPath        Path foto — selalu dari parameter eksplisit,
     *                                     bukan dari $source, agar nilainya akurat
     */
    private function buildAuditData(array $source, ?int $rumahHarapanId = null, ?string $fotoPath = null): array
    {
        $namaAsrama = null;
        if ($rumahHarapanId) {
            $rh         = RumahHarapan::find($rumahHarapanId);
            $namaAsrama = $rh ? $rh->nama : 'N/A';
        }

        $tanggalLahir = $this->normalizeTanggal($source['tanggal_lahir'] ?? null);
        $tanggalMasuk = $this->normalizeTanggal($source['tanggal_masuk_rh'] ?? null);

        // Format foto_path sebagai 'storage/...' agar getFotoDisplay() di AuditLog
        // bisa mengenalinya dan menampilkan basename sebagai nama file
        $fotoValue = $fotoPath ? 'storage/' . $fotoPath : null;

        return [
            'nama_anak'                      => $source['nama_anak']                      ?? null,
            'nik'                            => $source['nik']                            ?? null,
            'no_kartu_keluarga'              => $source['no_kartu_keluarga']              ?? null,
            'alamat_lengkap'                 => $source['alamat_lengkap']                 ?? null,
            'jenis_kel'                      => $source['jenis_kel']                      ?? null,
            'tempat_lahir'                   => $source['tempat_lahir']                   ?? null,
            'tanggal_lahir'                  => $tanggalLahir,
            'status'                         => $source['status']                         ?? null,
            'is_active'                      => isset($source['is_active']) ? (bool) $source['is_active'] : null,
            'grade'                          => $source['grade']                          ?? null,
            'pendidikan_kelas'               => $source['pendidikan_kelas']               ?? null,
            'nama_orang_tua'                 => $source['nama_orang_tua']                 ?? null,
            'no_handphone'                   => $source['no_handphone']                   ?? null,
            'tanggal_masuk_rh'               => $tanggalMasuk,
            'yang_mengasuh_sebelum_diasrama' => $source['yang_mengasuh_sebelum_diasrama'] ?? null,
            'rekomendasi'                    => $source['rekomendasi']                    ?? null,
            'rumah_harapan_id'               => $namaAsrama,
            'foto_path'                      => $fotoValue,
        ];
    }

    /**
     * Normalisasi nilai tanggal ke string Y-m-d.
     */
    private function normalizeTanggal(mixed $value): ?string
    {
        if ($value === null) return null;

        if (
            $value instanceof \Carbon\Carbon ||
            $value instanceof \DateTimeImmutable ||
            $value instanceof \DateTime
        ) {
            return $value->format('Y-m-d');
        }

        $str = trim((string) $value);
        if ($str === '') return null;

        if (preg_match('/^\d{4}-\d{2}-\d{2}/', $str)) {
            return substr($str, 0, 10);
        }

        $timestamp = strtotime($str);
        return $timestamp !== false ? date('Y-m-d', $timestamp) : $str;
    }
}
