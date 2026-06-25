<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', $pageSeo['title'] ?? $storeSettings['seo_default_title'] ?? 'Yungstr Club')</title>
    @hasSection('meta_description')
        <meta name="description" content="@yield('meta_description')">
    @elseif(!empty($pageSeo['description']))
        <meta name="description" content="{{ $pageSeo['description'] }}">
    @else
        <meta name="description" content="{{ $storeSettings['seo_default_description'] ?? '' }}">
    @endif
    @if(!empty($pageSeo['canonical']))
        <link rel="canonical" href="{{ $pageSeo['canonical'] }}">
    @endif

    <link rel="icon" type="image/png" href="{{ !empty($storeSettings['favicon_path']) ? asset($storeSettings['favicon_path']) : asset('favicon.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    <meta name="theme-color" content="{{ $storeSettings['theme_primary'] ?? '#004D26' }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.theme-variables')
    @stack('styles')
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="antialiased min-h-screen flex flex-col">
    @include('partials.announcement-bar')
    @include('partials.header')

    <main class="flex-grow">
        @if(session('success'))
            <div class="max-w-7xl mx-auto px-4 lg:px-12 mt-4">
                <div class="bg-brand-green-soft border-l-4 border-brand-green text-brand-green p-4 rounded-r-lg shadow-sm" role="alert">
                    <p class="font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="max-w-7xl mx-auto px-4 lg:px-12 mt-4">
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r-lg shadow-sm" role="alert">
                    <p class="font-medium">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    @include('partials.newsletter-bar')
    @include('partials.footer')
    @include('partials.contact-fab')

    @stack('scripts')
</body>
</html>
