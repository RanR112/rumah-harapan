<?php

namespace App\Services;

use App\Models\AnakAsuh;
use App\Models\RumahHarapan;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Rap2hpoutre\FastExcel\FastExcel;

/**
 * ImportDataAnakAsuh handles importing foster child data from Excel or CSV files.
 * Validates each row and reports errors without stopping the entire process.
 */
class ImportDataAnakAsuh
{
    protected User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Process an uploaded file and import valid records.
     *
     * @param string $filePath Absolute path to the uploaded file.
     * @return array Contains success count and list of errors.
     */
    public function execute(string $filePath): array
    {
        $errors = [];
        $successCount = 0;

        try {
            $rows = (new FastExcel)->import($filePath, function ($row) use (&$errors, &$successCount) {
                try {
                    // Validate required headers
                    $required = ['RH', 'NAMA_ANAK', 'NIK', 'NO_KARTU_KELUARGA', 'JENIS_KEL', 'TANGGAL_LAHIR', 'STATUS', 'GRADE', 'NAMA_ORANG_TUA', 'TANGGAL_MASUK_RH'];
                    foreach ($required as $field) {
                        if (!isset($row[$field]) || trim((string) $row[$field]) === '') {
                            throw new \Exception("Kolom '{$field}' wajib diisi");
                        }
                    }

                    // Normalize values
                    $rhKode = trim((string) $row['RH']);
                    $nik = trim((string) $row['NIK']);
                    $jenisKel = strtoupper(trim((string) $row['JENIS_KEL']));
                    $status = strtolower(trim((string) $row['STATUS']));
                    $grade = strtoupper(trim((string) $row['GRADE']));

                    // Validate RH
                    $rh = RumahHarapan::where('kode', $rhKode)->first();
                    if (!$rh) {
                        throw new \Exception("Kode RH '{$rhKode}' tidak ditemukan");
                    }

                    // Validate enums
                    if (!in_array($jenisKel, ['L', 'P'])) {
                        throw new \Exception("JENIS_KEL harus L atau P");
                    }
                    if (!in_array($status, ['aktif', 'tidak aktif'])) {
                        throw new \Exception("STATUS harus 'aktif' atau 'tidak aktif'");
                    }
                    if (!in_array($grade, ['A', 'B', 'C', 'D', 'E'])) {
                        throw new \Exception("GRADE harus A, B, C, D, atau E");
                    }

                    // Validate NIK uniqueness
                    if (AnakAsuh::where('nik', $nik)->exists()) {
                        throw new \Exception("NIK sudah terdaftar");
                    }

                    // Validate date format (YYYY-MM-DD)
                    $this->validateDate($row['TANGGAL_LAHIR']);
                    $this->validateDate($row['TANGGAL_MASUK_RH']);

                    // Create record
                    AnakAsuh::create([
                        'rumah_harapan_id' => $rh->id,
                        'nama_anak' => trim((string) $row['NAMA_ANAK']),
                        'nik' => $nik,
                        'no_kartu_keluarga' => trim((string) $row['NO_KARTU_KELUARGA']),
                        'alamat_lengkap' => $row['ALAMAT_LENGKAP'] ?? '',
                        'jenis_kel' => $jenisKel,
                        'tempat_lahir' => $row['TEMPAT_LAHIR'] ?? '',
                        'tanggal_lahir' => $row['TANGGAL_LAHIR'],
                        'status' => $status,
                        'grade' => $grade,
                        'pendidikan_kelas' => $row['PENDIDIKAN_KELAS'] ?? null,
                        'nama_orang_tua' => trim((string) $row['NAMA_ORANG_TUA']),
                        'no_handphone' => $row['NO_HANDPHONE'] ?? null,
                        'tanggal_masuk_rh' => $row['TANGGAL_MASUK_RH'],
                        'yang_mengasuh_sebelum_diasrama' => $row['YANG_MENGASUH_SEBELUM_DIASRAMA'] ?? null,
                        'rekomendasi' => $row['REKOMENDASI'] ?? null,
                        'created_by' => $this->user->id,
                        'updated_by' => $this->user->id,
                    ]);

                    $successCount++;
                } catch (\Exception $e) {
                    $errors[] = [
                        'row' => count($errors) + 2, // +2 because header is row 1
                        'message' => $e->getMessage(),
                    ];
                }

                return null; // We don't need to return model
            });
        } catch (\Exception $e) {
            // File-level error (e.g., invalid format)
            return [
                'success_count' => 0,
                'errors' => [['row' => 0, 'message' => 'Gagal membaca file: ' . $e->getMessage()]],
            ];
        }

        return compact('successCount', 'errors');
    }

    /**
     * Validate if a string is in YYYY-MM-DD format.
     *
     * @param string $date
     * @return void
     * @throws \Exception
     */
    private function validateDate(string $date): void
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            throw new \Exception("Format tanggal harus YYYY-MM-DD, diterima: " . $date);
        }

        if (!strtotime($date)) {
            throw new \Exception("Tanggal tidak valid: " . $date);
        }
    }
}
