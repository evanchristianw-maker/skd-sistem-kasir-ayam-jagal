<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Factory untuk membuat data pengguna.
 *
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * Menyimpan password default agar tidak di-hash berulang kali.
     */
    protected static ?string $password;

    /**
     * Menentukan struktur data pengguna.
     */
    public function definition(): array
    {
        return [
            // Membuat username unik
            'username' => fake()->unique()->userName(),

            // Membuat nama lengkap pengguna
            'nama_lengkap' => fake()->name(),

            // Password default untuk pengujian
            'password' => static::$password ??= Hash::make('password'),

            // Menentukan role default sebagai kasir
            'role' => 'kasir',

            // Membuat token untuk fitur remember me
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Mengatur role pengguna menjadi admin.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
        ]);
    }
}