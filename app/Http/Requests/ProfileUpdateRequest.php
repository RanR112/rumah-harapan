<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->user()->id),
            ],
            'phone' => ['nullable', 'string', 'max:20'],

            // Validasi current_password hanya sebagai string nullable
            // Validasi kecocokan password ditangani manual di controller
            'current_password' => ['nullable', 'string'],

            // sometimes: skip validasi jika field tidak ada di request sama sekali
            // nullable: izinkan nilai kosong
            'password' => [
                'sometimes',
                'nullable',
                'string',
                Password::min(8)
                    ->letters()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
                'confirmed',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.current_password' => 'Kata sandi saat ini tidak sesuai.',
            'password.min'       => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak sesuai.',
            'phone.max'          => 'Nomor telepon maksimal 20 karakter.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name'                  => 'Nama Lengkap',
            'email'                 => 'Email',
            'phone'                 => 'Nomor Telepon',
            'current_password'      => 'Kata Sandi Saat Ini',
            'password'              => 'Kata Sandi Baru',
            'password_confirmation' => 'Konfirmasi Kata Sandi',
        ];
    }
}
