<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AnakAsuhController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Health check route (untuk pengecekan koneksi)
Route::get('/health', function () {
    return response()->noContent();
})->name('health.check');

// Connection loading route (halaman awal aplikasi)
Route::get('/loading', function () {
    return view('auth.loading-connection');
})->name('loading.connection');

// Root redirect dengan logika cerdas
Route::get('/', function () {
    // Jika user sudah login, redirect langsung ke dashboard
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    // Jika user belum login, tampilkan loading connection
    return redirect()->route('loading.connection');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Dashboard utama - gunakan DashboardController
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Routes khusus admin
    Route::middleware('role:admin')->group(function () {
        // GET routes (tanpa rate limit - read only)
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');

        // Write operations dengan rate limiting (throttle:sensitive)
        Route::middleware('throttle:sensitive')->group(function () {
            Route::post('/users', [UserController::class, 'store'])->name('users.store');
            Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
            Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
        });
    });

    // Routes untuk admin dan petugas
    Route::middleware('role:admin,petugas')->group(function () {
        // Anak Asuh - Read operations (tanpa rate limit)
        Route::get('/anak-asuh', [AnakAsuhController::class, 'index'])->name('anak-asuh.index');
        Route::get('/anak-asuh/create', [AnakAsuhController::class, 'create'])->name('anak-asuh.create');
        Route::get('/anak-asuh/{anak_asuh}', [AnakAsuhController::class, 'show'])->name('anak-asuh.show');
        Route::get('/anak-asuh/{anak_asuh}/edit', [AnakAsuhController::class, 'edit'])->name('anak-asuh.edit');

        // Anak Asuh - Write operations dengan rate limiting
        Route::middleware('throttle:sensitive')->group(function () {
            Route::post('/anak-asuh', [AnakAsuhController::class, 'store'])->name('anak-asuh.store');
            Route::put('/anak-asuh/{anak_asuh}', [AnakAsuhController::class, 'update'])->name('anak-asuh.update');
            Route::delete('/anak-asuh/{anak_asuh}', [AnakAsuhController::class, 'destroy'])->name('anak-asuh.destroy');
            Route::post('/anak-asuh/{anak_asuh}/restore', [AnakAsuhController::class, 'restore'])->name('anak-asuh.restore');
            Route::delete('/anak-asuh/{anak_asuh}/hard-delete', [AnakAsuhController::class, 'hardDelete'])->name('anak-asuh.hard-delete');

            // Routes tambahan untuk Anak Asuh
            Route::post('/anak-asuh/{id}/upload-berkas', [AnakAsuhController::class, 'uploadBerkas'])->name('anak-asuh.upload-berkas');
            Route::delete('/anak-asuh/{anak_asuhId}/berkas/{berkasId}', [AnakAsuhController::class, 'deleteBerkas'])->name('anak-asuh.delete-berkas');

            // Import/Export routes
            Route::post('/anak-asuh/export', [AnakAsuhController::class, 'export'])->name('anak-asuh.export');
            Route::post('/anak-asuh/import', [AnakAsuhController::class, 'import'])->name('anak-asuh.import');
        });
    });
});

// Auth routes (login, forgot password, OTP, dll)
require __DIR__ . '/auth.php';
