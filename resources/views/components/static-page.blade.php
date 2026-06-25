@props(['title', 'subtitle' => null])

<section class="page-hero">
    <div class="max-w-7xl mx-auto px-4 lg:px-12 text-center">
        <h1 class="text-4xl md:text-5xl font-extrabold text-brand-dark mb-4">{{ $title }}</h1>
        @if($subtitle)
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">{{ $subtitle }}</p>
        @endif
    </div>
</section>

<div class="bg-cream py-16 px-4 max-w-7xl mx-auto">
    <div class="max-w-3xl mx-auto card p-8 md:p-12 policy-content">
        {{ $slot }}
    </div>
</div>
