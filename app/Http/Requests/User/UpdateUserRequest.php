<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * UpdateUserRequest validates data for updating an existing user.
 */
class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('id');

        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($userId),
            ],
            'phone' => 'sometimes|required|string|min:10|max:15',
            'role' => 'required|in:admin,petugas',
            'password' => 'nullable|string|min:8|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'Email sudah digunakan.',
            'phone.min' => 'Harap masukkan nomor yang valid',
            'phone.max' => 'Harap masukkan nomor yang valid',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 8 karakter.',
        ];
    }
}
