<?php

namespace App\Http\Requests\RumahHarapan;

use Illuminate\Foundation\Http\FormRequest;

/**
 * StoreRumahHarapanRequest validates data for creating a new branch.
 */
class StoreRumahHarapanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kode' => 'required|string|max:10|unique:rumah_harapans,kode',
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'kota' => 'required|string|max:100',
            'provinsi' => 'required|string|max:100',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'koordinator' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ];
    }
}
