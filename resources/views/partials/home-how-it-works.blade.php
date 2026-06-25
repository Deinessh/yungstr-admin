@php
$steps = $storeSettings['home_how_it_works'] ?? [];
if (is_string($steps)) {
    $steps = json_decode($steps, true) ?: [];
}
$stepIcons = ['fas fa-blender', 'fas fa-clock', 'fas fa-pan-frying'];
$stepImages = [
    0 => 'images/how-it-works/add-water.png',
    2 => 'images/how-it-works/cook-dosa-idly.png',
];
@endphp

@if(count($steps) > 0)
<section class="py-14 px-4 bg-cream-section border-t border-gray-100">
    <div class="max-w-5xl mx-auto">
        <div class="text-center mb-10">
            @include('partials.section-label', ['text' => 'How It Works'])
            <h2 class="section-subtitle !mt-1 text-brand-dark font-semibold">Healthy breakfast in 3 easy steps</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($steps as $index => $step)
            @php
                $iconClass = $step['icon'] ?? ($stepIcons[$index] ?? 'fas fa-leaf');
                $imagePath = $step['image'] ?? ($stepImages[$index] ?? null);
                if (! $imagePath && str_contains($iconClass, 'blender')) {
                    $imagePath = $stepImages[0];
                }
                if (! $imagePath && (str_contains($iconClass, 'pan-frying') || str_contains($iconClass, 'fire-burner'))) {
                    $imagePath = $stepImages[2];
                }
            @endphp
            <div class="card p-6 text-center">
                <div class="w-12 h-12 mx-auto mb-4 rounded-full bg-brand-green text-white flex items-center justify-center overflow-hidden shrink-0">
                    @if($imagePath)
                        <img
                            src="{{ asset($imagePath) }}"
                            alt=""
                            class="w-8 h-8 object-contain"
                            width="32"
                            height="32"
                            loading="lazy"
                            decoding="async"
                        >
                    @else
                        <i class="{{ $iconClass }} text-lg" aria-hidden="true"></i>
                    @endif
                </div>
                <h3 class="font-bold text-brand-dark mb-2">{{ $step['title'] ?? '' }}</h3>
                <p class="text-sm text-gray-600 leading-relaxed">{{ $step['desc'] ?? '' }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif
