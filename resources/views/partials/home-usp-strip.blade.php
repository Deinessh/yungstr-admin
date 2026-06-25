@php
$items = $storeSettings['home_usp_strip'] ?? [];
if (is_string($items)) {
    $items = json_decode($items, true) ?: [];
}
@endphp

@if(count($items) > 0)
<section class="bg-cream-section border-y border-gray-100 py-5 px-4">
    <div class="max-w-6xl mx-auto">
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 sm:gap-6">
            @foreach($items as $item)
            <div class="flex flex-col items-center text-center gap-2">
                <div class="home-usp-strip__icon w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-white border shadow-sm flex items-center justify-center text-lg">
                    <i class="{{ $item['icon'] ?? 'fas fa-leaf' }}"></i>
                </div>
                <p class="text-[11px] sm:text-xs font-bold text-brand-chocolate leading-snug">{{ $item['label'] ?? '' }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif
