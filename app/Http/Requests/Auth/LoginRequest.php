<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Memeriksa apakah pengguna memiliki hak akses untuk membuat permintaan ini.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Mengambil aturan validasi yang berlaku untuk permintaan ini.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Pesan validasi kustom dalam Bahasa Indonesia.
     */
    public function messages(): array
    {
        return [
            'username.required' => 'Username atau password salah.',
            'password.required' => 'Username atau password salah.',
        ];
    }

    /**
     * Memverifikasi dan melakukan autentikasi kredensial login.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $loginInput = trim((string)$this->input('username'));
        $password = (string)$this->input('password');
        $loginType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $loginType => $loginInput,
            'password' => $password,
        ];

        if (! Auth::attempt($credentials, $this->boolean('remember'))) {

            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'username' => ['Username atau password salah.'],
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Memastikan batas percobaan login belum terlampaui (rate limiting).
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'username' => ['Terlalu banyak percobaan login. Silakan coba lagi dalam ' . $seconds . ' detik.'],
        ]);
    }

    /**
     * Mengambil kunci pembatas kecepatan (throttle key) untuk permintaan ini.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower((string)$this->input('username')).'|'.$this->ip());
    }
}
