<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\AuthIdentifier;
use App\Services\ReferralService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'login' => ['required', 'string', 'max:255', function ($attribute, $value, $fail) {
                $value = trim($value);

                if (AuthIdentifier::isEmail($value)) {
                    if (! filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $fail('Enter a valid email address.');
                    }

                    return;
                }

                if (! preg_match('/^\d{10}$/', AuthIdentifier::normalizePhone($value))) {
                    $fail('Mobile number must be exactly 10 digits.');
                }
            }],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $identifier = AuthIdentifier::parse($request->input('login'));

        if ($identifier['type'] === 'email') {
            if (User::where('email', $identifier['value'])->exists()) {
                throw ValidationException::withMessages([
                    'login' => 'This email is already registered.',
                ]);
            }

            $email = $identifier['value'];
            $phone = null;
        } else {
            if (User::where('phone', $identifier['value'])->exists()) {
                throw ValidationException::withMessages([
                    'login' => 'This mobile number is already registered.',
                ]);
            }

            $email = null;
            $phone = $identifier['value'];
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $email,
            'phone' => $phone,
            'password' => Hash::make($request->password),
        ]);

        if ($request->filled('referral_code')) {
            app(ReferralService::class)->attachReferrerOnRegistration($user, $request->referral_code);
        }

        event(new Registered($user));

        Auth::login($user);

        if ($checkout = session()->pull('checkout.intended')) {
            return redirect($checkout);
        }

        $intended = session()->pull('url.intended');
        if ($intended && ! str_contains($intended, '/admin')) {
            return redirect($intended);
        }

        return redirect()->route('account.dashboard');
    }
}
