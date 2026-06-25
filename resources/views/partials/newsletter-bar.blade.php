<div class="bg-brand-chocolate text-cream px-4 py-6 md:py-8 rounded-t-3xl">
    <div class="max-w-7xl mx-auto flex flex-col lg:flex-row items-center justify-between gap-6">
        <div class="flex items-center gap-4 text-center lg:text-left">
            <div class="text-3xl text-brand-orange-logo hidden sm:block"><i class="fas fa-envelope-open-text"></i></div>
            <h3 class="font-bold text-base md:text-lg leading-tight">{{ $storeSettings['newsletter_heading'] ?? 'Stay Updated with Healthy Recipes & Offers!' }}</h3>
        </div>

        <form action="#" method="POST" class="w-full lg:w-auto flex flex-col sm:flex-row gap-2 max-w-xl" onsubmit="event.preventDefault(); alert('Thank you for subscribing!'); this.reset();">
            <input type="email" required placeholder="Enter your email address" class="px-4 py-2.5 rounded-full text-black text-sm outline-none w-full sm:w-80">
            <button type="submit" class="btn-primary text-sm px-6 py-2.5 whitespace-nowrap">Subscribe</button>
        </form>

        <div class="flex items-center gap-3 text-lg">
            <span class="text-xs font-semibold mr-1">Follow Us</span>
            <a href="{{ $storeSettings['social_instagram'] ?? '#' }}" class="hover:text-brand-orange-logo" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
            <a href="{{ $storeSettings['social_facebook'] ?? '#' }}" class="hover:text-brand-orange-logo" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
            <a href="{{ $storeSettings['social_youtube'] ?? '#' }}" class="hover:text-brand-orange-logo" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
            <a href="https://wa.me/{{ $storeSettings['contact_whatsapp'] ?? '918978605003' }}" target="_blank" rel="noopener noreferrer" class="hover:text-brand-orange-logo" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
        </div>
    </div>
</div>
