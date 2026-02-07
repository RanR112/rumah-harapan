<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * UserController - Controller untuk manajemen pengguna dengan pendekatan Traditional
 * 
 * Controller ini menggunakan pola MVC standar Laravel:
 * - Setiap operasi CRUD menggunakan halaman terpisah
 * - Form submission tradisional dengan redirect dan flash messages
 * - Search dan pagination menggunakan query parameters (reload halaman)
 * 
 * Struktur halaman:
 * - index: Daftar pengguna dengan search & pagination
 * - create: Halaman form tambah pengguna
 * - edit: Halaman form edit pengguna
 * 
 * Fitur keamanan:
 * - Pencegahan self-deletion (user tidak bisa hapus akun sendiri)
 * - Validasi input melalui Form Requests
 * - Error logging untuk debugging
 * 
 * @package App\Http\Controllers
 */
class UserController extends Controller
{
    /**
     * Instance UserService untuk menangani logika bisnis
     * 
     * @var UserService
     */
    protected UserService $userService;

    /**
     * Constructor - Dependency Injection
     * 
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Menampilkan halaman daftar pengguna dengan search dan pagination
     * 
     * Method ini:
     * 1. Mengambil parameter search dan trashed dari query string
     * 2. Memanggil service untuk mendapatkan data paginated
     * 3. Mengirim data ke view index.blade.php
     * 
     * Contoh URL:
     * - /users?search=admin&page=2
     * - /users?trashed=1 (menampilkan pengguna yang di-soft delete)
     * 
     * @param Request $request HTTP request instance
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Handle AJAX request untuk search/pagination
        if ($request->ajax() || $request->wantsJson()) {
            $perPage = $request->input('per_page', 7);
            $search = $request->input('search', '');
            $page = $request->input('page', 1);

            $users = $this->userService->getPaginated($perPage, false, $search);

            /** @var array $rawData */
            $rawData = [];
            foreach ($users->items() as $user) {
                $rawData[] = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'role' => $user->role,
                    'deleted_at' => null,
                ];
            }

            return response()->json([
                'data' => $rawData,
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'total' => $users->total(),
                'per_page' => $users->perPage(),
                'first_item' => $users->firstItem(),
            ]);
        }

        return view('pages.users.index');
    }

    /**
     * Menampilkan halaman form tambah pengguna
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('pages.users.create');
    }

    /**
     * Menyimpan pengguna baru ke database
     * 
     * Proses:
     * 1. Validasi data input melalui StoreUserRequest
     * 2. Simpan data ke database via UserService
     * 3. Redirect ke halaman daftar dengan pesan sukses
     * 
     * Jika terjadi error:
     * - Redirect kembali ke form dengan old input
     * - Tampilkan pesan error
     * 
     * @param StoreUserRequest $request Request yang sudah divalidasi
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreUserRequest $request)
    {
        try {
            $this->userService->create($request->validated());

            // Redirect ke index dengan query parameter
            return redirect()->route('users.index', [
                'success' => 'Pengguna berhasil ditambahkan.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating user', [
                'error' => $e->getMessage(),
                'user' => $request->user()?->email
            ]);


            return redirect()->route('users.index', [
                'error' => 'Gagal menambahkan pengguna. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * Menampilkan halaman form edit pengguna
     * 
     * @param int $id ID pengguna yang akan diedit
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit(int $id)
    {
        try {
            $user = $this->userService->findById($id, true);

            if (!$user) {
                return redirect()->route('users.index', [
                    'error' => 'Pengguna tidak ditemukan.'
                ]);
            }

            return view('pages.users.edit', compact('user'));
        } catch (\Exception $e) {
            Log::error('Error loading edit page', [
                'user_id' => $id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('users.index', [
                'error' => 'Gagal memuat halaman edit.'
            ]);
        }
    }

    /**
     * Memperbarui data pengguna di database
     * 
     * Proses:
     * 1. Validasi data input melalui UpdateUserRequest
     * 2. Cari pengguna berdasarkan ID
     * 3. Update data via UserService
     * 4. Redirect ke halaman daftar dengan pesan sukses
     * 
     * @param UpdateUserRequest $request Request yang sudah divalidasi
     * @param int $id ID pengguna yang akan diupdate
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateUserRequest $request, int $id)
    {
        try {
            $user = $this->userService->findById($id);

            if (!$user) {
                return redirect()->route('users.index', [
                    'error' => 'Pengguna tidak ditemukan.'
                ]);
            }

            $this->userService->update($user, $request->validated());

            // Redirect ke index dengan query parameter
            return redirect()->route('users.index', [
                'success' => 'Pengguna berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating user', [
                'user_id' => $id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('users.index', [
                'error' => 'Gagal memperbarui pengguna. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * Hard delete pengguna (hapus permanen dari database)
     * 
     * Fitur keamanan:
     * - Mencegah user menghapus permanen akun sendiri
     * - Hanya bisa dilakukan pada pengguna yang sudah di-soft delete
     * 
     * ⚠️ PERINGATAN: Data tidak bisa dipulihkan setelah ini!
     * 
     * @param Request $request HTTP request instance
     * @param int $id ID pengguna yang akan dihapus permanen
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, int $id)
    {
        try {
            // 🔒 KEAMANAN: Cegah self-deletion
            if ($request->user()->id === $id) {
                return redirect()->route('users.index', [
                    'error' => 'Anda tidak dapat menghapus akun Anda sendiri.'
                ]);
            }

            // Cari user terlebih dahulu untuk validasi
            $user = $this->userService->findById($id);

            if (!$user) {
                return redirect()->route('users.index', [
                    'error' => 'Pengguna tidak ditemukan.'
                ]);
            }

            // LANGSUNG HARD DELETE (tanpa soft delete terlebih dahulu)
            $success = $this->userService->hardDelete($id);

            if (!$success) {
                return redirect()->route('users.index', [
                    'error' => 'Gagal menghapus pengguna. Silakan coba lagi.'
                ]);
            }

            // SUCCESS: Redirect ke index dengan query parameter
            return redirect()->route('users.index', [
                'success' => 'Pengguna berhasil dihapus permanen.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error hard deleting user', [
                'user_id' => $id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('users.index', [
                'error' => 'Terjadi kesalahan saat menghapus pengguna.'
            ]);
        }
    }
}
