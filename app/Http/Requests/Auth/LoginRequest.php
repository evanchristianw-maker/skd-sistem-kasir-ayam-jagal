<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Menentukan apakah pengguna diizinkan melakukan request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Mengatur validasi data login.
     */
    public function rules(): array
    {
        return [
            // Username wajib diisi
            'username' => ['required', 'string'],

            // Password wajib diisi
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Memeriksa kecocokan username dan password.
     */
    public function authenticate(): void
    {
        // Memastikan pengguna belum melewati batas percobaan login
        $this->ensureIsNotRateLimited();

        // Memeriksa username dan password ke database
        if (! Auth::attempt(
            $this->only('username', 'password'),
            $this->boolean('remember')
        )) {
            // Mencatat percobaan login yang gagal
            RateLimiter::hit($this->throttleKey());

            // Menampilkan pesan kesalahan
            throw ValidationException::withMessages([
                'username' => trans('auth.failed'),
            ]);
        }

        // Menghapus hitungan percobaan setelah login berhasil
        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Membatasi percobaan login yang gagal.
     */
    public function ensureIsNotRateLimited(): void
    {
        // Maksimal lima kali percobaan login
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        // Menjalankan event ketika akun terkena pembatasan
        event(new Lockout($this));

        // Mengambil waktu tunggu sebelum mencoba kembali
        $seconds = RateLimiter::availableIn($this->throttleKey());

        // Menampilkan pesan pembatasan login
        throw ValidationException::withMessages([
            'username' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Membuat identitas pembatasan percobaan login.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(
            Str::lower($this->string('username')) . '|' . $this->ip()
        );
    }
}