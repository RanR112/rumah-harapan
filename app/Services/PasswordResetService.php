<?php

namespace App\Services;

use App\Mail\ResetPasswordOtpMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * PasswordResetService handles password reset logic using OTP (6-digit numeric code).
 * OTP is sent via email and valid for 5 minutes.
 */
class PasswordResetService
{
    /**
     * Generate a 6-digit OTP and send it to the user's email.
     *
     * @param string $email The user's email address.
     * @return bool True if OTP was sent, false if user not found.
     */
    public function sendOtp(string $email): bool
    {
        $user = User::where('email', $email)->first();
        if (!$user) {
            return false;
        }

        // Clear any existing OTP
        $this->clearOtp($user);

        // Generate 6-digit numeric OTP
        $otp = rand(100000, 999999);
        $user->update([
            'reset_otp' => $otp,
            'reset_otp_created_at' => now(),
        ]);

        // Send email
        Mail::to($user->email)->send(new ResetPasswordOtpMail((string) $otp));

        return true;
    }

    /**
     * Validate if the provided OTP is correct and not expired (5 minutes).
     *
     * @param string $email The user's email.
     * @param string $otp The OTP input by the user.
     * @return User|null Returns user if valid, null otherwise.
     */
    public function validateOtp(string $email, string $otp): ?User
    {
        $user = User::where('email', $email)
            ->where('reset_otp', $otp)
            ->first();

        if (!$user || !$user->reset_otp_created_at) {
            return null;
        }

        // Check if OTP is expired (5 minutes)
        if (now()->diffInMinutes($user->reset_otp_created_at) >= 5) {
            $this->clearOtp($user);
            return null;
        }

        return $user;
    }

    /**
     * Reset the user's password and clear the OTP.
     *
     * @param User $user The user instance.
     * @param string $newPassword The new plain-text password.
     * @return void
     */
    public function resetPassword(User $user, string $newPassword): void
    {
        $user->update([
            'password' => bcrypt($newPassword),
            'reset_otp' => null,
            'reset_otp_created_at' => null,
        ]);
    }

    /**
     * Clear any existing OTP for a user.
     *
     * @param User $user
     * @return void
     */
    private function clearOtp(User $user): void
    {
        $user->update([
            'reset_otp' => null,
            'reset_otp_created_at' => null,
        ]);
    }
}
