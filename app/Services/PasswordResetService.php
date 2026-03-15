<?php

namespace App\Services;

use App\Mail\ResetPasswordOtpMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

/**
 * PasswordResetService handles password reset logic using OTP (6-digit numeric code).
 *
 * Security Notes:
 * - Kegagalan "user not found" TIDAK DILOG untuk mencegah user enumeration
 * - Hanya log aktivitas yang melibatkan user yang valid
 * - Nilai OTP TIDAK PERNAH disimpan/log untuk mencegah kebocoran
 */
class PasswordResetService
{
    protected $auditLogService;

    public function __construct(AuditLogService $auditLogService)
    {
        $this->auditLogService = $auditLogService;
    }

    public function sendOtp(string $email): bool
    {
        $user = User::where('email', $email)->first();
        if (!$user) {
            // Tidak dilog — mencegah user enumeration
            return false;
        }

        $this->clearOtp($user);

        $otp = random_int(100000, 999999);
        $user->update([
            'reset_otp'            => $otp,
            'reset_otp_created_at' => now(),
        ]);

        Mail::to($user->email)->send(new ResetPasswordOtpMail((string) $otp));

        $this->auditLogService->logCustom(
            'Reset Password',
            'request',
            [
                'alamat IP' => request()?->ip(),
            ],
            $user
        );

        return true;
    }

    public function validateOtp(string $email, string $otp): ?User
    {
        $user = User::where('email', $email)
            ->where('reset_otp', $otp)
            ->first();

        if (!$user || !$user->reset_otp_created_at) {
            if ($user) {
                $this->auditLogService->logCustom(
                    'Reset Password',
                    'invalid',
                    [
                        'alasan'    => 'Kode OTP salah',
                        'alamat IP' => request()?->ip(),
                    ],
                    $user
                );
            }
            return null;
        }

        if (now()->diffInMinutes($user->reset_otp_created_at) >= 5) {
            $this->clearOtp($user);

            $this->auditLogService->logCustom(
                'Reset Password',
                'invalid',
                [
                    'alasan'    => 'Kode OTP kadaluarsa',
                    'alamat IP' => request()?->ip(),
                ],
                $user
            );
            return null;
        }

        $this->auditLogService->logCustom(
            'Reset Password',
            'verify',
            [
                'keterangan' => 'Kode OTP berhasil diverifikasi',
                'alamat IP'  => request()?->ip(),
            ],
            $user
        );

        return $user;
    }

    public function resetPassword(User $user, string $newPassword): void
    {
        $user->update([
            'password'             => bcrypt($newPassword),
            'reset_otp'            => null,
            'reset_otp_created_at' => null,
        ]);

        $this->auditLogService->logCustom(
            'Reset Password',
            'reset',
            [
                'keterangan' => 'Kata sandi berhasil diperbarui',
                'alamat IP'  => request()?->ip(),
            ],
            $user
        );
    }

    private function clearOtp(User $user): void
    {
        $user->update([
            'reset_otp'            => null,
            'reset_otp_created_at' => null,
        ]);
    }
}
