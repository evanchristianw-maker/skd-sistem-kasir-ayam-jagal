<?php

namespace App\Models;

// Mengimpor factory untuk pembuatan data pengguna
use Database\Factories\UserFactory;
// Mengimpor atribut untuk mengatur kolom yang dapat diisi
use Illuminate\Database\Eloquent\Attributes\Fillable;
// Mengimpor atribut untuk menyembunyikan data sensitif
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Mengimpor fitur factory dan autentikasi Laravel
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'username',
    'nama_lengkap',
    'password',
    'role',
])]

#[Hidden([
    'password',
    'remember_token',
])]

class User extends Authenticatable
{
    public const ROLE_ADMIN = 'admin';
    public const ROLE_KASIR = 'kasir';
    // Mengaktifkan factory dan notifikasi Laravel
    use HasFactory, Notifiable;

    /**
     * Mengatur factory yang digunakan untuk model User.
     */
    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }

    /**
     * Mengatur tipe data kolom tertentu.
     */
    protected function casts(): array
    {
        return [
            // Password akan disimpan dalam bentuk hash
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi user dengan transaksi yang dilayani.
     */

    /**
     * Memeriksa apakah pengguna memiliki role Admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Memeriksa apakah pengguna memiliki role Kasir.
     */
    public function isKasir(): bool
    {
        return $this->role === self::ROLE_KASIR;
    }
    public function transaksis(): HasMany
    {
        return $this->hasMany(Transaksi::class);
    }

    /**
     * Relasi user/kasir dengan transaksi yang dikonfirmasi pembayarannya.
     */
    public function transaksiDikonfirmasi(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'dikonfirmasi_oleh');
    }
}
