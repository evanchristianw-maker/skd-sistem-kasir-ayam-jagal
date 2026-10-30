<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Menjalankan proses pengisian data awal database.
     */
    public function run(): void
    {
        // Membuat akun Admin UMKM Ayam Jagal
        User::factory()->admin()->create([
            // Username untuk login
            'username' => 'admin',

            // Nama lengkap Admin
            'nama_lengkap' => 'Administrator UMKM Ayam Jagal',

            // Password untuk pengujian lokal
            'password' => 'Admin12345!',

            // Menentukan hak akses sebagai Admin
            'role' => 'admin',
        ]);
    }
}