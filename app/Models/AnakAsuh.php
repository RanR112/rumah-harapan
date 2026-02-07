<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * AnakAsuh model represents a foster child in the system.
 * Includes computed age and soft delete capability.
 */
class AnakAsuh extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'rumah_harapan_id',
        'nama_anak',
        'nik',
        'no_kartu_keluarga',
        'alamat_lengkap',
        'jenis_kel',
        'tempat_lahir',
        'tanggal_lahir',
        'status',
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
        'tanggal_lahir' => 'date',
        'tanggal_masuk_rh' => 'date',
    ];

    /**
     * Compute age from birth date.
     *
     * @return Attribute
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
     * Define relationship to RumahHarapan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rumahHarapan()
    {
        return $this->belongsTo(RumahHarapan::class);
    }

    /**
     * Define relationship to user who created this record.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Define relationship to user who last updated this record.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
