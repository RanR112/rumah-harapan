<?php

namespace App\Services;

use App\Models\AnakAsuh;
use App\Models\RumahHarapan;
use App\Models\User;
use Rap2hpoutre\FastExcel\FastExcel;

class ImportDataAnakAsuh
{
    protected $auditLogService;

    public function __construct(AuditLogService $auditLogService)
    {
        $this->auditLogService = $auditLogService;
    }

    private const COLUMN_ALIASES = [
        'JENIS_KELAMIN'                  => 'JENIS_KEL',
        'JENIS_KEL'                      => 'JENIS_KEL',
        'NO_KK'                          => 'NO_KARTU_KELUARGA',
        'NOMOR_KARTU_KELUARGA'           => 'NO_KARTU_KELUARGA',
        'NO_KARTU_KELUARGA'              => 'NO_KARTU_KELUARGA',
        'TGL_LAHIR'                      => 'TANGGAL_LAHIR',
        'TANGGAL_LAHIR'                  => 'TANGGAL_LAHIR',
        'TGL_MASUK_RH'                   => 'TANGGAL_MASUK_RH',
        'TANGGAL_MASUK_RH'               => 'TANGGAL_MASUK_RH',
        'NAMA_ORANG_TUA_WALI'            => 'NAMA_ORANG_TUA',
        'NAMA_WALI'                      => 'NAMA_ORANG_TUA',
        'NAMA_ORANG_TUA'                 => 'NAMA_ORANG_TUA',
        'YANG_MENGASUH'                      => 'YANG_MENGASUH_SEBELUM_DIASRAMA',
        'PENGASUH_SEBELUMNYA'                => 'YANG_MENGASUH_SEBELUM_DIASRAMA',
        'YANG_MENGASUH_SEBELUM_DIASRAMA'     => 'YANG_MENGASUH_SEBELUM_DIASRAMA',
        'YANG_MENGASUH_SEBELUM_DI_ASRAMA'    => 'YANG_MENGASUH_SEBELUM_DIASRAMA',
        'RH'           => 'RH',
        'KODE_ASRAMA'  => 'RH',
        'ASRAMA'       => 'RH',
        'NAMA_ANAK'    => 'NAMA_ANAK',
        'NAMA'         => 'NAMA_ANAK',
        'NIK'              => 'NIK',
        'STATUS'           => 'STATUS',
        'GRADE'            => 'GRADE',
        'ALAMAT_LENGKAP'   => 'ALAMAT_LENGKAP',
        'TEMPAT_LAHIR'     => 'TEMPAT_LAHIR',
        'PENDIDIKAN_KELAS' => 'PENDIDIKAN_KELAS',
        'NO_HANDPHONE'     => 'NO_HANDPHONE',
        'NO_HP'            => 'NO_HANDPHONE',
        'REKOMENDASI'      => 'REKOMENDASI',
    ];

    private function normalizeKey(string $key): string
    {
        $normalized = strtoupper(str_replace(' ', '_', trim($key)));
        return self::COLUMN_ALIASES[$normalized] ?? $normalized;
    }

    /**
     * Normalisasi nilai tanggal dari berbagai format ke Y-m-d.
     *
     * Format yang didukung:
     * - DateTimeImmutable / DateTime (dari FastExcel/OpenSpout)
     * - Y-m-d  → "2005-01-15"      (format utama)
     * - d-m-Y  → "15-01-2005"
     * - d/m/Y  → "15/01/2005"
     * - m-d-Y  → "01-15-2005"
     * - m/d/Y  → "01/15/2005"
     * - d-m-y  → "15-01-05"
     * - m/d/y  → "01/15/05"
     *
     * @throws \Exception jika format tidak dikenali
     */
    private function normalizeDateValue(mixed $value): string
    {
        if ($value instanceof \DateTimeImmutable || $value instanceof \DateTime) {
            return $value->format('Y-m-d');
        }

        $raw = trim((string) $value);

        if ($raw === '') {
            throw new \Exception("Nilai tanggal tidak boleh kosong");
        }

        $formats = [
            'Y-m-d',
            'd-m-Y',
            'd/m/Y',
            'm-d-Y',
            'm/d/Y',
            'd-m-y',
            'm/d/y',
            'Y/m/d',
        ];

        foreach ($formats as $format) {
            $dt = \DateTime::createFromFormat($format, $raw);
            if ($dt && $dt->format($format) === $raw) {
                return $dt->format('Y-m-d');
            }
        }

        $timestamp = strtotime($raw);
        if ($timestamp !== false) {
            return date('Y-m-d', $timestamp);
        }

        throw new \Exception(
            "Format tanggal '{$raw}' tidak dikenali. Gunakan format YYYY-MM-DD (contoh: 2005-01-15)"
        );
    }

    public function execute(string $filePath, User $user, string $originalFilename = ''): array
    {
        $errors       = [];
        $successCount = 0;

        try {
            (new FastExcel)->import($filePath, function ($row) use (&$errors, &$successCount, $user) {
                try {
                    $normalized = [];
                    foreach ($row as $key => $value) {
                        $normalizedKey              = $this->normalizeKey((string) $key);
                        $normalized[$normalizedKey] = $value;
                    }

                    $required = [
                        'RH',
                        'NAMA_ANAK',
                        'NIK',
                        'NO_KARTU_KELUARGA',
                        'JENIS_KEL',
                        'TANGGAL_LAHIR',
                        'STATUS',
                        'GRADE',
                        'NAMA_ORANG_TUA',
                        'TANGGAL_MASUK_RH',
                    ];

                    foreach ($required as $field) {
                        if (!isset($normalized[$field]) || trim((string) $normalized[$field]) === '') {
                            $friendlyName = match ($field) {
                                'JENIS_KEL'         => 'Jenis Kelamin',
                                'NO_KARTU_KELUARGA' => 'No Kartu Keluarga',
                                'TANGGAL_MASUK_RH'  => 'Tanggal Masuk RH',
                                default             => ucwords(strtolower(str_replace('_', ' ', $field))),
                            };
                            throw new \Exception("Kolom '{$friendlyName}' wajib diisi");
                        }
                    }

                    $rhKode   = trim((string) $normalized['RH']);
                    $nik      = trim((string) $normalized['NIK']);
                    $jenisKel = strtoupper(trim((string) $normalized['JENIS_KEL']));
                    $status   = strtolower(str_replace(' ', '_', trim((string) $normalized['STATUS'])));
                    $grade    = strtoupper(trim((string) $normalized['GRADE']));

                    $tanggalLahir = $this->normalizeDateValue($normalized['TANGGAL_LAHIR']);
                    $tanggalMasuk = $this->normalizeDateValue($normalized['TANGGAL_MASUK_RH']);

                    $rh = RumahHarapan::where('kode', $rhKode)->first();
                    if (!$rh) {
                        throw new \Exception("Kode RH '{$rhKode}' tidak ditemukan");
                    }

                    if (!in_array($jenisKel, ['L', 'P'])) {
                        throw new \Exception("Jenis Kelamin harus 'L' atau 'P', diterima: '{$jenisKel}'");
                    }

                    $validStatus = array_keys(AnakAsuh::STATUS_OPTIONS);
                    if (!in_array($status, $validStatus)) {
                        $validLabel = implode(', ', $validStatus);
                        throw new \Exception("Status '{$status}' tidak valid. Harus salah satu dari: {$validLabel}");
                    }

                    if (!in_array($grade, ['A', 'B', 'C', 'D', 'E'])) {
                        throw new \Exception("Grade '{$grade}' tidak valid. Harus A, B, C, D, atau E");
                    }

                    if (AnakAsuh::where('nik', $nik)->exists()) {
                        throw new \Exception("NIK '{$nik}' sudah terdaftar di sistem");
                    }

                    AnakAsuh::create([
                        'rumah_harapan_id'               => $rh->id,
                        'nama_anak'                      => trim((string) $normalized['NAMA_ANAK']),
                        'nik'                            => $nik,
                        'no_kartu_keluarga'              => trim((string) $normalized['NO_KARTU_KELUARGA']),
                        'alamat_lengkap'                 => $normalized['ALAMAT_LENGKAP'] ?? '',
                        'jenis_kel'                      => $jenisKel,
                        'tempat_lahir'                   => $normalized['TEMPAT_LAHIR'] ?? '',
                        'tanggal_lahir'                  => $tanggalLahir,
                        'status'                         => $status,
                        'is_active'                      => true,
                        'grade'                          => $grade,
                        'pendidikan_kelas'               => $normalized['PENDIDIKAN_KELAS'] ?? null,
                        'nama_orang_tua'                 => trim((string) $normalized['NAMA_ORANG_TUA']),
                        'no_handphone'                   => $normalized['NO_HANDPHONE'] ?? null,
                        'tanggal_masuk_rh'               => $tanggalMasuk,
                        'yang_mengasuh_sebelum_diasrama' => $normalized['YANG_MENGASUH_SEBELUM_DIASRAMA'] ?? null,
                        'rekomendasi'                    => $normalized['REKOMENDASI'] ?? null,
                        'created_by'                     => $user->id,
                        'updated_by'                     => $user->id,
                    ]);

                    $successCount++;
                } catch (\Exception $e) {
                    $errors[] = [
                        'row'     => $successCount + count($errors) + 2,
                        'message' => $e->getMessage(),
                    ];
                }

                return null;
            });
        } catch (\Exception $e) {
            return [
                'successCount' => 0,
                'errors'       => [['row' => 0, 'message' => 'Gagal membaca file: ' . $e->getMessage()]],
            ];
        }

        $this->auditLogService->logCustom(
            'Impor Data Anak Asuh',
            'import',
            [
                'nama file'     => $originalFilename ?: basename($filePath),
                'data berhasil' => $successCount,
                'data gagal'    => count($errors),
                'total data'    => $successCount + count($errors),
            ]
        );

        return compact('successCount', 'errors');
    }
}
