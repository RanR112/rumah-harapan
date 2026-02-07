<?php

namespace App\Http\Requests\AnakAsuh;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * UpdateChildRequest validates data for updating an existing foster child.
 */
class UpdateAnakAsuhRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $anakAsuhId = $this->route('anak_asuh');

        return [
            'rh' => 'sometimes|required|string',
            'nama_anak' => 'sometimes|required|string|max:255',
            'nik' => [
                'sometimes',
                'required',
                'digits:16',
                Rule::unique('anak_asuhs')->ignore($anakAsuhId),
            ],
            'no_kartu_keluarga' => 'sometimes|required|digits:16',
            'jenis_kel' => 'sometimes|required|in:L,P',
            'tempat_lahir' => 'sometimes|required|string|max:100',
            'tanggal_lahir' => 'sometimes|required|date|before_or_equal:today',
            'status' => 'sometimes|required|in:aktif,tidak aktif',
            'grade' => 'sometimes|required|in:A,B,C,D,E',
            'nama_orang_tua' => 'sometimes|required|string|max:255',
            'tanggal_masuk_rh' => 'sometimes|required|date|before_or_equal:today',
            'alamat_lengkap' => 'nullable|string',
            'pendidikan_kelas' => 'nullable|string|max:50',
            'no_handphone' => 'nullable|string|max:20',
            'yang_mengasuh_sebelum_diasrama' => 'nullable|string|max:255',
            'rekomendasi' => 'nullable|string',
        ];
    }
}
