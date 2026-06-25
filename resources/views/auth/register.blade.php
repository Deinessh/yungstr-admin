<x-guest-layout>
    <h1 class="text-2xl font-extrabold text-brand-dark mb-1">Create account</h1>
    <p class="text-sm text-gray-500 mb-6">Register to shop, track orders, and checkout faster</p>

    @if(session('checkout.intended') || (session('url.intended') && str_contains(session('url.intended'), '/checkout')))
    <div class="mb-4 p-4 bg-amber-50 border border-amber-100 rounded-xl text-sm text-gray-700">
        Create an account to continue to checkout.
    </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        @if(request('ref'))
            <input type="hidden" name="referral_code" value="{{ request('ref') }}">
            <p class="mb-4 text-sm text-brand-green">Referral code applied: {{ request('ref') }}</p>
        @endif

        <div>
            <x-input-label for="name" :value="__('Full Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="login" value="Email or Mobile Number" />
            <x-text-input id="login" class="block mt-1 w-full" type="text" name="login" :value="old('login')" required autocomplete="username" placeholder="you@email.com or 9876543210" />
            <p class="text-xs text-gray-500 mt-1">Provide either email <strong>or</strong> 10-digit mobile — not both. Use the same to log in later.</p>
            <x-input-error :messages="$errors->get('login')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mt-6">
            <a class="text-sm font-semibold text-brand-orange hover:text-brand-orange-dark" href="{{ route('login') }}">
                Already have an account? Log in
            </a>

            <x-primary-button class="justify-center sm:ms-4">
                {{ __('Create Account') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
