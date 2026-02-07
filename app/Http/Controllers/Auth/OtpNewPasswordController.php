<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\PasswordResetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class OtpNewPasswordController extends Controller
{
    protected PasswordResetService $passwordResetService;

    public function __construct(PasswordResetService $passwordResetService)
    {
        $this->passwordResetService = $passwordResetService;
    }

    /**
     * Display the OTP verification form.
     */
    public function create(): View|RedirectResponse
    {
        $email = Session::get('email_for_password_reset');
        
        if (!$email) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Sesi telah berakhir. Silakan minta OTP baru.']);
        }

        return view('auth.otp-code', ['email' => $email]);
    }

    /**
     * Handle OTP verification.
     */
    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'otp' => ['required', 'digits:6'],
        ]);

        $email = $request->input('email');
        $otp = $request->input('otp');

        // Validasi OTP
        $user = $this->passwordResetService->validateOtp($email, $otp);

        if (!$user) {
            return back()->withErrors(['otp' => 'OTP tidak valid atau telah kadaluarsa.']);
        }

        // Simpan user ID untuk reset password
        Session::put('user_id_for_password_reset', $user->id);
        Session::put('email_for_password_reset', $email);
        
        return redirect()->route('password.reset.form');
    }

    /**
     * Display the password reset form.
     */
    public function showResetForm(): View|RedirectResponse
    {
        $userId = Session::get('user_id_for_password_reset');
        
        if (!$userId) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Sesi telah berakhir. Silakan minta OTP baru.']);
        }

        $email = Session::get('email_for_password_reset');
        return view('auth.reset-password', ['email' => $email]);
    }

    /**
     * Handle password reset.
     */
    public function store(Request $request): RedirectResponse|View
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $userId = Session::get('user_id_for_password_reset');
        if (!$userId) {
            return redirect()->route('password.request')
                ->withErrors(['password' => 'Sesi telah berakhir.']);
        }

        $user = \App\Models\User::find($userId);
        if (!$user) {
            return redirect()->route('password.request')
                ->withErrors(['password' => 'User tidak ditemukan.']);
        }

        try {
            $this->passwordResetService->resetPassword($user, $request->password);
            Session::forget(['user_id_for_password_reset', 'email_for_password_reset']);
            return view('auth.reset-password-success');
        } catch (\Exception $e) {
            return back()->withErrors(['password' => 'Terjadi kesalahan saat mengatur ulang password.']);
        }
    }
}