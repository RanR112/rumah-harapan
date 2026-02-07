<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * BerkasAnak model represents a document or photo associated with a foster child.
 * Stores file metadata and provides download URL.
 */
class BerkasAnak extends Model
{
    protected $fillable = [
        'anak_asuh_id',
        'file_path',
        'original_name',
        'mime_type',
        'size_bytes',
        'uploaded_by',
    ];

    /**
     * Get the public download URL for this file.
     *
     * @return string
     */
    public function getDownloadUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    /**
     * Define relationship to AnakAsuh.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function anakAsuh()
    {
        return $this->belongsTo(AnakAsuh::class);
    }

    /**
     * Define relationship to user who uploaded the file.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}