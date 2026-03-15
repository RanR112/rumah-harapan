<?php

namespace App\Http\Requests\AnakAsuh;

use App\Models\AnakAsuh;
use Illuminate\Foundation\Http\FormRequest;

/**
 * StoreChildRequest validates data for creating a new foster child.
 */
class StoreAnakAsuhRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $statusValues = implode(',', array_keys(AnakAsuh::STATUS_OPTIONS));

        return [
            'rh'                              => 'required|string',
            'nama_anak'                       => 'required|string|max:255',
            'nik'                             => 'required|digits:16|unique:anak_asuhs,nik',
            'no_kartu_keluarga'               => 'required|digits:16',
            'jenis_kel'                       => 'required|in:L,P',
            'tempat_lahir'                    => 'required|string|max:100',
            'tanggal_lahir'                   => 'required|date|before_or_equal:today',
            'status'                          => "required|in:{$statusValues}",
            'grade'                           => 'required|in:A,B,C,D,E',
            'nama_orang_tua'                  => 'required|string|max:255',
            'tanggal_masuk_rh'                => 'required|date|before_or_equal:today',
            'alamat_lengkap'                  => 'nullable|string',
            'pendidikan_kelas'                => 'nullable|string|max:50',
            'no_handphone'                    => 'nullable|string|max:20',
            'yang_mengasuh_sebelum_diasrama'  => 'nullable|string|max:255',
            'rekomendasi'                     => 'nullable|string',
            'foto'                            => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }
}
