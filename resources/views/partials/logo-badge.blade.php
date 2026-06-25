@props(['size' => 'header'])

@php
    $sizes = [
        'header' => 'h-[4.6rem] md:h-[5.2rem] w-auto',
        'footer' => 'h-[5.75rem] w-auto',
        'auth' => 'h-[6.9rem] w-auto mx-auto',
        'small' => 'h-[2.875rem] w-auto',
        'admin' => 'h-[3.45rem] w-auto',
    ];
    $class = $sizes[$size] ?? $sizes['header'];
    $logoSrc = !empty($storeSettings['logo_path']) ? asset($storeSettings['logo_path']) : asset('images/yungstr-logo.svg');
    $logoAlt = $storeSettings['logo_alt'] ?? ($storeSettings['brand_name'] ?? 'Yungstr Club');
@endphp

@if($size === 'footer')
<div {{ $attributes->merge(['class' => 'space-y-3']) }}>
    <a href="{{ route('home') }}">
        <img src="{{ $logoSrc }}" alt="{{ $logoAlt }}" class="{{ $class }}">
    </a>
    <p class="leading-relaxed text-gray-500 text-xs">{{ $storeSettings['footer_tagline'] ?? 'Premium streetwear for those who lead, not follow.' }}</p>
</div>
@elseif($size === 'admin')
<img src="{{ $logoSrc }}" alt="{{ $logoAlt }}" {{ $attributes->merge(['class' => $class]) }}>
@else
<a href="{{ route('home') }}" {{ $attributes->merge(['class' => 'flex items-center shrink-0']) }}>
    <img src="{{ $logoSrc }}" alt="{{ $logoAlt }}" class="{{ $class }} hover:opacity-90 transition-opacity">
</a>
@endif
