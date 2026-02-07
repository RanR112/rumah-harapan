<?php

namespace App\Services;

use App\Models\AnakAsuh;
use App\Models\BerkasAnak;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * BerkasAnakService handles file upload, storage, and management for foster child documents.
 */
class BerkasAnakService
{
    /**
     * Allowed file extensions.
     */
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'pdf'];

    /**
     * Maximum file size in bytes (2MB).
     */
    private const MAX_SIZE = 2097152; // 2 * 1024 * 1024

    /**
     * Upload a file and store its metadata.
     *
     * @param UploadedFile $file The uploaded file.
     * @param AnakAsuh $anakAsuh The foster child record.
     * @param User $user The authenticated user.
     * @return BerkasAnak
     * @throws \Exception If validation fails.
     */
    public function upload(UploadedFile $file, AnakAsuh $anakAsuh, User $user): BerkasAnak
    {
        // Validate file
        $this->validateFile($file);

        // Generate path
        $extension = $file->getClientOriginalExtension();
        $filename = now()->format('Ymd_His') . '_' . uniqid() . '.' . $extension;
        $path = "berkas/anak_{$anakAsuh->id}/{$filename}";

        // Store file
        $storedPath = $file->storeAs(
            dirname($path),
            basename($path),
            'public'
        );

        if (!$storedPath) {
            throw new \Exception('Gagal menyimpan file.');
        }

        // Save metadata
        return BerkasAnak::create([
            'anak_asuh_id' => $anakAsuh->id,
            'file_path' => $storedPath,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size_bytes' => $file->getSize(),
            'uploaded_by' => $user->id,
        ]);
    }

    /**
     * Delete a file record and remove the physical file.
     *
     * @param BerkasAnak $berkas
     * @return void
     */
    public function delete(BerkasAnak $berkas): void
    {
        // Delete physical file
        if (Storage::disk('public')->exists($berkas->file_path)) {
            Storage::disk('public')->delete($berkas->file_path);
        }

        // Delete database record
        $berkas->delete();
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
            throw new \Exception('Ukuran file melebihi 2MB.');
        }

        $mimeType = $file->getMimeType();
        $allowedMimeTypes = [
            'image/jpeg',
            'image/png',
            'application/pdf'
        ];
        if (!in_array($mimeType, $allowedMimeTypes)) {
            throw new \Exception('Tipe MIME tidak diizinkan.');
        }
    }
}
