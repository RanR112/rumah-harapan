<?php

namespace App\Http\Requests\RumahHarapan;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * UpdateRumahHarapanRequest validates data for updating a branch.
 */
class UpdateRumahHarapanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $branchId = $this->route('rumah_harapan');

        return [
            'kode' => [
                'sometimes',
                'required',
                'string',
                'max:10',
                Rule::unique('rumah_harapans')->ignore($branchId),
            ],
            'nama' => 'sometimes|required|string|max:255',
            'alamat' => 'sometimes|required|string',
            'kota' => 'sometimes|required|string|max:100',
            'provinsi' => 'sometimes|required|string|max:100',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'koordinator' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ];
    }
}
