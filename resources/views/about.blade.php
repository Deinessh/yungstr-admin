@extends('layouts.master')

@section('content')
<section class="page-hero">
    <div class="max-w-7xl mx-auto px-4 lg:px-12 text-center">
        <h1 class="text-4xl md:text-5xl font-extrabold text-brand-dark mb-6">{{ $storeSettings['about_hero_title'] }}</h1>
        <p class="text-lg text-gray-600 max-w-3xl mx-auto leading-relaxed">{{ $storeSettings['about_hero_subtitle'] }}</p>
    </div>
</section>

<section class="py-16 px-4 max-w-7xl mx-auto">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
        <div>@include('partials.hero-visual')</div>
        <div>
            <h2 class="text-3xl font-bold text-brand-dark mb-6">{{ $storeSettings['about_journey_title'] }}</h2>
            <p class="text-gray-600 mb-4 leading-relaxed">{{ $storeSettings['about_journey_p1'] }}</p>
            <p class="text-gray-600 mb-6 leading-relaxed">{{ $storeSettings['about_journey_p2'] }}</p>
            <ul class="space-y-3">
                @foreach($storeSettings['about_journey_bullets'] ?? [] as $item)
                <li class="flex items-center gap-3">
                    <i class="fas fa-check text-brand-green"></i>
                    <span class="text-brand-dark font-medium">{{ $item }}</span>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</section>

<section id="founder-story" class="founder-story-section scroll-mt-24">
    @include('partials.founder-story-banner', ['showFounderNote' => true])
</section>

<section class="py-16 bg-amber-50/30 px-4 max-w-7xl mx-auto">
    <div class="text-center mb-10">
        @include('partials.section-label', ['text' => 'Our Core Values'])
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        @foreach($storeSettings['about_core_values'] ?? [] as $value)
        <div class="card p-8 text-center">
            <div class="w-14 h-14 bg-brand-green-soft rounded-full flex items-center justify-center mx-auto mb-6 text-2xl">{{ $value['emoji'] ?? '✨' }}</div>
            <h3 class="text-xl font-bold text-brand-dark mb-3">{{ $value['title'] ?? '' }}</h3>
            <p class="text-gray-600 text-sm">{{ $value['desc'] ?? '' }}</p>
        </div>
        @endforeach
    </div>
</section>
@endsection
