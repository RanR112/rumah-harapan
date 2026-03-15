<?php

namespace App\Services;

use App\Models\AnakAsuh;
use App\Models\BerkasAnak;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * BerkasAnakService handles file upload, storage, and management
 * for foster child documents.
 */
class BerkasAnakService
{
    protected $auditLogService;

    /**
     * Allowed file extensions.
     */
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'pdf'];

    /**
     * Maximum file size in bytes (5MB).
     * Disesuaikan dengan validasi di JavaScript (5MB).
     */
    private const MAX_SIZE = 5242880; // 5 * 1024 * 1024

    public function __construct(AuditLogService $auditLogService)
    {
        $this->auditLogService = $auditLogService;
    }

    /**
     * Upload a file and store its metadata.
     *
     * @param UploadedFile $file         The uploaded file.
     * @param AnakAsuh     $anakAsuh     The foster child record.
     * @param User         $user         The authenticated user.
     * @param string|null  $originalName Custom name from user (optional).
     *                                   If null, uses actual filename.
     * @return BerkasAnak
     * @throws \Exception If validation fails.
     */
    public function upload(
        UploadedFile $file,
        AnakAsuh $anakAsuh,
        User $user,
        ?string $originalName = null
    ): BerkasAnak {
        // Validate file
        $this->validateFile($file);

        // Generate unique filename untuk storage
        $extension = $file->getClientOriginalExtension();
        $filename  = now()->format('Ymd_His') . '_' . uniqid() . '.' . $extension;
        $directory = "berkas/anak_{$anakAsuh->id}";

        // Store file ke disk public
        $storedPath = $file->storeAs($directory, $filename, 'public');

        if (!$storedPath) {
            throw new \Exception('Gagal menyimpan file.');
        }

        // Gunakan nama custom dari user jika ada,
        // fallback ke nama file asli
        $displayName = $originalName
            ? trim($originalName)
            : $file->getClientOriginalName();

        // Save metadata ke database
        $berkas = BerkasAnak::create([
            'anak_asuh_id'  => $anakAsuh->id,
            'file_path'     => $storedPath,
            'original_name' => $displayName,
            'mime_type'     => $file->getMimeType(),
            'size_bytes'    => $file->getSize(),
            'uploaded_by'   => $user->id,
        ]);

        // Log audit untuk upload berkas
        $this->auditLogService->logCrud(
            BerkasAnak::class,
            $berkas->id,
            'created',
            [],
            [
                'anak_asuh_id' => $anakAsuh->id,
                'original_name' => $displayName,
                'file_path' => $storedPath,
                'mime_type' => $file->getMimeType(),
                'size_bytes' => $file->getSize(),
            ]
        );

        return $berkas;
    }

    /**
     * Delete a file record and remove the physical file.
     *
     * @param BerkasAnak $berkas
     * @return void
     */
    public function delete(BerkasAnak $berkas): void
    {
        // Simpan data lama untuk audit log
        $oldValues = $berkas->toArray();

        // Delete physical file dari storage
        if (Storage::disk('public')->exists($berkas->file_path)) {
            Storage::disk('public')->delete($berkas->file_path);
        }

        // Delete database record
        $berkas->delete();

        // Log audit untuk delete berkas
        $this->auditLogService->logCrud(
            BerkasAnak::class,
            $berkas->id,
            'deleted',
            $oldValues,
            []
        );
    }

    /**
     * Validate the uploaded file.
     *
     * @param UploadedFile $file
     * @return void
     * @throws \Exception
     */
    private function validateFile(UploadedFile $file): void
    {
        if (!$file->isValid()) {
            throw new \Exception('File tidak valid.');
        }

        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, self::ALLOWED_EXTENSIONS)) {
            throw new \Exception('Jenis file tidak diizinkan. Hanya JPG, JPEG, PNG, PDF.');
        }

        if ($file->getSize() > self::MAX_SIZE) {
            throw new \Exception('Ukuran file melebihi 5MB.');
        }

        $allowedMimeTypes = [
            'image/jpeg',
            'image/png',
            'application/pdf',
        ];

        if (!in_array($file->getMimeType(), $allowedMimeTypes)) {
            throw new \Exception('Tipe MIME tidak diizinkan.');
        }
    }
}
