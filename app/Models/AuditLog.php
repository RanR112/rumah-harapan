<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\AnakAsuh;
use App\Models\RumahHarapan;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'model_type',
        'model_id',
        'action',
        'old_values',
        'new_values',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Format data untuk ditampilkan di view dengan proteksi field sensitif
     */
    public function getFormattedChangesAttribute(): array
    {
        $changes = [];

        // Helper: ambil nama relasi
        $getRelatedName = function ($field, $value) {
            if ($value === null || $value === '') return $value;

            switch ($field) {
                case 'anak_asuh_id':
                    $anakAsuh = AnakAsuh::find($value);
                    return $anakAsuh ? $anakAsuh->nama_anak : 'N/A';

                case 'rumah_harapan_id':
                    // buildAuditData() sudah menyimpan nama asrama (bukan ID)
                    if (!is_numeric($value)) return $value;
                    $rh = RumahHarapan::find($value);
                    return $rh ? $rh->nama : 'N/A';

                default:
                    return $value;
            }
        };

        /**
         * Helper: format nilai foto untuk ditampilkan.
         *
         * - Jika ada path (storage/...) → tampilkan nama file saja (basename)
         * - Jika null/kosong           → kembalikan null (akan tampil sebagai '-')
         */
        $getFotoDisplay = function ($value): ?string {
            if (!empty($value) && strpos($value, 'storage/') !== false) {
                return basename($value);
            }
            return null;
        };

        if ($this->action === 'created') {
            foreach ($this->new_values as $field => $value) {
                // foto_path ditangani terpisah sebelum isSensitiveField
                if ($field === 'foto_path') {
                    $changes[] = [
                        'field' => 'Foto',
                        'old'   => '-',
                        'new'   => $getFotoDisplay($value) ?? '-',
                    ];
                    continue;
                }

                if ($this->isSensitiveField($field)) continue;

                $formattedValue = $getRelatedName($field, $value);
                $changes[] = [
                    'field' => $this->getHumanReadableField($field),
                    'old'   => '-',
                    'new'   => $this->formatValue($field, $formattedValue),
                ];
            }
        } elseif ($this->action === 'updated') {
            $old = $this->old_values ?? [];
            $new = $this->new_values ?? [];

            foreach ($new as $field => $newValue) {
                // foto_path ditangani terpisah sebelum isSensitiveField
                if ($field === 'foto_path') {
                    $oldValue    = $old[$field] ?? null;
                    $displayOld  = $getFotoDisplay($oldValue) ?? '-';
                    $displayNew  = $getFotoDisplay($newValue) ?? '-';

                    // Hanya tampilkan jika ada perubahan
                    if ($displayOld !== $displayNew) {
                        $changes[] = [
                            'field' => 'Foto',
                            'old'   => $displayOld,
                            'new'   => $displayNew,
                        ];
                    }
                    continue;
                }

                if ($this->isSensitiveField($field)) continue;

                $oldValue = $old[$field] ?? null;

                if ($oldValue !== $newValue || $this->isPasswordField($field)) {
                    if ($this->isPasswordField($field)) {
                        $changes[] = [
                            'field'              => 'Kata Sandi',
                            'old'                => '********',
                            'new'                => '******** (Diubah)',
                            'is_password_change' => true,
                        ];
                    } else {
                        $changes[] = [
                            'field' => $this->getHumanReadableField($field),
                            'old'   => $this->formatValue($field, $getRelatedName($field, $oldValue)),
                            'new'   => $this->formatValue($field, $getRelatedName($field, $newValue)),
                        ];
                    }
                }
            }
        } elseif ($this->action === 'deleted') {
            foreach ($this->old_values as $field => $value) {
                // foto_path ditangani terpisah sebelum isSensitiveField
                if ($field === 'foto_path') {
                    $changes[] = [
                        'field' => 'Foto',
                        'old'   => $getFotoDisplay($value) ?? '-',
                        'new'   => '-',
                    ];
                    continue;
                }

                if ($this->isSensitiveField($field)) continue;

                $changes[] = [
                    'field' => $this->getHumanReadableField($field),
                    'old'   => $this->formatValue($field, $value),
                    'new'   => '-',
                ];
            }
        }

        return $changes;
    }

    /**
     * Deteksi field sensitif yang tidak boleh ditampilkan.
     *
     * Catatan: foto_path TIDAK ada di sini — ditangani terpisah
     * di getFormattedChangesAttribute() agar bisa ditampilkan sebagai field 'Foto'.
     * foto_url tetap di sini karena tidak digunakan di audit log sama sekali.
     */
    private function isSensitiveField(string $field): bool
    {
        $sensitive = [
            // Field teknis
            'id',
            'uploaded_by',
            'current_password',
            'new_password',
            'password_confirmation',
            '_token',

            'password_changed',

            // Field metadata
            'created_by',
            'created_at',
            'updated_by',
            'updated_at',
            'deleted_at',

            // foto_url tidak dipakai di audit log (yang dipakai foto_path)
            'foto_url',

            // Field teknis user
            'reset_otp',
            'reset_otp_created_at',
            'email_verified_at',
            'remember_token',
        ];
        return in_array(strtolower($field), $sensitive);
    }

    /**
     * Deteksi field password untuk penanganan khusus
     */
    private function isPasswordField(string $field): bool
    {
        return in_array(strtolower($field), ['password', 'current_password', 'new_password']);
    }

    private function getHumanReadableField(string $field): string
    {
        $contextualLabels = [
            'App\Models\RumahHarapan' => [
                'email'       => 'Email Asrama',
                'nama'        => 'Nama Asrama',
                'telepon'     => 'Telepon Asrama',
                'kode'        => 'Kode Asrama',
                'koordinator' => 'Koordinator',
                'is_active'   => 'Status Aktif',
                'alamat'      => 'Alamat',
                'kota'        => 'Kota',
                'provinsi'    => 'Provinsi',
            ],
        ];

        if (
            isset($contextualLabels[$this->model_type]) &&
            isset($contextualLabels[$this->model_type][$field])
        ) {
            return $contextualLabels[$this->model_type][$field];
        }

        $labels = [
            // User / Profil fields
            'name'     => 'Nama',
            'email'    => 'Email',
            'phone'    => 'Telepon',
            'role'     => 'Role',
            'password' => 'Kata Sandi',

            // Profile fields
            'current_password' => 'Kata Sandi Saat Ini',
            'new_password'     => 'Kata Sandi Baru',

            // Anak Asuh fields
            'nama_anak'                      => 'Nama Anak',
            'nik'                            => 'NIK',
            'no_kartu_keluarga'              => 'No Kartu Keluarga',
            'jenis_kel'                      => 'Jenis Kelamin',
            'tanggal_lahir'                  => 'Tanggal Lahir',
            'no_handphone'                   => 'No Handphone',
            'status'                         => 'Status',
            'grade'                          => 'Grade',
            'nama_orang_tua'                 => 'Nama Orang Tua',
            'tanggal_masuk_rh'               => 'Tanggal Masuk RH',
            'yang_mengasuh_sebelum_diasrama' => 'Yang Mengasuh Sebelum Diasrama',
            'rh'                             => 'Asrama',
            'foto_path'                      => 'Foto',
            'alamat_lengkap'                 => 'Alamat Lengkap',
            'tempat_lahir'                   => 'Tempat Lahir',
            'rekomendasi'                    => 'Rekomendasi',
            'pendidikan_kelas'               => 'Pendidikan Kelas',
            'rumah_harapan_id'               => 'Nama Asrama',

            // Rumah Harapan fields
            'kode'        => 'Kode Asrama',
            'koordinator' => 'Koordinator',
            'is_active'   => 'Status Aktif',

            // Berkas Anak fields
            'file_path'     => 'File',
            'mime_type'     => 'Tipe File',
            'size_bytes'    => 'Ukuran File',
            'original_name' => 'Nama File',
            'anak_asuh_id'  => 'Nama Anak Asuh',

            // Authentication
            'login'  => 'Masuk',
            'logout' => 'Keluar',
        ];

        return $labels[$field] ?? $field;
    }

    private function formatValue(string $field, $value)
    {
        if ($value === null || $value === '') {
            return '-';
        }

        if ($field === 'size_bytes') {
            $mb = $value / (1024 * 1024);
            return number_format($mb, 2) . ' MB';
        }

        if ($field === 'is_active') {
            return $value ? 'Aktif' : 'Non-Aktif';
        }

        if ($field === 'role') {
            return $value === 'admin' ? 'Admin' : 'Petugas';
        }

        if ($field === 'jenis_kel') {
            return $value === 'L' ? 'Laki-laki' : 'Perempuan';
        }

        if ($field === 'status') {
            $statusLabels = [
                'yatim'       => 'Yatim',
                'piatu'       => 'Piatu',
                'yatim_piatu' => 'Yatim Piatu',
                'dhuafa'      => 'Dhuafa',
            ];
            return $statusLabels[$value] ?? ucfirst(str_replace('_', ' ', $value));
        }

        // Format field tanggal ke d/m/Y
        // Menggunakan array field name (bukan strpos 'Tanggal') agar match dengan key audit data
        $tanggalFields = ['tanggal_lahir', 'tanggal_masuk_rh', 'tanggal_keluar', 'created_at', 'updated_at'];
        if (in_array($field, $tanggalFields) && $value && $value !== '-') {
            try {
                return \Carbon\Carbon::parse($value)->format('d/m/Y');
            } catch (\Exception $e) {
                return $value;
            }
        }

        return $value;
    }

    /**
     * Kategori aktivitas untuk ikon di dashboard.
     * Dipetakan dari model_type ke kategori ikon yang tersedia di blade.
     */
    public function getCategoryAttribute(): string
    {
        $modelType = $this->model_type;
        $action    = $this->action;

        // Auth
        if ($action === 'login')  return 'login';
        if ($action === 'logout') return 'logout';

        // Import / Export
        if ($action === 'import') return 'import';
        if ($action === 'export') return 'export';

        // Reset Password
        if ($modelType === 'Reset Password') return 'reset_password';

        // Profil & Kata Sandi
        if (in_array($modelType, ['Profil', 'Kata Sandi Profil'])) return 'profil';

        // CRUD models
        if (str_contains($modelType, 'AnakAsuh') || str_contains($modelType, 'BerkasAnak')) return 'anak_asuh';
        if (str_contains($modelType, 'RumahHarapan')) return 'asrama';
        if (str_contains($modelType, 'User')) return 'user';

        return 'default';
    }

    /**
     * Deskripsi aktivitas untuk ditampilkan di dashboard.
     * Format: "<Nama User> <aksi> <model>"
     * Contoh: "Muhamad Fathurrohman menambahkan data Anak Asuh"
     */
    public function getDescriptionAttribute(): string
    {
        $userName  = $this->user?->name ?? 'Seseorang';
        $model     = $this->human_readable_model;
        $action    = $this->action;

        $kalimat = match ($action) {
            'created' => "<strong>{$userName}</strong> menambahkan data <strong>{$model}</strong>",
            'updated' => "<strong>{$userName}</strong> memperbarui data <strong>{$model}</strong>",
            'deleted' => "<strong>{$userName}</strong> menghapus data <strong>{$model}</strong>",
            'login'   => "<strong>{$userName}</strong> masuk ke sistem",
            'logout'  => "<strong>{$userName}</strong> keluar dari sistem",
            'import'  => "<strong>{$userName}</strong> mengimpor data <strong>{$model}</strong>",
            'export'  => "<strong>{$userName}</strong> mengekspor data <strong>{$model}</strong>",
            'request' => "<strong>{$userName}</strong> meminta reset kata sandi",
            'verify'  => "<strong>{$userName}</strong> memverifikasi kode OTP",
            'reset'   => "<strong>{$userName}</strong> mereset kata sandi",
            'invalid' => "<strong>{$userName}</strong> gagal verifikasi kode OTP",
            'change'  => "<strong>{$userName}</strong> mengubah kata sandi",
            default   => "<strong>{$userName}</strong> melakukan aktivitas <strong>{$model}</strong>",
        };

        return $kalimat;
    }

    public function getHumanReadableActionAttribute(): string
    {
        $actions = [
            'created' => 'Dibuat',
            'updated' => 'Diperbarui',
            'deleted' => 'Dihapus',
            'login'   => 'Masuk',
            'logout'  => 'Keluar',
            'import'  => 'Impor',
            'export'  => 'Ekspor',
            'request' => 'Request',
            'verify'  => 'Verifikasi',
            'reset'   => 'Reset',
            'invalid' => 'Gagal',
            'change'  => 'Diubah',
        ];

        return $actions[$this->action] ?? ucfirst($this->action);
    }

    public function getHumanReadableModelAttribute(): string
    {
        $models = [
            'App\Models\User'         => 'User',
            'App\Models\AnakAsuh'     => 'Anak Asuh',
            'App\Models\BerkasAnak'   => 'Berkas Anak',
            'App\Models\RumahHarapan' => 'Asrama',
            'Profil'                  => 'Profil',
            'Kata Sandi Profil'       => 'Kata Sandi Profil',
            'Import Data Anak Asuh'   => 'Import Data Anak Asuh',
            'Export Data Anak Asuh'   => 'Export Data Anak Asuh',
            'Reset Password'          => 'Reset Password',
            'Autentikasi'             => 'Autentikasi',
        ];

        return $models[$this->model_type] ?? $this->model_type;
    }
}
