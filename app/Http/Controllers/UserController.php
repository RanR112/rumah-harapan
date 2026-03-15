<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $perPage = $request->input('per_page', 7);
            $search  = $request->input('search', '');

            $users = $this->userService->getPaginated($perPage, false, $search);

            $rawData = [];
            foreach ($users->items() as $user) {
                $rawData[] = [
                    'id'         => $user->id,
                    'name'       => $user->name,
                    'email'      => $user->email,
                    'phone'      => $user->phone,
                    'role'       => $user->role,
                    'deleted_at' => null,
                ];
            }

            return response()->json([
                'data'         => $rawData,
                'current_page' => $users->currentPage(),
                'last_page'    => $users->lastPage(),
                'total'        => $users->total(),
                'per_page'     => $users->perPage(),
                'first_item'   => $users->firstItem(),
            ]);
        }

        return view('pages.users.index');
    }

    public function create()
    {
        return view('pages.users.create');
    }

    public function store(StoreUserRequest $request)
    {
        try {
            $this->userService->create($request->validated());

            return redirect()->route('users.index', [
                'success' => 'Pengguna berhasil ditambahkan.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating user', [
                'error' => $e->getMessage(),
                'user'  => $request->user()?->email,
            ]);

            return redirect()->route('users.index', [
                'error' => 'Gagal menambahkan pengguna. Silakan coba lagi.'
            ]);
        }
    }

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
                'error'   => $e->getMessage(),
            ]);

            return redirect()->route('users.index', [
                'error' => 'Gagal memuat halaman edit.'
            ]);
        }
    }

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

            // Kembali ke page asal — dikirim dari hidden input di form edit
            $currentPage = (int) $request->input('current_page', 1);

            return redirect()->route('users.index', array_filter([
                'page'    => $currentPage > 1 ? $currentPage : null,
                'success' => 'Pengguna berhasil diperbarui.',
            ]));
        } catch (\Exception $e) {
            Log::error('Error updating user', [
                'user_id' => $id,
                'error'   => $e->getMessage(),
            ]);

            return redirect()->route('users.index', [
                'error' => 'Gagal memperbarui pengguna. Silakan coba lagi.'
            ]);
        }
    }

    public function destroy(Request $request, int $id)
    {
        try {
            // Cegah self-deletion
            if ($request->user()->id === $id) {
                return redirect()->route('users.index', [
                    'error' => 'Anda tidak dapat menghapus akun Anda sendiri.'
                ]);
            }

            $user = $this->userService->findById($id);

            if (!$user) {
                return redirect()->route('users.index', [
                    'error' => 'Pengguna tidak ditemukan.'
                ]);
            }

            $currentPage = (int) $request->input('current_page', 1);

            $success = $this->userService->hardDelete($id);

            if (!$success) {
                return redirect()->route('users.index', [
                    'error' => 'Gagal menghapus pengguna. Silakan coba lagi.'
                ]);
            }

            // Hitung sisa data setelah delete untuk menentukan redirect page
            $perPage       = 7;
            $remainingData = $this->userService->getPaginated($perPage, false, '');
            $lastPage      = $remainingData->lastPage();

            // Jika page saat ini melebihi last page, turun ke page sebelumnya
            $redirectPage = min($currentPage, max(1, $lastPage));

            return redirect()->route('users.index', array_filter([
                'page'    => $redirectPage > 1 ? $redirectPage : null,
                'success' => 'Pengguna berhasil dihapus.',
            ]));
        } catch (\Exception $e) {
            Log::error('Error hard deleting user', [
                'user_id' => $id,
                'error'   => $e->getMessage(),
            ]);

            return redirect()->route('users.index', [
                'error' => 'Terjadi kesalahan saat menghapus pengguna.'
            ]);
        }
    }
}
