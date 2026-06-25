@php
$slideCount = max($heroSlides->count(), 1);
$uspItems = $storeSettings['home_usp_strip'] ?? [];
if (is_string($uspItems)) {
    $uspItems = json_decode($uspItems, true) ?: [];
}
$trustItems = $storeSettings['home_trust_bar'] ?? [];
if (is_string($trustItems)) {
    $trustItems = json_decode($trustItems, true) ?: [];
}
$heroBadge = $storeSettings['home_hero_badge'] ?? "NEW DROP";
$heroTitle = $storeSettings['home_hero_title'] ?? 'Built for the Yungstr';
$heroSubtitle = $storeSettings['home_hero_subtitle'] ?? 'Premium streetwear for those who lead, not follow.';
$heroButtonText = $storeSettings['home_hero_button_text'] ?? 'Shop Collection';
$heroLinks = $heroSlides->isNotEmpty()
    ? $heroSlides->map(fn ($slide) => $slide->button_url ?: '/products')->values()->all()
    : ['/products'];
@endphp

<section
    class="home-hero-split"
    x-data="homeHeroSplit"
    data-slide-count="{{ $slideCount }}"
    data-slide-links='@json($heroLinks)'
    @mouseenter="stopAutoplay()"
    @mouseleave="startAutoplay()"
>
    <div class="home-hero-split__panel">
        <div class="home-hero-split__content">
            <div class="home-hero-split__copy">
                @if($heroBadge)
                <span class="home-hero-split__badge">{{ $heroBadge }}</span>
                @endif

                <h1 class="home-hero-split__title">{{ $heroTitle }}</h1>
                <p class="home-hero-split__subtitle">{{ $heroSubtitle }}</p>

                <a :href="links[active] || '/products'" class="home-hero-split__cta">
                    {{ $heroButtonText }} <i class="fas fa-chevron-right text-[11px] ml-1.5"></i>
                </a>
            </div>

            @if(count($uspItems) > 0)
            <div class="home-hero-split__usp">
                @foreach($uspItems as $item)
                <div class="home-hero-split__usp-item">
                    <div class="home-hero-split__usp-icon">
                        <i class="{{ $item['icon'] ?? 'fas fa-leaf' }}"></i>
                    </div>
                    <p class="home-hero-split__usp-label">{{ $item['label'] ?? '' }}</p>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        <div class="home-hero-split__media-col">
            @if($heroSlides->count() > 0)
                @foreach($heroSlides as $index => $slide)
                @php
                    $mediaUrl = $slide->image ? asset($slide->image) : asset('images/hero/banner-1.png');
                @endphp
                <div x-show="active === {{ $index }}" x-cloak x-transition.opacity class="home-hero-split__slide">
                    <img src="{{ $mediaUrl }}" alt="{{ $heroTitle }}" class="home-hero-split__visual">
                </div>
                @endforeach
            @else
                <div class="home-hero-split__slide">
                    <img src="{{ asset('images/hero/banner-1.png') }}" alt="{{ $heroTitle }}" class="home-hero-split__visual">
                </div>
            @endif

            <div class="home-hero-split__fade" aria-hidden="true"></div>

            @if($heroSlides->count() > 1)
            <div class="home-hero-split__nav">
                <button type="button" @click="prev()" class="home-hero-split__arrow" aria-label="Previous slide"><i class="fas fa-chevron-left"></i></button>
                <div class="flex items-center gap-2">
                    @foreach($heroSlides as $index => $slide)
                    <button type="button" @click="go({{ $index }})" class="home-hero-split__dot" :class="active === {{ $index }} ? 'home-hero-split__dot--active' : ''" aria-label="Slide {{ $index + 1 }}"></button>
                    @endforeach
                </div>
                <button type="button" @click="next()" class="home-hero-split__arrow" aria-label="Next slide"><i class="fas fa-chevron-right"></i></button>
            </div>
            @endif
        </div>
    </div>

    @if(count($trustItems) > 0)
    <div class="home-hero-split__trust">
        <div class="max-w-[1400px] mx-auto px-4 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 py-4 md:py-5">
                @foreach($trustItems as $item)
                <div class="flex items-center gap-2 justify-center lg:justify-start">
                    <div class="home-hero-split__trust-icon w-8 h-8 rounded-full border flex items-center justify-center shrink-0">
                        <i class="{{ $item['icon'] ?? 'fas fa-leaf' }} text-xs"></i>
                    </div>
                    <p class="text-[10px] sm:text-[11px] font-semibold text-brand-chocolate leading-snug">{{ $item['label'] ?? '' }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</section>
