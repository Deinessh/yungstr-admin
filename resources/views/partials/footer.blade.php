<footer class="bg-cream pt-12 pb-6 border-t border-gray-200/60 px-4 text-xs">
    <div class="max-w-7xl mx-auto grid grid-cols-2 md:grid-cols-5 gap-8 mb-10 text-gray-600">
        @include('partials.logo-badge', ['size' => 'footer'])

        <div class="space-y-2.5">
            <h4 class="font-bold uppercase text-gray-800 text-[11px] tracking-wider">Quick Links</h4>
            <ul class="space-y-1.5">
                <li><a href="{{ route('home') }}" class="hover:text-brand-orange">Home</a></li>
                <li><a href="{{ route('products.index') }}" class="hover:text-brand-orange">Shop</a></li>
                <li><a href="{{ route('about') }}" class="hover:text-brand-orange">About Us</a></li>
                <li><a href="{{ route('contact') }}" class="hover:text-brand-orange">Contact</a></li>
            </ul>
        </div>

        <div class="space-y-2.5">
            <h4 class="font-bold uppercase text-gray-800 text-[11px] tracking-wider">Help</h4>
            <ul class="space-y-1.5">
                <li><a href="{{ route('pages.faqs') }}" class="hover:text-brand-orange">FAQs</a></li>
                <li><a href="{{ route('pages.shipping-policy') }}" class="hover:text-brand-orange">Shipping Policy</a></li>
                <li><a href="{{ route('pages.returns-refunds') }}" class="hover:text-brand-orange">Return & Refund</a></li>
                <li><a href="{{ route('pages.terms') }}" class="hover:text-brand-orange">Terms & Conditions</a></li>
                <li><a href="{{ route('pages.privacy-policy') }}" class="hover:text-brand-orange">Privacy Policy</a></li>
            </ul>
        </div>

        <div class="space-y-2.5">
            <h4 class="font-bold uppercase text-gray-800 text-[11px] tracking-wider">Our Products</h4>
            <ul class="space-y-1.5">
                <li><a href="{{ route('products.index') }}" class="hover:text-brand-orange">Dosa Mixes</a></li>
                <li><a href="{{ route('products.index') }}" class="hover:text-brand-orange">Idli Mixes</a></li>
                <li><a href="{{ route('products.index') }}" class="hover:text-brand-orange">Combo Packs</a></li>
                <li><a href="{{ route('products.index') }}" class="hover:text-brand-orange">Trial Packs</a></li>
            </ul>
        </div>

        <div class="space-y-2.5 col-span-2 md:col-span-1">
            <h4 class="font-bold uppercase text-gray-800 text-[11px] tracking-wider">Contact Us</h4>
            <ul class="space-y-2">
                <li class="flex items-center gap-2"><i class="fas fa-phone-alt text-brand-green"></i> <span>{{ $storeSettings['store_phone'] }}</span></li>
                <li class="flex items-center gap-2"><i class="fas fa-envelope text-brand-green"></i> <span>{{ $storeSettings['store_email'] }}</span></li>
                <li class="flex items-start gap-2"><i class="fas fa-map-marker-alt text-brand-green mt-0.5"></i> <span>{{ $storeSettings['contact_address'] }}</span></li>
            </ul>
        </div>
    </div>

    <div class="max-w-7xl mx-auto pt-6 border-t border-gray-200/60 flex flex-col md:flex-row items-center justify-between gap-4 text-gray-400 text-[11px]">
        <div>&copy; {{ $storeSettings['footer_copyright'] }}</div>
        @if(!empty($storeSettings['footer_fssai']))
        <div class="flex items-center gap-2">
            <div class="w-3 h-3 bg-brand-green-logo rounded-full flex items-center justify-center text-[8px] text-white font-bold">✓</div>
            <span>{{ $storeSettings['footer_fssai'] }}</span>
        </div>
        @endif
        <div class="flex items-center gap-1.5 text-brand-green-logo font-semibold">
            <span class="w-2 h-2 rounded-full border border-brand-green-logo bg-brand-green-logo inline-block"></span>
            <span>{{ $storeSettings['footer_vegetarian'] ?? '100% Vegetarian' }}</span>
        </div>
    </div>

    <p class="text-center text-xs mt-4 opacity-80">Developed by <a href="https://smartindia.ai" target="_blank">smartindia.ai</a> || <a href="https://smartindia.digital" target="_blank">smartindia.digital</a></p>
</footer>
