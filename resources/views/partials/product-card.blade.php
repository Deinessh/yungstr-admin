@props(['product', 'index' => 0])

@php
$tag = $product->benefit_tag ?: null;
@endphp

<div class="card p-3 flex flex-col justify-between h-full hover:shadow-md transition min-w-0 relative">
    @php
        $galleryUrls = collect($product->galleryImages())->map(fn ($path) => asset($path))->values()->all();
        if (empty($galleryUrls)) {
            $galleryUrls = [asset($product->image ?: 'images/placeholder-product.svg')];
        }
    @endphp
    <div 
        x-data="{ 
            active: 0, 
            images: @js($galleryUrls), 
            interval: null,
            init() {
                this.start();
            },
            start() {
                if (this.images.length > 1 && !this.interval) {
                    this.interval = setInterval(() => {
                        this.active = (this.active + 1) % this.images.length;
                    }, 3000);
                }
            }
        }"
        class="bg-[#FAF8F2] rounded-xl mb-3 h-48 sm:h-56 w-full flex items-center justify-center relative overflow-hidden"
    >
        <div class="absolute top-2 left-2 z-10 flex flex-col items-start gap-1">
            @if($product->is_coming_soon)
                <span class="product-tag-coming-soon shadow-sm">Coming Soon</span>
            @endif
            @if($product->hasDiscount())
                <span class="text-[10px] font-bold text-white bg-brand-orange px-2 py-1 rounded-md shadow-sm">{{ $product->discountPercent() }}% OFF</span>
            @endif
        </div>
        <template x-for="(img, index) in images" :key="index">
            <img :src="img" x-show="active === index" x-transition.opacity.duration.300ms alt="{{ $product->name }}" class="absolute inset-0 w-full h-full object-cover" onerror="this.src='{{ asset('images/placeholder-product.svg') }}'">
        </template>
        <img src="{{ $galleryUrls[0] }}" x-show="false" alt="{{ $product->name }}" class="w-full h-full object-cover" onerror="this.src='{{ asset('images/placeholder-product.svg') }}'">
    </div>
    <div class="flex flex-col flex-grow min-w-0">
        <a href="{{ route('products.show', $product->slug) }}">
            <h4 class="font-bold text-xs sm:text-sm text-gray-800 line-clamp-2 min-h-[2rem] hover:text-brand-amber text-left">{{ $product->name }}</h4>
        </a>
        <div class="flex items-center gap-0.5 mt-1">
            <i class="fas fa-star text-amber-400 text-[9px]"></i>
            <i class="fas fa-star text-amber-400 text-[9px]"></i>
            <i class="fas fa-star text-amber-400 text-[9px]"></i>
            <i class="fas fa-star text-amber-400 text-[9px]"></i>
            <i class="fas fa-star-half-alt text-amber-400 text-[9px]"></i>
            <span class="text-[9px] text-gray-500 font-semibold ml-1">4.8</span>
        </div>
        @if($tag)
        <span class="product-tag self-start mt-1.5">{{ $tag }}</span>
        @endif

        @if($product->is_coming_soon)
        <div class="flex items-start justify-between gap-2 pt-2 mt-auto">
            <div class="text-left min-w-0">
                <div class="text-sm font-extrabold text-brand-dark leading-tight">₹{{ number_format($product->price, 0) }}</div>
                @if($product->hasDiscount())
                    <div class="text-[10px] sm:text-[11px] text-gray-400 line-through leading-tight">MRP ₹{{ number_format($product->mrp, 0) }}</div>
                @endif
            </div>
        </div>
        <p class="text-[11px] text-slate-600 font-semibold py-2 text-left">Coming Soon</p>
        @elseif($product->isPurchasable())
            @if($product->is_pick_any_combo)
                <div class="flex items-center justify-between gap-2 pt-2 mt-auto">
                    <div class="text-left min-w-0">
                        <div class="text-sm font-extrabold text-brand-dark leading-tight">₹{{ number_format($product->price, 0) }}</div>
                        @if($product->hasDiscount())
                            <div class="text-[10px] sm:text-[11px] text-gray-400 line-through leading-tight">MRP ₹{{ number_format($product->mrp, 0) }}</div>
                        @endif
                    </div>
                </div>
                <a href="{{ route('products.show', $product->slug) }}" class="block w-full bg-brand-orange hover:bg-brand-orange-dark text-white text-[11px] sm:text-xs font-bold py-2.5 rounded-lg shadow-sm transition hover:-translate-y-0.5 hover:shadow-md text-center mt-2">
                    <i class="fas fa-layer-group mr-1"></i> Select 3 Mixes
                </a>
            @else
        <form action="{{ route('cart.add') }}" method="POST" class="mt-auto space-y-2">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <div class="flex items-center justify-between gap-2 pt-2">
                <div class="text-left min-w-0">
                    <div class="text-sm font-extrabold text-brand-dark leading-tight">₹{{ number_format($product->price, 0) }}</div>
                    @if($product->hasDiscount())
                        <div class="text-[10px] sm:text-[11px] text-gray-400 line-through leading-tight">MRP ₹{{ number_format($product->mrp, 0) }}</div>
                    @endif
                </div>
                @include('partials.quantity-adjuster', [
                    'value' => 1,
                    'min' => 1,
                    'max' => $product->hasStockLimit() ? $product->stock : null,
                    'size' => 'sm',
                ])
            </div>
            <button type="submit" class="w-full bg-brand-orange hover:bg-brand-orange-dark text-white text-[11px] sm:text-xs font-bold py-2.5 rounded-lg shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <i class="fas fa-shopping-cart mr-1"></i> Add To Cart
            </button>
        </form>
            @endif
        @else
        <div class="flex items-start justify-between gap-2 pt-2 mt-auto">
            <div class="text-left min-w-0">
                <div class="text-sm font-extrabold text-brand-dark leading-tight">₹{{ number_format($product->price, 0) }}</div>
                @if($product->hasDiscount())
                    <div class="text-[10px] sm:text-[11px] text-gray-400 line-through leading-tight">MRP ₹{{ number_format($product->mrp, 0) }}</div>
                @endif
            </div>
        </div>
        <p class="text-[11px] text-red-600 font-semibold py-2 text-left">Out of Stock</p>
        @endif
    </div>
</div>
