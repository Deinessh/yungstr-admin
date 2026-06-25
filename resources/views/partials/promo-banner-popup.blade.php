@if($promoBanners->isNotEmpty())
@php
    $bannerPayload = $promoBanners->map(fn ($banner) => [
        'image' => asset($banner->image),
        'link' => $banner->link_url ?: null,
        'title' => $banner->title ?: 'Promotion',
    ])->values();
@endphp

<div
    x-data="{
        show: false,
        active: 0,
        banners: @js($bannerPayload),
        get total() { return this.banners.length; },
        init() {
            if (this.total === 0 || sessionStorage.getItem('s7_promo_banner_dismissed')) return;
            setTimeout(() => { this.show = true; }, 5000);
        },
        close() {
            this.show = false;
            sessionStorage.setItem('s7_promo_banner_dismissed', '1');
        },
        next() { this.active = (this.active + 1) % this.total; },
        prev() { this.active = (this.active - 1 + this.total) % this.total; },
    }"
    x-show="show"
    x-cloak
    class="promo-banner-popup"
    role="dialog"
    aria-modal="true"
    aria-label="Promotion"
    @keydown.escape.window="close()"
>
    <div class="promo-banner-popup__backdrop" @click="close()" aria-hidden="true"></div>

    <div class="promo-banner-popup__panel">
        <button type="button" class="promo-banner-popup__close" @click="close()" aria-label="Close promotion">
            <i class="fas fa-times"></i>
        </button>

        <template x-for="(banner, index) in banners" :key="index">
            <div x-show="active === index" class="promo-banner-popup__slide">
                <template x-if="banner.link">
                    <a :href="banner.link" class="promo-banner-popup__link" @click="close()">
                        <img :src="banner.image" :alt="banner.title" class="promo-banner-popup__image">
                    </a>
                </template>
                <template x-if="!banner.link">
                    <img :src="banner.image" :alt="banner.title" class="promo-banner-popup__image">
                </template>
            </div>
        </template>

        <template x-if="total > 1">
            <div class="promo-banner-popup__nav">
                <button type="button" class="promo-banner-popup__arrow" @click="prev()" aria-label="Previous banner">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <div class="promo-banner-popup__dots">
                    <template x-for="(banner, index) in banners" :key="'dot-' + index">
                        <button
                            type="button"
                            class="promo-banner-popup__dot"
                            :class="active === index ? 'promo-banner-popup__dot--active' : ''"
                            @click="active = index"
                            :aria-label="'Go to banner ' + (index + 1)"
                        ></button>
                    </template>
                </div>
                <button type="button" class="promo-banner-popup__arrow" @click="next()" aria-label="Next banner">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </template>
    </div>
</div>
@endif
