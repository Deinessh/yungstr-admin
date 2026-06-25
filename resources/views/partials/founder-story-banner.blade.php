@once
@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@600;700&display=swap" rel="stylesheet">
@endpush
@endonce

@php
    $founderPhoto = $storeSettings['founder_photo_path'] ?? null;
    if (! $founderPhoto && file_exists(public_path('images/founder.jpg'))) {
        $founderPhoto = 'images/founder.jpg';
    } elseif (! $founderPhoto && file_exists(public_path('images/founder.png'))) {
        $founderPhoto = 'images/founder.png';
    }
    $founderPhotoSrc = $founderPhoto ? asset($founderPhoto) : asset('images/founder-photo-placeholder.svg');
    $founderPhotoClass = $founderPhoto ? 'founder-story-banner__photo' : 'founder-story-banner__photo founder-story-banner__photo--placeholder';

    $illustrationPath = $storeSettings['founder_illustration_path'] ?? 'images/founder-kitchen-illustration.svg';
    $ctaUrl = $storeSettings['founder_cta_url'] ?? route('about').'#founder-story';
    if ($ctaUrl && ! str_starts_with($ctaUrl, 'http') && ! str_starts_with($ctaUrl, '/')) {
        $ctaUrl = '/'.ltrim($ctaUrl, '/');
    }
@endphp

<div class="founder-story-banner">
    <img src="{{ asset('images/founder-corner-leaves-left.svg') }}" alt="" class="founder-story-banner__corner founder-story-banner__corner--left" aria-hidden="true">
    <img src="{{ asset('images/founder-corner-leaves-right.svg') }}" alt="" class="founder-story-banner__corner founder-story-banner__corner--right" aria-hidden="true">

    <div class="founder-story-banner__inner">
        <div class="founder-story-banner__portrait">
            <div class="founder-story-banner__photo-wrap">
                <img src="{{ $founderPhotoSrc }}" alt="{{ $storeSettings['brand_name'] }} Founder" class="{{ $founderPhotoClass }}">

                <div class="founder-story-banner__badge">
                    <span class="founder-story-banner__badge-title">{!! nl2br(e($storeSettings['founder_badge_title'] ?? "Founder\n& Mother")) !!}</span>
                    <svg class="founder-story-banner__badge-heart" viewBox="0 0 16 14" aria-hidden="true"><path d="M8 13S1 8.5 1 4.5a3.2 3.2 0 0 1 5.6-2.1A3.2 3.2 0 0 1 15 4.5C15 8.5 8 13 8 13Z" fill="none" stroke="currentColor" stroke-width="1.3"/></svg>
                </div>
            </div>

            <div class="founder-story-banner__ribbon">
                {{ $storeSettings['founder_ribbon'] }}
            </div>
        </div>

        <div class="founder-story-banner__content">
            <h2 class="founder-story-banner__heading">
                <span class="founder-story-banner__heading-script">{{ $storeSettings['founder_heading_script'] }}</span>
                <span class="founder-story-banner__heading-bold">{{ $storeSettings['founder_heading_bold'] }}</span>
            </h2>

            <p class="founder-story-banner__text">{{ $storeSettings['founder_body'] }}</p>

            <ul class="founder-story-banner__features">
                @foreach(array_filter([$storeSettings['founder_feature_1'] ?? null, $storeSettings['founder_feature_2'] ?? null, $storeSettings['founder_feature_3'] ?? null]) as $feature)
                <li>
                    <svg viewBox="0 0 20 20" aria-hidden="true"><path d="M10 16S3 11.5 3 7.5a3.5 3.5 0 0 1 6-2.2A3.5 3.5 0 0 1 17 7.5C17 11.5 10 16 10 16Z" fill="none" stroke="currentColor" stroke-width="1.2"/></svg>
                    <span>{{ $feature }}</span>
                </li>
                @endforeach
            </ul>

            <a href="{{ $ctaUrl }}" class="founder-story-banner__cta">
                {{ $storeSettings['founder_cta_text'] ?? 'Read Our Story' }}
            </a>
        </div>

        <div class="founder-story-banner__illustration">
            <img src="{{ asset($illustrationPath) }}" alt="" class="founder-story-banner__illustration-art" aria-hidden="true">
            <p class="founder-story-banner__signature">
                <span class="founder-story-banner__signature-script">{{ $storeSettings['founder_signature_label'] ?? '— Founder' }}</span>
                <span class="founder-story-banner__signature-brand">{{ $storeSettings['founder_signature_brand'] ?? $storeSettings['brand_name'] }}</span>
            </p>
        </div>
    </div>
</div>

@if($showFounderNote ?? false)
<blockquote class="founder-story-note">
    <p>{{ $storeSettings['founder_quote_note'] }}</p>
</blockquote>
@endif
