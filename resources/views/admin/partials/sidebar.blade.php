@php
$isMobile = !empty($mobile);
$classes = $isMobile
    ? 'fixed inset-y-0 left-0 z-50 w-72 max-w-[85vw] h-screen bg-admin-sidebar text-white flex flex-col lg:hidden'
    : 'hidden lg:flex fixed inset-y-0 left-0 z-40 w-64 h-screen bg-admin-sidebar text-white flex-col';
@endphp

<aside
    @if($isMobile)
    x-show="mobileNav"
    x-cloak
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="-translate-x-full"
    x-transition:enter-end="translate-x-0"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="translate-x-0"
    x-transition:leave-end="-translate-x-full"
    role="dialog"
    aria-modal="true"
    aria-label="Admin navigation"
    @endif
    class="{{ $classes }}">
    <div class="p-4 border-b border-white/10 flex items-center justify-between gap-3">
        <a href="{{ route('admin.dashboard') }}" @if($isMobile) @click="mobileNav = false" @endif class="flex items-center gap-3 min-w-0">
            <span class="shrink-0 rounded-xl bg-white p-1.5 shadow-sm">
                @include('partials.logo-badge', ['size' => 'admin'])
            </span>
            <span class="min-w-0">
                <span class="font-bold text-sm text-white block truncate">{{ $storeSettings['brand_name'] ?? 'Yungstr Club' }}</span>
                <span class="block text-[10px] text-white/45">Admin Panel</span>
            </span>
        </a>
        @if($isMobile)
        <button type="button" @click="mobileNav = false" class="w-9 h-9 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center" aria-label="Close menu">
            <i class="fas fa-times"></i>
        </button>
        @endif
    </div>

    <nav class="flex-1 p-3 overflow-y-auto">
        @include('admin.partials.nav-links', ['mobile' => $isMobile])
    </nav>

    <div class="p-4 border-t border-white/10 space-y-3">
        <div class="flex items-center gap-3 px-2">
            <div class="w-10 h-10 rounded-full bg-admin-sidebar-active flex items-center justify-center text-white text-sm font-bold shrink-0">
                {{ strtoupper(substr(auth('admin')->user()->name, 0, 2)) }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold text-white truncate">{{ auth('admin')->user()->name }}</p>
                <p class="text-[11px] text-white/45">Administrator</p>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-lg text-sm text-white/70 hover:text-white hover:bg-white/10 transition">
                <i class="fas fa-sign-out-alt text-xs"></i> Logout
            </button>
        </form>
    </div>
</aside>
