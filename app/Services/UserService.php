<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * UserService - Menangani logika bisnis untuk manajemen pengguna
 * 
 * Service ini menyediakan operasi CRUD lengkap dengan:
 * - Pembuatan dan update pengguna
 * - Soft delete, restore, dan hard delete (soft delete dikomentari untuk kemungkinan masa depan)
 * - Pagination dengan search
 * - Penghitungan total pengguna (untuk dashboard)
 * 
 * Prinsip:
 * - Service hanya fokus pada logika bisnis
 * - Tidak ada HTTP handling atau response
 * - Exception di-throw ke Controller untuk penanganan
 * - Menggunakan Eloquent ORM untuk query database
 * 
 * @package App\Services
 */
class UserService
{
    protected $auditLogService;

    public function __construct(\App\Services\AuditLogService $auditLogService)
    {
        $this->auditLogService = $auditLogService;
    }

    /**
     * Membuat pengguna baru di database
     * 
     * Proses:
     * 1. Mengambil data dari request yang sudah divalidasi
     * 2. Meng-hash password sebelum disimpan
     * 3. Menyimpan data ke database
     * 
     * Catatan:
     * - Phone bersifat opsional (bisa null)
     * - Password wajib di-hash menggunakan bcrypt
     * 
     * @param array $data Data pengguna yang sudah divalidasi
     *                    - name: string (nama lengkap)
     *                    - email: string (email unik)
     *                    - phone: string|null (nomor telepon opsional)
     *                    - password: string (password yang akan di-hash)
     *                    - role: string (admin/petugas)
     * @return User Instance User yang baru dibuat
     * 
     * @throws \Exception Jika terjadi error saat penyimpanan ke database
     */
    public function create(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
        ]);

        $auditData = collect($data)->except([
            'password',
            'password_confirmation',
            '_token'
        ])->toArray();

        $this->auditLogService->logCrud(
            User::class,
            $user->id,
            'created',
            [],
            $auditData
        );

        return $user;
    }

    /**
     * Memperbarui data pengguna yang sudah ada
     * 
     * Proses:
     * 1. Update field wajib (name, email, phone, role)
     * 2. Update password HANYA jika ada input password baru
     * 3. Simpan perubahan ke database
     * 
     * Catatan:
     * - Phone bersifat opsional (bisa null)
     * - Password tidak diupdate jika kosong/null
     * 
     * @param User $user Instance User yang akan diupdate
     * @param array $data Data pengguna yang sudah divalidasi
     *                    - name: string
     *                    - email: string
     *                    - phone: string|null
     *                    - role: string
     * @return User Instance User yang sudah diupdate
     * 
     * @throws \Exception Jika terjadi error saat update database
     */
    public function update(User $user, array $data): User
    {
        $oldValues = [
            'name'  => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'role'  => $user->role,
        ];

        // Update hanya field yang boleh diubah oleh admin
        $user->name  = $data['name'];
        $user->email = $data['email'];
        $user->phone = $data['phone'] ?? null;
        $user->role  = $data['role'];

        $user->save();

        $auditNew = [
            'name'  => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'role'  => $data['role'],
        ];

        $this->auditLogService->logCrud(
            User::class,
            $user->id,
            'updated',
            $oldValues,
            $auditNew
        );

        return $user;
    }

    // ========================================================================
    // SOFT DELETE & RESTORE - DIKOMENTARI UNTUK KEMUNGKINAN MASA DEPAN
    // ========================================================================
    /*
    /**
     * Soft delete pengguna (hapus sementara)
     * 
     * Proses:
     * - Mengisi kolom deleted_at dengan timestamp saat ini
     * - Data tetap ada di database dan bisa dipulihkan
     * - Menggunakan Laravel SoftDeletes trait
     * 
     * @param User $user Instance User yang akan di-soft delete
     * @return void
     * 
     * @throws \Exception Jika terjadi error saat soft delete
     *\/
    public function delete(User $user): void
    {
        $user->delete(); // Mengisi kolom deleted_at
    }

    /**
     * Memulihkan pengguna yang di-soft delete
     * 
     * Proses:
     * - Mencari user berdasarkan ID (termasuk yang di-soft delete)
     * - Mengosongkan kolom deleted_at
     * - User kembali aktif dan bisa diakses
     * 
     * @param int $id ID pengguna yang akan dipulihkan
     * @return User|null Mengembalikan User jika berhasil, null jika tidak ditemukan
     * 
     * @throws \Exception Jika terjadi error saat restore
     *\/
    public function restore(int $id): ?User
    {
        // Cari user termasuk yang di-soft delete
        $user = User::withTrashed()->where('id', $id)->first();

        if ($user) {
            $user->restore(); // Kosongkan kolom deleted_at
        }

        return $user;
    }
    */

    /**
     * Hard delete pengguna (hapus permanen dari database)
     * 
     * PERUBAHAN PENTING:
     *  HAPUS pengecekan "whereNotNull('deleted_at')" 
     *  Bisa menghapus user AKTIF langsung tanpa soft delete terlebih dahulu
     *  Menggunakan withTrashed() untuk handle user aktif maupun soft-deleted
     * 
     * Proses:
     * - Cari user berdasarkan ID (baik aktif maupun yang di-soft delete)
     * - Hapus data secara permanen dari database
     * - Data TIDAK BISA dipulihkan setelah ini
     * 
     * @param int $id ID pengguna yang akan dihapus permanen
     * @return bool True jika berhasil dihapus, false jika tidak ditemukan
     * 
     * @throws \Exception Jika terjadi error saat hard delete
     */
    public function hardDelete(int $id): bool
    {
        $user = User::withTrashed()->where('id', $id)->first();

        if ($user) {
            $oldValues = $user->toArray();

            $result = $user->forceDelete();

            // Log audit untuk delete
            $this->auditLogService->logCrud(
                User::class,
                $id,
                'deleted',
                $oldValues,
                []
            );

            return $result;
        }

        return false;
    }

    /**
     * Mendapatkan data pengguna dengan pagination dan search
     * 
     * Fitur:
     * - Pagination dengan jumlah item per halaman yang bisa dikustomisasi
     * - Search pada kolom: name, email, phone, role
     * - Opsi untuk menyertakan pengguna yang di-soft delete (tidak digunakan saat ini)
     * - Urut berdasarkan created_at descending (terbaru di atas)
     * 
     * Digunakan di:
     * - Halaman index users (daftar pengguna)
     * - Dashboard statistics (dengan parameter tertentu)
     * 
     * @param int $perPage Jumlah data per halaman (default: 15)
     * @param bool $includeTrashed Jika true, tampilkan juga pengguna yang di-soft delete
     *                              (TIDAK DIGUNAKAN saat ini karena tidak ada soft delete)
     * @param string|null $search Kata kunci pencarian (opsional)
     *                            - Search di: name, email, phone, role
     * @return LengthAwarePaginator Data pengguna dengan pagination
     * 
     * @throws \Exception Jika terjadi error saat query database
     */
    public function getPaginated(
        int $perPage = 15,
        bool $includeTrashed = false,
        ?string $search = null
    ): LengthAwarePaginator {
        $query = User::query();

        // Terapkan filter search jika ada
        if (!empty(trim($search))) {
            $searchTerm = '%' . trim($search) . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                    ->orWhere('email', 'like', $searchTerm)
                    ->orWhere('phone', 'like', $searchTerm)
                    ->orWhere('role', 'like', $searchTerm);
            });
        }

        // TIDAK DIGUNAKAN saat ini karena tidak ada soft delete
        // if ($includeTrashed) {
        //     $query->withTrashed();
        // }

        // Urutkan berdasarkan created_at descending (terbaru di atas)
        $query->orderBy('created_at', 'desc');

        return $query->paginate($perPage);
    }

    /**
     * Mendapatkan SEMUA data pengguna (tanpa pagination)
     * 
     * Catatan:
     * - Hanya digunakan untuk keperluan khusus (misal: export data)
     * - Tidak disarankan untuk data besar karena tidak ada pagination
     * - Gunakan getPaginated() untuk menampilkan di halaman web
     * 
     * @param bool $includeTrashed Jika true, tampilkan juga pengguna yang di-soft delete
     *                              (TIDAK DIGUNAKAN saat ini)
     * @return \Illuminate\Database\Eloquent\Collection Koleksi semua pengguna
     * 
     * @throws \Exception Jika terjadi error saat query database
     */
    public function getAll(bool $includeTrashed = false)
    {
        $query = User::query();

        // TIDAK DIGUNAKAN saat ini karena tidak ada soft delete
        // if ($includeTrashed) {
        //     $query->withTrashed();
        // }

        return $query->get();
    }

    /**
     * Mencari pengguna berdasarkan ID
     * 
     * @param int $id ID pengguna yang dicari
     * @param bool $withTrashed Jika true, cari juga pengguna yang di-soft delete
     *                          (TIDAK DIGUNAKAN saat ini)
     * @return User|null Mengembalikan User jika ditemukan, null jika tidak
     * 
     * @throws \Exception Jika terjadi error saat query database
     */
    public function findById(int $id, bool $withTrashed = false): ?User
    {
        // TIDAK DIGUNAKAN saat ini karena tidak ada soft delete
        // return $withTrashed
        //     ? User::withTrashed()->find($id)
        //     : User::find($id);

        // HANYA CARI USER AKTIF (tanpa soft delete)
        return User::find($id);
    }

    /**
     * Menghitung total pengguna aktif (tidak di-soft delete)
     * 
     * Digunakan di:
     * - Dashboard untuk menampilkan statistik total pengguna
     * 
     * @return int Jumlah total pengguna aktif
     * 
     * @throws \Exception Jika terjadi error saat query database
     */
    public function getTotalCount(): int
    {
        return User::count();
    }

    // /**
    //  * Get total count of user.
    //  *
    //  * @param bool $includeTrashed (BELUM DIGUNAKAN karena tidak ada soft delete)
    //  * @return int
    //  */
    // public function getTotalCountWithTrashed(bool $includeTrashed = false): int
    // {
    //     $query = User::query();

    //     // TIDAK DIGUNAKAN karena tidak ada soft delete
    //     // if ($includeTrashed) {
    //     //     $query->withTrashed();
    //     // }

    //     return $query->count();
    // }
}
