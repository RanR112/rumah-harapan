<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * UserSeeder creates an initial admin user for development and testing purposes.
 * This seeder ensures there is at least one authenticated user to access the system.
 */
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Create a default admin user
        User::create([
            'name' => 'Admin Rumah Harapan',
            'email' => 'admin@gmail.com',
            'phone' => '08123456789',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Petugas',
            'email' => 'petugas@gmail.com',
            'phone' => '08123456789',
            'password' => Hash::make('password123'),
            'role' => 'petugas',
        ]);

        User::create([
            'name' => 'Randy Rafael',
            'email' => 'randyrafael112@gmail.com',
            'phone' => '08123456789',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Muhamad Fathurrohman',
            'email' => 'muhammadfathurrohman0602@gmail.com',
            'phone' => '08123456789',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);
    }
}
