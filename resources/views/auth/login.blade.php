<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <h1 class="text-2xl font-extrabold text-brand-dark mb-1">Log in</h1>
    <p class="text-sm text-gray-500 mb-6">Sign in to your Yungstr Club account</p>

    @if(session('checkout.intended') || (session('url.intended') && str_contains(session('url.intended'), '/checkout')))
    <div class="mb-4 p-4 bg-amber-50 border border-amber-100 rounded-xl text-sm text-gray-700">
        Login is required to continue to checkout.
    </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email or Mobile -->
        <div>
            <x-input-label for="login" value="Email or Mobile Number" />
            <x-text-input id="login" class="block mt-1 w-full" type="text" name="login" :value="old('login')" required autofocus autocomplete="username" placeholder="you@email.com or 9876543210" inputmode="text" />
            <p class="text-xs text-gray-500 mt-1">Use your registered email or 10-digit mobile number.</p>
            <x-input-error :messages="$errors->get('login')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-brand-orange shadow-sm focus:ring-brand-orange" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mt-6">
            <div class="flex flex-col gap-2 text-sm">
                @if (Route::has('register'))
                    <a class="font-semibold text-brand-orange hover:text-brand-orange-dark" href="{{ route('register') }}">
                        Create an account
                    </a>
                @endif
                @if (Route::has('password.request'))
                    <a class="text-gray-600 hover:text-brand-dark" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
            </div>

            <x-primary-button class="justify-center sm:ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
