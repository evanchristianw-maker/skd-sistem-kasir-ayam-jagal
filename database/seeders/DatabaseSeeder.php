<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Membuat atau memperbarui akun Admin
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'nama_lengkap' => 'Administrator UMKM Ayam Jagal',
                'password' => 'Admin12345!',
                'role' => 'admin',
            ]
        );

        // Membuat atau memperbarui akun Kasir
        User::updateOrCreate(
            ['username' => 'kasir'],
            [
                'nama_lengkap' => 'Kasir UMKM Ayam Jagal',
                'password' => 'Kasir12345!',
                'role' => 'kasir',
            ]
        );
    }
}