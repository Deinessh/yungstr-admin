@php
    $announcementItems = [
        [
            'icon' => 'fas fa-truck',
            'iconClass' => 'text-[10px]',
            'text' => ! empty($storeSettings['show_global_free_shipping_banner'])
                ? ($storeSettings['announcement_1'] ?? 'FREE Shipping on Orders Over ₹100')
                : ($storeSettings['announcement_1'] ?? 'Street Approved Styles'),
        ],
        [
            'icon' => 'fas fa-shield-halved',
            'iconClass' => 'text-[10px]',
            'text' => $storeSettings['announcement_2'] ?? 'Premium Quality Heavyweight Blends',
        ],
        [
            'icon' => 'fas fa-fire',
            'iconClass' => 'text-[11px]',
            'text' => $storeSettings['announcement_3'] ?? 'Free Shipping on Orders Above ₹100',
        ],
    ];
    $announcementItems = array_values(array_filter($announcementItems, fn ($item) => filled($item['text'] ?? null)));

    $shippingZones = \App\Models\ShippingZone::where('is_active', true)->orderBy('is_default')->orderBy('id')->get();
@endphp

@if(count($announcementItems) > 0)
<div class="announcement-bar text-xs py-2.5 px-4 relative flex items-center justify-center">
    <div
        class="flex-1 max-w-[calc(100%-100px)] sm:max-w-none"
        x-data="announcementTicker"
        data-item-count="{{ count($announcementItems) }}"
    >
        {{-- Desktop: show all lines --}}
        <div class="hidden md:flex flex-wrap justify-center items-center gap-6 lg:gap-12">
            @foreach($announcementItems as $item)
                <div class="flex items-center gap-1.5 shrink-0">
                    <i class="{{ $item['icon'] }} {{ $item['iconClass'] }}" aria-hidden="true"></i>
                    <span>{{ $item['text'] }}</span>
                </div>
            @endforeach
        </div>

        {{-- Mobile: auto-sliding ticker --}}
        <div class="md:hidden relative overflow-hidden h-5" aria-live="polite">
            @foreach($announcementItems as $index => $item)
                <div
                    x-show="active === {{ $index }}"
                    x-cloak
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-x-6"
                    x-transition:enter-end="opacity-100 translate-x-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-x-0"
                    x-transition:leave-end="opacity-0 -translate-x-6"
                    class="absolute inset-0 flex items-center justify-center gap-1.5 px-2 text-center"
                    @if($index === 0) aria-hidden="false" @endif
                >
                    <i class="{{ $item['icon'] }} {{ $item['iconClass'] }} shrink-0" aria-hidden="true"></i>
                    <span class="truncate">{{ $item['text'] }}</span>
                </div>
            @endforeach
        </div>
    </div>
    
    <div x-data class="absolute right-4 top-1/2 -translate-y-1/2 z-10 hidden sm:block">
        <button @click="$dispatch('open-delivery-modal')" class="underline font-bold hover:text-white transition">Check Benefits</button>
    </div>
</div>
@endif

{{-- Delivery Rates Modal --}}
<div x-data="{ open: false }" 
     @open-delivery-modal.window="open = true" 
     @keydown.escape.window="open = false" 
     style="display: none;" 
     x-show="open" 
     class="fixed inset-0 z-[100] overflow-y-auto"
>
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div x-show="open" x-transition.opacity class="fixed inset-0 transition-opacity bg-gray-900/60" @click="open = false"></div>
        <div x-show="open" x-transition class="relative inline-block w-full max-w-md p-6 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl sm:my-8 text-brand-dark">
            <button @click="open = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
            <h3 class="text-xl font-bold text-brand-dark mb-4 border-b pb-2">Delivery & Benefits</h3>
            <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-2 custom-scrollbar">
                @foreach($shippingZones as $zone)
                <div class="flex items-start gap-3">
                    <i class="fas fa-map-marker-alt text-brand-orange text-lg mt-0.5 w-5 text-center"></i>
                    <div>
                        <p class="font-bold">{{ $zone->name }}</p>
                        <p class="text-sm text-gray-600">
                            @if($zone->shipping_fee == 0)
                                Free Delivery
                            @else
                                Delivery Fee: ₹{{ number_format($zone->shipping_fee, 0) }}
                            @endif
                            @if($zone->free_shipping_threshold > 0)
                                <span class="block text-brand-green font-medium mt-0.5">Free on orders above ₹{{ number_format($zone->free_shipping_threshold, 0) }}</span>
                            @endif
                        </p>
                    </div>
                </div>
                @endforeach

                <div class="flex items-start gap-3 pt-3 border-t border-gray-100">
                    <i class="fas fa-bolt text-brand-orange text-lg mt-0.5 w-5 text-center"></i>
                    <div>
                        <p class="font-bold">Fast Dispatch</p>
                        <p class="text-sm text-gray-600">Orders are usually dispatched within 24 hours.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <i class="fas fa-shield-alt text-brand-orange text-lg mt-0.5 w-5 text-center"></i>
                    <div>
                        <p class="font-bold">Secure Packaging</p>
                        <p class="text-sm text-gray-600">100% safe & tamper-proof delivery.</p>
                    </div>
                </div>
            </div>
            <div class="mt-6">
                <button @click="open = false" class="w-full btn-primary py-2 rounded-xl text-center font-bold">Got it</button>
            </div>
        </div>
    </div>
</div>
