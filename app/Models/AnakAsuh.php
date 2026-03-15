<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

/**
 * AnakAsuh model represents a foster child in the system.
 * Includes computed age.
 */
class AnakAsuh extends Model
{

    protected $fillable = [
        'rumah_harapan_id',
        'foto_path',
        'nama_anak',
        'nik',
        'no_kartu_keluarga',
        'alamat_lengkap',
        'jenis_kel',
        'tempat_lahir',
        'tanggal_lahir',
        'status',
        'is_active',
        'grade',
        'pendidikan_kelas',
        'nama_orang_tua',
        'no_handphone',
        'tanggal_masuk_rh',
        'yang_mengasuh_sebelum_diasrama',
        'rekomendasi',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal_lahir'   => 'date',
        'tanggal_masuk_rh' => 'date',
        'is_active'        => 'boolean',
    ];

    /**
     * Nilai enum yang valid untuk kolom status.
     * Digunakan di Request, Service, dan Import untuk validasi konsisten.
     */
    const STATUS_OPTIONS = [
        'yatim'       => 'Yatim',
        'piatu'       => 'Piatu',
        'yatim_piatu' => 'Yatim Piatu',
        'dhuafa'      => 'Dhuafa',
    ];

    /**
     * Tambahkan foto_url ke appends agar selalu di-include
     */
    protected $appends = ['foto_url'];

    /**
     * Compute age from birth date.
     */
    protected function umur(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->tanggal_lahir
                ? now()->diffInYears($this->tanggal_lahir)
                : null
        );
    }

    /**
     * Accessor: label human-readable untuk kolom status.
     * Contoh: 'yatim_piatu' → 'Yatim Piatu'
     */
    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn() => self::STATUS_OPTIONS[$this->status] ?? $this->status
        );
    }

    /**
     * Get the foto URL with proper fallback logic
     */
    public function getFotoUrlAttribute(): string
    {
        if ($this->foto_path) {
            return Storage::url($this->foto_path);
        }

        // Fallback murni UI, tidak pernah masuk database
        $defaultImage = $this->jenis_kel === 'L' ? 'L.png' : 'P.png';
        return asset('images/default-anak-asuh-' . $defaultImage);
    }

    /**
     * Define relationship to RumahHarapan.
     */
    public function rumahHarapan()
    {
        return $this->belongsTo(RumahHarapan::class);
    }

    /**
     * Define relationship to user who created this record.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Define relationship to user who last updated this record.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Define relationship to berkas (documents) of this foster child.
     * Required for withCount('berkasAnak') in AnakAsuhService
     */
    public function berkasAnak()
    {
        return $this->hasMany(BerkasAnak::class, 'anak_asuh_id');
    }
}
