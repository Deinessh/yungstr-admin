<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — Yungstr Club</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="admin-shell font-sans bg-admin-main min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md card p-6 sm:p-8 border-orange-100/80">
        <div class="text-center mb-8">
            <div class="mb-4 flex justify-center">
                @include('partials.logo-badge', ['size' => 'auth'])
            </div>
            <p class="text-[10px] uppercase tracking-[0.2em] text-gray-400 font-semibold">Admin Panel</p>
            <h1 class="text-2xl font-extrabold text-gray-900 mt-1">Sign in</h1>
            <p class="text-sm text-gray-500 mt-2">Manage your Yungstr Club store</p>
        </div>

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-50 text-red-700 rounded-2xl text-sm border border-red-100">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login') }}" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium text-brand-brown mb-1">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus class="input-field">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-brand-brown mb-1">Password</label>
                <input type="password" id="password" name="password" required class="input-field">
            </div>
            <label class="flex items-center gap-2 text-sm text-brand-brown/70">
                <input type="checkbox" name="remember" class="rounded border-orange-200 text-brand-orange focus:ring-brand-orange/30">
                Remember me
            </label>
            <button type="submit" class="btn-primary w-full py-3 rounded-xl">Sign In</button>
        </form>

        <p class="text-center text-xs text-brand-brown/50 mt-6">
            <a href="{{ route('home') }}" class="hover:text-brand-orange">← Back to store</a>
            ·
            <a href="{{ route('login') }}" class="hover:text-brand-orange">Customer login</a>
        </p>
    </div>
</body>
</html>
