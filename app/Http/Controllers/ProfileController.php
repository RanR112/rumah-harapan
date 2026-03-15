<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ProfileController extends Controller
{
    protected AuditLogService $auditLogService;

    public function __construct(AuditLogService $auditLogService)
    {
        $this->auditLogService = $auditLogService;
    }

    public function edit(Request $request): View
    {
        return view('pages.profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        try {
            $user      = $request->user();
            $oldValues = $user->getOriginal();
            $validated = $request->validated();

            // Cek apakah user ingin mengganti password
            $wantsToChangePassword = !empty($request->current_password) || !empty($request->password);

            // Cek apakah ada perubahan data profil selain password
            $profileFields     = ['name', 'email', 'phone'];
            $hasProfileChanges = false;
            foreach ($profileFields as $field) {
                if (isset($validated[$field]) && $validated[$field] !== $oldValues[$field]) {
                    $hasProfileChanges = true;
                    break;
                }
            }

            if ($wantsToChangePassword) {
                if (empty($request->current_password)) {
                    return redirect()->route('profile.edit', ['error' => 'Kata sandi saat ini harus diisi untuk mengubah password.'])
                        ->withInput();
                }

                if (empty($request->password)) {
                    return redirect()->route('profile.edit', ['error' => 'Kata sandi baru harus diisi.'])
                        ->withInput();
                }

                if (!Hash::check($request->current_password, $user->password)) {
                    return redirect()->route('profile.edit', ['error' => 'Kata sandi saat ini tidak sesuai.'])
                        ->withInput();
                }

                $validated['password'] = Hash::make($request->password);
            }

            // Hapus field password dari validated jika tidak ingin ganti password
            if (!$wantsToChangePassword) {
                unset($validated['password']);
                unset($validated['current_password']);
                unset($validated['password_confirmation']);
            }

            // Reset email_verified_at jika email berubah
            if ($user->email !== $validated['email']) {
                $validated['email_verified_at'] = null;
            }

            $user->fill($validated)->save();

            if ($wantsToChangePassword && !$hasProfileChanges) {
                // Hanya ganti password
                $this->auditLogService->logCrud(
                    'Kata Sandi Profil',
                    $user->id,
                    'change',
                    ['password' => $oldValues['password']],  // hash lama
                    ['password' => $validated['password']]   // hash baru
                );
            } else {
                // Update profil (dengan atau tanpa ganti password)
                $auditOldValues = collect($oldValues)->except([
                    'remember_token',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                    'email_verified_at',
                ])->toArray();

                $auditNewValues = collect($validated)->except([
                    'current_password',
                    'password_confirmation',
                    '_token',
                    'email_verified_at',
                ])->toArray();

                $this->auditLogService->logCrud(
                    'Profil',
                    $user->id,
                    'updated',
                    $auditOldValues,
                    $auditNewValues
                );
            }

            if ($wantsToChangePassword) {
                return redirect()->route('profile.edit', ['password_changed' => '1']);
            }

            return redirect()->route('profile.edit', ['success' => 'Profil Anda Berhasil Diperbarui']);
        } catch (\Exception $e) {
            Log::error('Profile update error', [
                'user_id' => $request->user()->id,
                'error'   => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
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

        $oldValues = collect($user->toArray())->except([
            'password',
            'remember_token',
            'created_at',
            'updated_at',
            'deleted_at',
        ])->toArray();

        Auth::logout();

        $user->delete();

        $this->auditLogService->logCrud(
            get_class($user),
            $user->id,
            'deleted',
            $oldValues,
            []
        );

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->to('/');
    }
}
