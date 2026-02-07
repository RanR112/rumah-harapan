<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\OtpNewPasswordController;
use App\Http\Controllers\Auth\OtpPasswordResetController;
use App\Http\Controllers\Auth\PasswordController;
use Illuminate\Support\Facades\Route;

// Guest routes (untuk user yang belum login)
Route::middleware('guest')->group(function () {
    // Login routes
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    // OTP Forgot Password
    Route::get('forgot-password', [OtpPasswordResetController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [OtpPasswordResetController::class, 'store'])->name('password.email');

    // OTP Verification
    Route::get('otp-code', [OtpNewPasswordController::class, 'create'])->name('otp.code');
    Route::post('otp/verify', [OtpNewPasswordController::class, 'verify'])->name('otp.verification');

    // Password Reset
    Route::get('password/reset', [OtpNewPasswordController::class, 'showResetForm'])->name('password.reset.form');
    Route::post('reset-password', [OtpNewPasswordController::class, 'store'])->name('password.store');
});

// Authenticated routes (untuk user yang sudah login)
Route::middleware('auth')->group(function () {

    // Password update routes
    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    // Logout route
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
