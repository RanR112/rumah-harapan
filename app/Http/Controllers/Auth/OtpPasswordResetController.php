<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\PasswordResetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class OtpPasswordResetController extends Controller
{
    protected PasswordResetService $passwordResetService;

    public function __construct(PasswordResetService $passwordResetService)
    {
        $this->passwordResetService = $passwordResetService;
    }

    /**
     * Display the OTP password reset request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming OTP password reset request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = $request->input('email');

        // Throttling: 1 request per 2 menit per email
        $throttleKey = "password_reset_otp:{$email}";
        $lastAttempt = Cache::get($throttleKey);

        if ($lastAttempt && now()->diffInMinutes($lastAttempt) < 2) {
            return back()->withErrors(['email' => 'Silakan coba lagi dalam 2 menit.']);
        }

        try {
            $success = $this->passwordResetService->sendOtp($email);

            if ($success) {
                Cache::put($throttleKey, now(), 120); // 2 minutes
                session(['email_for_password_reset' => $email]);
                return redirect()->route('otp.code');
            }

            return back()->withErrors(['email' => 'Gagal mengirim OTP. Email tidak terdaftar.']);

        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Terjadi kesalahan saat mengirim OTP.']);
        }
    }
}