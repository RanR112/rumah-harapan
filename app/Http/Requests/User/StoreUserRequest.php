<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

/**
 * StoreUserRequest validates data for creating a new user.
 */
class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'sometimes|required|string|min:10|max:15',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,petugas',
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
