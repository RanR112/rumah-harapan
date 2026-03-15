<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AnakAsuhController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RumahHarapanController;
use App\Http\Controllers\AuditLogController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Health check route
Route::get('/health', function () {
    return response()->noContent();
})->name('health.check');

// Connection loading route
Route::get('/loading', function () {
    return view('auth.loading-connection');
})->name('loading.connection');

// Root redirect
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('loading.connection');
});

// Authenticated routes
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // CSRF token refresh — digunakan oleh session-timeout.js saat akan POST /logout
    // Frontend meminta token fresh agar POST /logout tidak 419 meski token lama expired
    Route::get('/csrf-token', function () {
        return response()->json(['token' => csrf_token()]);
    })->name('csrf.token');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');

    // Theme preference — semua user yang login
    Route::patch('/settings/theme', [ThemeController::class, 'update'])->name('settings.theme');

    // =========================================================================
    // Routes khusus admin
    // =========================================================================
    Route::middleware('role:admin')->group(function () {

        // Users
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');

        // Audit Logs
        Route::get('/audit-log', [AuditLogController::class, 'index'])->name('audit-log.index');
        Route::get('/audit-log/{id}', [AuditLogController::class, 'show'])->name('audit-log.show');

        // Rumah Harapan — write operations (admin only)
        Route::get('/rumah-harapan/create', [RumahHarapanController::class, 'create'])->name('rumah-harapan.create');
        Route::get('/rumah-harapan/{id}/edit', [RumahHarapanController::class, 'edit'])->name('rumah-harapan.edit');

        Route::middleware('throttle:sensitive')->group(function () {
            // Users write
            Route::post('/users', [UserController::class, 'store'])->name('users.store');
            Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
            Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

            // Rumah Harapan write
            Route::post('/rumah-harapan', [RumahHarapanController::class, 'store'])->name('rumah-harapan.store');
            Route::put('/rumah-harapan/{id}', [RumahHarapanController::class, 'update'])->name('rumah-harapan.update');
            Route::delete('/rumah-harapan/{id}', [RumahHarapanController::class, 'destroy'])->name('rumah-harapan.destroy');
        });
    });

    // =========================================================================
    // Routes untuk admin dan petugas
    // =========================================================================
    Route::middleware('role:admin,petugas')->group(function () {

        // Rumah Harapan — read operations (admin & petugas)
        Route::get('/rumah-harapan', [RumahHarapanController::class, 'index'])->name('rumah-harapan.index');
        Route::get('/rumah-harapan/{id}', [RumahHarapanController::class, 'show'])->name('rumah-harapan.show');

        // Anak Asuh — read operations
        Route::get('/anak-asuh', [AnakAsuhController::class, 'index'])->name('anak-asuh.index');
        Route::get('/anak-asuh/create', [AnakAsuhController::class, 'create'])->name('anak-asuh.create');
        Route::get('/anak-asuh/{id}', [AnakAsuhController::class, 'show'])->name('anak-asuh.show');
        Route::get('/anak-asuh/{id}/edit', [AnakAsuhController::class, 'edit'])->name('anak-asuh.edit');

        // Anak Asuh — write operations
        Route::middleware('throttle:sensitive')->group(function () {
            Route::post('/anak-asuh', [AnakAsuhController::class, 'store'])->name('anak-asuh.store');
            Route::put('/anak-asuh/{id}', [AnakAsuhController::class, 'update'])->name('anak-asuh.update');
            Route::delete('/anak-asuh/{id}', [AnakAsuhController::class, 'destroy'])->name('anak-asuh.destroy');

            Route::post('/anak-asuh/{id}/upload-berkas', [AnakAsuhController::class, 'uploadBerkas'])
                ->name('anak-asuh.upload-berkas');
            Route::delete('/anak-asuh/{id}/berkas/{berkasId}', [AnakAsuhController::class, 'deleteBerkas'])
                ->name('anak-asuh.delete-berkas');

            Route::post('/anak-asuh/export', [AnakAsuhController::class, 'export'])->name('anak-asuh.export');
            Route::post('/anak-asuh/import', [AnakAsuhController::class, 'import'])->name('anak-asuh.import');
        });
    });
});

require __DIR__ . '/auth.php';
