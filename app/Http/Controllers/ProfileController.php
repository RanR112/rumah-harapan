<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('pages.profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update profile dengan penanganan lengkap
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        try {
            $user = $request->user();
            $validated = $request->validated();

            // Cek apakah user ingin mengganti password
            $wantsToChangePassword = !empty($request->current_password) || !empty($request->password);

            if ($wantsToChangePassword) {
                // Validasi: current_password harus diisi
                if (empty($request->current_password)) {
                    return redirect()->route('profile.edit', ['error' => 'Kata sandi saat ini harus diisi untuk mengubah password.'])
                        ->withInput();
                }

                // Validasi: password baru harus diisi
                if (empty($request->password)) {
                    return redirect()->route('profile.edit', ['error' => 'Kata sandi baru harus diisi.'])
                        ->withInput();
                }

                // Validasi: current_password harus benar
                if (!Hash::check($request->current_password, $user->password)) {
                    return redirect()->route('profile.edit', ['error' => 'Kata sandi saat ini tidak sesuai.'])
                        ->withInput();
                }

                // Hash password baru
                $validated['password'] = Hash::make($request->password);
            }

            // Reset email_verified_at jika email berubah
            if ($user->email !== $validated['email']) {
                $validated['email_verified_at'] = null;
            }

            // Hapus field password-related dari validated data
            unset(
                $validated['current_password'],
                $validated['password_confirmation']
            );

            // Jika tidak ingin ganti password, pastikan field password dihapus
            if (!$wantsToChangePassword) {
                unset($validated['password']);
            }

            // Simpan perubahan ke database
            $user->fill($validated)->save();

            // Redirect sesuai skenario
            if ($wantsToChangePassword) {
                return redirect()->route('profile.edit', ['password_changed' => '1']);
            }

            return redirect()->route('profile.edit', ['success' => 'Profil Anda Berhasil Diperbarui']);
        } catch (\Exception $e) {
            Log::error('Profile update error', [
                'user_id' => $request->user()->id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return redirect()->route('profile.edit', ['error' => 'Terjadi kesalahan. Silakan coba lagi.'])
                ->withInput();
        }
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->to('/');
    }
}
