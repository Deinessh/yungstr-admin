<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use App\Support\AuthIdentifier;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'login' => ['required', 'string', 'max:255', function ($attribute, $value, $fail) {
                $value = trim($value);

                if (str_contains($value, '@')) {
                    if (! filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $fail('Enter a valid email address.');
                    }

                    return;
                }

                if (! preg_match('/^\d{10}$/', AuthIdentifier::normalizePhone($value))) {
                    $fail('Mobile number must be exactly 10 digits.');
                }
            }],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $user = $this->findUser();

        if (! $user || ! Hash::check($this->string('password'), $user->password)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'login' => trans('auth.failed'),
            ]);
        }

        Auth::login($user, $this->boolean('remember'));

        RateLimiter::clear($this->throttleKey());
    }

    /**
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
            'login' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('login')).'|'.$this->ip());
    }

    protected function findUser(): ?User
    {
        $login = trim($this->input('login'));

        if (str_contains($login, '@')) {
            return User::where('email', strtolower($login))->first();
        }

        $phone = AuthIdentifier::normalizePhone($login);

        return User::query()
            ->whereNotNull('phone')
            ->where(function ($query) use ($phone) {
                $query->where('phone', $phone)
                    ->orWhere('phone', '+91'.$phone)
                    ->orWhere('phone', '91'.$phone)
                    ->orWhereRaw("RIGHT(REPLACE(REPLACE(REPLACE(REPLACE(phone, '+', ''), ' ', ''), '-', ''), '.', ''), 10) = ?", [$phone]);
            })
            ->first();
    }
}
