@php
    $cartCount = session('cart') ? count(session('cart')) : 0;
@endphp

<header x-data="{ mobileOpen: false, shopOpen: false }" class="store-header bg-cream sticky top-0 z-50 border-b border-gray-100 relative">
    <div class="max-w-[1400px] mx-auto px-4 lg:px-8 py-3">
        <div class="flex items-center gap-4 lg:gap-8">
            {{-- Logo --}}
            <div class="shrink-0">
                @include('partials.logo-badge')
            </div>

            {{-- Desktop nav (centered) --}}
            <nav class="hidden xl:flex flex-1 items-center justify-center gap-6 text-sm font-medium text-brand-chocolate">
                <a href="{{ route('home') }}" class="pb-0.5 {{ request()->routeIs('home') ? 'text-brand-green border-b-2 border-brand-green font-semibold' : 'hover:text-brand-green' }}">Home</a>

                <div class="relative" @mouseenter="shopOpen = true" @mouseleave="shopOpen = false">
                    <button type="button" class="inline-flex items-center gap-1 pb-0.5 {{ request()->routeIs('products.*') ? 'text-brand-green border-b-2 border-brand-green font-semibold' : 'hover:text-brand-green' }}">
                        Shop <i class="fas fa-chevron-down text-[10px]"></i>
                    </button>
                    <div x-show="shopOpen" x-cloak x-transition class="absolute left-1/2 -translate-x-1/2 top-full pt-2 w-56 z-50">
                        <div class="bg-white rounded-xl shadow-lg border border-amber-100 py-2">
                            <a href="{{ route('products.index') }}" class="block px-4 py-2 text-sm hover:bg-amber-50">All Products</a>
                            @foreach($navCategories as $category)
                            <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="block px-4 py-2 text-sm hover:bg-amber-50">{{ $category->name }}</a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <a href="{{ route('products.index', ['category' => 'breakfast-combo-packs']) }}" class="pb-0.5 hover:text-brand-green">Combo Packs</a>
                <a href="{{ route('about') }}" class="pb-0.5 {{ request()->routeIs('about') ? 'text-brand-green border-b-2 border-brand-green font-semibold' : 'hover:text-brand-green' }}">About Us</a>
                <a href="{{ route('contact') }}" class="pb-0.5 {{ request()->routeIs('contact') ? 'text-brand-green border-b-2 border-brand-green font-semibold' : 'hover:text-brand-green' }}">Contact Us</a>
            </nav>

            {{-- Search + account + cart --}}
            <div class="flex items-center gap-3 sm:gap-4 ml-auto">
                <form action="{{ route('products.index') }}" method="GET" class="hidden md:flex items-center">
                    <div class="relative">
                        <input
                            type="search"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Search products..."
                            class="w-44 lg:w-56 xl:w-64 pl-4 pr-10 py-2.5 rounded-full border border-amber-200/80 bg-white text-sm text-brand-body placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-green/20 focus:border-brand-green/40"
                        >
                        <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-brand-green" aria-label="Search">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>

                @auth
                    <div class="relative hidden sm:block group">
                        <button class="w-9 h-9 rounded-full border border-amber-200/80 bg-white text-brand-chocolate hover:text-brand-green flex items-center justify-center" aria-label="Account">
                            <i class="far fa-user"></i>
                        </button>
                        <div class="absolute right-0 w-48 mt-2 py-2 bg-white rounded-xl shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all border border-gray-100 z-50">
                            <p class="px-4 py-2 text-sm font-semibold text-brand-dark border-b border-gray-100">{{ Auth::user()->name }}</p>
                            <a href="{{ route('account.dashboard') }}" class="block px-4 py-2 text-sm text-gray-600 hover:text-brand-green">My Account</a>
                            <a href="{{ route('orders.index') }}" class="block px-4 py-2 text-sm text-gray-600 hover:text-brand-green">Past Orders</a>
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-600 hover:text-brand-green">Profile</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Log Out</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="hidden sm:flex w-9 h-9 rounded-full border border-amber-200/80 bg-white text-brand-chocolate hover:text-brand-green items-center justify-center" aria-label="Login">
                        <i class="far fa-user"></i>
                    </a>
                @endauth

                <a href="{{ route('cart.index') }}" class="relative w-9 h-9 rounded-full border border-amber-200/80 bg-white text-brand-chocolate hover:text-brand-green flex items-center justify-center" aria-label="Cart">
                    <i class="fas fa-shopping-basket text-sm"></i>
                    @if($cartCount > 0)
                    <span class="absolute -top-1.5 -right-1.5 bg-brand-orange text-white text-[10px] rounded-full min-w-[1.1rem] h-[1.1rem] px-0.5 flex items-center justify-center font-bold">{{ $cartCount }}</span>
                    @endif
                </a>

                <button @click="mobileOpen = !mobileOpen" class="xl:hidden w-9 h-9 rounded-full border border-amber-200/80 bg-white text-brand-chocolate flex items-center justify-center" aria-label="Menu">
                    <i x-show="!mobileOpen" class="fas fa-bars"></i>
                    <i x-show="mobileOpen" x-cloak class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div x-show="mobileOpen" x-cloak x-transition class="xl:hidden border-t border-gray-100 bg-cream px-4 py-4">
        <form action="{{ route('products.index') }}" method="GET" class="mb-4 md:hidden">
            <div class="relative">
                <input type="search" name="search" placeholder="Search products..." class="w-full pl-4 pr-10 py-2.5 rounded-full border border-amber-200/80 bg-white text-sm">
                <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400"><i class="fas fa-search"></i></button>
            </div>
        </form>
        <nav class="flex flex-col gap-1 text-sm font-medium">
            <a href="{{ route('home') }}" class="px-3 py-2.5 rounded-lg hover:bg-white/70">Home</a>
            <a href="{{ route('products.index') }}" class="px-3 py-2.5 rounded-lg hover:bg-white/70">Shop All</a>
            <a href="{{ route('products.index', ['category' => 'breakfast-combo-packs']) }}" class="px-3 py-2.5 rounded-lg hover:bg-white/70">Combo Packs</a>
            <a href="{{ route('about') }}" class="px-3 py-2.5 rounded-lg hover:bg-white/70">About Us</a>
            <a href="{{ route('contact') }}" class="px-3 py-2.5 rounded-lg hover:bg-white/70">Contact Us</a>
            @guest
                <a href="{{ route('login') }}" class="px-3 py-2.5 rounded-lg hover:bg-white/70">Login</a>
                <a href="{{ route('register') }}" class="px-3 py-2.5 rounded-lg hover:bg-white/70">Register</a>
            @else
                <a href="{{ route('account.dashboard') }}" class="px-3 py-2.5 rounded-lg hover:bg-white/70">My Account</a>
                <a href="{{ route('orders.index') }}" class="px-3 py-2.5 rounded-lg hover:bg-white/70">Past Orders</a>
            @endguest
        </nav>
    </div>
</header>
