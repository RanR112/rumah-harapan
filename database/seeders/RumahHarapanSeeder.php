<?php

namespace Database\Seeders;

use App\Models\RumahHarapan;
use App\Models\User;
use Illuminate\Database\Seeder;

class RumahHarapanSeeder extends Seeder
{
    public function run(): void
    {
        // Asumsikan ada user admin dengan ID 1
        $adminId = 1;

        RumahHarapan::create([
            'kode' => 'JKT-PST',
            'nama' => 'Rumah Harapan Jakarta Pusat',
            'alamat' => 'Jl. Sudirman No. 100',
            'kota' => 'Jakarta Pusat',
            'provinsi' => 'DKI Jakarta',
            'telepon' => '021-12345678',
            'email' => 'jkt.pst@rumahharapan.org',
            'koordinator' => 'Budi Santoso',
            'created_by' => $adminId,
            'updated_by' => $adminId,
        ]);

        RumahHarapan::create([
            'kode' => 'BDG-SLT',
            'nama' => 'Rumah Harapan Bandung Selatan',
            'alamat' => 'Jl. Riau No. 50',
            'kota' => 'Bandung',
            'provinsi' => 'Jawa Barat',
            'telepon' => '022-87654321',
            'email' => 'bdg.slt@rumahharapan.org',
            'koordinator' => 'Siti Rahayu',
            'created_by' => $adminId,
            'updated_by' => $adminId,
        ]);
    }
}