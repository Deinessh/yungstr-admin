<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — Yungstr Club</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        [x-cloak]{display:none!important}
        .admin-shell{font-family:'Inter',system-ui,sans-serif}
    </style>
</head>
<body class="admin-shell bg-admin-main text-gray-800 min-h-screen" x-data="{ mobileNav: false }" x-effect="document.body.classList.toggle('overflow-hidden', mobileNav)" @keydown.escape.window="mobileNav = false">
<div class="flex min-h-screen">
    <div x-show="mobileNav"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="mobileNav = false"
         class="fixed inset-0 z-40 bg-black/50 lg:hidden"
         aria-hidden="true"></div>

    @include('admin.partials.sidebar', ['mobile' => true])
    @include('admin.partials.sidebar')

    <div class="flex-1 flex flex-col min-w-0 lg:ml-64">
        <header class="bg-white border-b border-gray-200 px-4 lg:px-8 py-4 flex items-center justify-between gap-3 sticky top-0 z-30">
            <div class="flex items-center gap-3 min-w-0">
                <button type="button"
                        @click="mobileNav = true"
                        class="lg:hidden w-10 h-10 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 flex items-center justify-center shrink-0 transition"
                        aria-label="Open menu">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="min-w-0">
                    @hasSection('breadcrumb_parent_url')
                        <nav class="flex flex-wrap items-center gap-x-1 text-[11px] uppercase tracking-wider text-gray-400 font-semibold mb-0.5" aria-label="Breadcrumb">
                            <a href="{{ route('admin.dashboard') }}" class="hover:text-admin-green transition-colors">Admin</a>
                            <span aria-hidden="true">/</span>
                            <a href="@yield('breadcrumb_parent_url')" class="hover:text-admin-green transition-colors">@yield('breadcrumb_parent_label')</a>
                            <span aria-hidden="true">/</span>
                            <span class="text-gray-600 truncate">@yield('title', 'Dashboard')</span>
                        </nav>
                    @endif
                    <h1 class="text-lg sm:text-xl font-bold text-gray-900 truncate">@yield('heading', 'Admin')</h1>
                    @hasSection('subtitle')
                        <p class="text-sm text-gray-500 truncate">@yield('subtitle')</p>
                    @elseif(request()->routeIs('admin.dashboard'))
                        <p class="text-sm text-gray-500">Welcome back</p>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-2 sm:gap-3 text-sm shrink-0">
                <a href="{{ route('home') }}" target="_blank" rel="noopener" class="hidden sm:inline-flex items-center gap-1.5 text-gray-600 hover:text-admin-green text-xs font-medium px-3 py-2 rounded-lg hover:bg-gray-50 transition">
                    <i class="fas fa-external-link-alt text-[10px]"></i> View Store
                </a>
            </div>
        </header>

        <main class="flex-1 p-4 lg:p-8 overflow-x-hidden">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-50 text-green-800 rounded-xl border border-green-100">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-50 text-red-800 rounded-xl border border-red-100">{{ session('error') }}</div>
            @endif
            @if(isset($errors) && $errors->any())
                <div class="mb-4 p-4 bg-red-50 text-red-800 rounded-xl border border-red-100">
                    <p class="font-semibold mb-1">Please fix the following:</p>
                    <ul class="list-disc list-inside text-sm space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</div>
@stack('scripts')
</body>
</html>
