@extends('layouts.master')

@section('content')
@include('partials.home-hero', ['heroSlides' => $heroSlides])

{{-- Bestseller Combos --}}
<section class="py-16 bg-cream-section border-t border-gray-100 px-4">
    <div class="max-w-7xl mx-auto relative">
        <div class="text-center mb-10">
            @include('partials.section-label', ['text' => 'Our Popular Combos'])
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 md:gap-6 px-1 md:px-6">
            @forelse($featuredProducts->take(5) as $index => $product)
                @include('partials.product-card', ['product' => $product, 'index' => $index])
            @empty
                <div class="col-span-full text-center text-gray-500 py-8">No featured products selected yet.</div>
            @endforelse
        </div>

        <div class="text-center mt-8">
            <a href="{{ route('products.index') }}" class="text-brand-green font-semibold hover:text-brand-orange transition">View All Products →</a>
        </div>
    </div>
</section>

@include('partials.home-how-it-works')

{{-- Customer Reviews --}}
<section class="py-16 bg-cream-bar px-4 max-w-7xl mx-auto relative">
    <div class="text-center mb-10">
        @include('partials.section-label', ['text' => 'What Our Customers Say'])
    </div>

    @php
    $gradients = ['from-orange-400 to-amber-500', 'from-blue-400 to-indigo-500', 'from-pink-400 to-rose-500', 'from-green-400 to-emerald-500'];
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @forelse($testimonials as $index => $testimonial)
        <div class="card p-6 text-center space-y-3">
            <div class="flex justify-center text-amber-500 text-sm gap-0.5">
                @for($s = 0; $s < $testimonial->rating; $s++)<i class="fas fa-star"></i>@endfor
            </div>
            <p class="text-xs text-gray-600 italic leading-relaxed">"{{ $testimonial->quote }}"</p>
            <div class="flex items-center justify-center gap-2 pt-2">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-[10px] font-bold text-white bg-gradient-to-tr {{ $gradients[$index % count($gradients)] }}">{{ $testimonial->initials() }}</div>
                <div class="text-left">
                    <h5 class="text-xs font-bold text-gray-800">{{ $testimonial->name }}</h5>
                    <span class="text-[10px] text-brand-green block font-medium">{{ $testimonial->role ?: 'Verified Buyer' }}</span>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center text-gray-500 py-8">Customer testimonials will appear here once added in admin.</div>
        @endforelse
    </div>
</section>

{{-- Shop by Category --}}
<section class="py-16 px-4 max-w-[1400px] mx-auto">
    <div class="text-center mb-10">
        @include('partials.section-label', ['text' => 'Shop by Category'])
        <h2 class="section-subtitle !mt-1 text-brand-dark font-semibold">{{ $storeSettings['home_category_subtitle'] }}</h2>
    </div>

    <div class="home-category-grid grid grid-cols-2 md:grid-cols-5 gap-3 sm:gap-4 lg:gap-5">
        @foreach($categories->take(5) as $category)
        <div class="card p-3 md:p-4 flex flex-col items-center group hover:shadow-md transition min-w-0 h-full">
            <div class="w-full h-40 sm:h-48 rounded-xl mb-3 bg-gradient-to-br from-amber-50 to-orange-100/50 flex items-center justify-center overflow-hidden">
                <img src="{{ asset($category->image) }}" alt="{{ $category->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
            </div>
            <h3 class="font-bold text-[11px] sm:text-xs md:text-sm text-brand-dark mb-3 text-center leading-snug min-h-[2.5rem] flex items-center justify-center px-1">{{ $category->name }}</h3>
            <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="btn-secondary text-[11px] sm:text-xs w-full text-center mt-auto">Shop Now</a>
        </div>
        @endforeach
    </div>
</section>

{{-- Founder Story --}}
<section class="founder-story-section">
    @include('partials.founder-story-banner')
</section>

@include('partials.promo-banner-popup', ['promoBanners' => $promoBanners])
@endsection
