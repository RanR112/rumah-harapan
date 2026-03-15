<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * ThemeController - Menangani preferensi tampilan (dark/light mode) per user
 *
 * Hanya satu operasi:
 * - update(): Simpan preferensi tema user yang sedang login ke database
 *
 * Akses: Semua user yang sudah login (admin & petugas)
 *
 * @package App\Http\Controllers
 */
class ThemeController extends Controller
{
    /**
     * Menyimpan preferensi tema user ke database
     *
     * Dipanggil via AJAX dari halaman settings saat user toggle dark/light mode.
     * Langsung update kolom `theme` pada user yang sedang login.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $request->validate([
            'theme' => ['required', 'in:light,dark'],
        ]);

        try {
            $user = $request->user();
            $user->theme = $request->theme;
            $user->save();

            return response()->json([
                'success' => true,
                'theme'   => $user->theme,
                'message' => 'Preferensi tema berhasil disimpan.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving theme preference', [
                'user_id' => $request->user()?->id,
                'error'   => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan preferensi tema.',
            ], 500);
        }
    }
}
