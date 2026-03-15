<?php

namespace App\Http\Controllers;

/**
 * SettingsController - Menampilkan halaman pengaturan sistem
 *
 * Hanya satu method index() karena:
 * - Info akun diambil langsung dari auth()->user() di Blade
 * - Toggle tema ditangani oleh ThemeController via AJAX
 * - Tidak ada form submission di halaman ini
 *
 * @package App\Http\Controllers
 */
class SettingsController extends Controller
{
    /**
     * Tampilkan halaman pengaturan sistem
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('pages.settings.index');
    }
}
