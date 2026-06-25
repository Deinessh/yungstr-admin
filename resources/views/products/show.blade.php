@extends('layouts.master')

@section('content')
<div class="max-w-7xl mx-auto px-4 lg:px-12 py-12">
    <div class="card overflow-hidden mb-16">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-16">
            @php
                $galleryUrls = collect($product->galleryImages())
                    ->map(fn ($path) => asset($path))
                    ->values()
                    ->all();

                if ($galleryUrls === []) {
                    $galleryUrls = [asset($product->image ?: 'images/placeholder-product.svg')];
                }
            @endphp
            <div class="p-4 sm:p-6 md:p-8 lg:p-12 bg-gradient-to-br from-amber-50 to-orange-100/50" x-data="productGallery({ images: @js($galleryUrls) })">
                @if($product->video)
                <div class="mb-4">
                    <video src="{{ asset($product->video) }}" class="w-full max-w-sm mx-auto rounded-2xl shadow-md" controls playsinline></video>
                </div>
                @endif

                {{-- Mobile: swipeable image carousel (Amazon-style) --}}
                <div class="md:hidden -mx-4 sm:-mx-6">
                    <div
                        x-ref="mobileTrack"
                        class="flex overflow-x-auto snap-x snap-mandatory scrollbar-hide touch-pan-x"
                        aria-label="Product images"
                    >
                        @foreach($galleryUrls as $index => $galleryUrl)
                        <div class="w-full shrink-0 snap-center flex items-center justify-center min-h-[360px] px-4">
                            <img
                                src="{{ $galleryUrl }}"
                                alt="{{ $product->name }} — image {{ $index + 1 }}"
                                class="w-full max-h-[420px] object-contain drop-shadow-md select-none"
                                draggable="false"
                                loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
                            >
                        </div>
                        @endforeach
                    </div>

                    @if(count($galleryUrls) > 1)
                    <div class="flex items-center justify-center gap-3 px-4 pt-3 pb-1">
                        <div class="flex items-center gap-1.5" role="tablist" aria-label="Choose product image">
                            @foreach($galleryUrls as $index => $galleryUrl)
                            <button
                                type="button"
                                @click="goTo({{ $index }})"
                                class="h-2 rounded-full transition-all duration-200"
                                :class="active === {{ $index }} ? 'w-5 bg-brand-orange' : 'w-2 bg-gray-300'"
                                :aria-label="'Image {{ $index + 1 }}'"
                                :aria-selected="active === {{ $index }}"
                            ></button>
                            @endforeach
                        </div>
                        <span class="text-xs text-gray-500 tabular-nums" x-text="(active + 1) + ' / ' + images.length"></span>
                    </div>
                    @endif
                </div>

                {{-- Desktop: main image + thumbnails --}}
                <div class="hidden md:block">
                    <div class="flex items-center justify-center min-h-[400px]">
                        <img :src="images[active]" alt="{{ $product->name }}" class="w-full max-w-md object-contain drop-shadow-md">
                    </div>
                    @if(count($galleryUrls) > 1)
                    <div class="flex flex-wrap justify-center gap-2 mt-4">
                        @foreach($galleryUrls as $index => $galleryUrl)
                        <button type="button" @click="active = {{ $index }}" class="w-14 h-14 rounded-lg border-2 overflow-hidden p-1 bg-white" :class="active === {{ $index }} ? 'border-brand-orange' : 'border-transparent'">
                            <img src="{{ $galleryUrl }}" alt="" class="w-full h-full object-contain">
                        </button>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            <div class="p-8 lg:p-12 flex flex-col justify-center">
                <div class="flex flex-wrap items-center gap-2 mb-4">
                    @if($product->is_coming_soon)
                    <span class="product-tag-coming-soon">Coming Soon</span>
                    @endif
                    @if($product->benefit_tag)
                    <span class="product-tag">{{ $product->benefit_tag }}</span>
                    @endif
                    @if($product->is_best_seller)
                    <span class="inline-block bg-brand-green text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wide">Best Seller</span>
                    @endif
                    @if($product->is_hot)
                    <span class="inline-block bg-brand-orange text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wide">Hot Pick</span>
                    @endif
                    @if($product->category)
                    <span class="inline-block bg-brand-green-soft text-brand-green text-[10px] font-bold px-3 py-1 rounded-full">{{ $product->category->name }}</span>
                    @endif
                </div>
                <h1 class="text-3xl md:text-4xl font-extrabold text-brand-dark mb-4">{{ $product->name }}</h1>
                <div class="flex flex-wrap items-baseline gap-3 mb-2">
                    <div class="text-3xl font-extrabold text-brand-orange">₹{{ number_format($product->price, 0) }}</div>
                    @if($product->hasDiscount())
                        <div class="text-lg text-gray-400 line-through">MRP ₹{{ number_format($product->mrp, 0) }}</div>
                        <span class="text-xs font-bold uppercase text-white bg-brand-orange px-2.5 py-1 rounded-full">{{ $product->discountPercent() }}% OFF</span>
                        <span class="text-xs font-semibold text-brand-green">Save ₹{{ number_format($product->discountAmount(), 0) }}</span>
                    @endif
                </div>
                @if($product->comboProducts->isNotEmpty())
                    <div class="mb-6">
                        <h2 class="text-sm font-bold text-brand-dark mb-3">This combo includes</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($product->comboProducts as $included)
                                <a href="{{ route('products.show', $included->slug) }}" class="flex items-center gap-3 rounded-2xl border border-amber-100 bg-amber-50/40 p-3 hover:border-brand-orange/40 transition">
                                    <div class="w-14 h-16 bg-white rounded-xl flex items-center justify-center overflow-hidden p-1 shrink-0">
                                        <img src="{{ asset($included->image ?: 'images/placeholder-product.svg') }}" alt="{{ $included->name }}" class="max-h-full max-w-full object-contain">
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-brand-dark text-sm leading-snug">{{ $included->name }}</p>
                                        @if($included->weight)
                                            <p class="text-xs text-gray-500 mt-0.5">{{ $included->weight }}</p>
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @elseif($product->combo_includes)
                    <p class="text-sm text-gray-600 mb-6"><strong>Includes:</strong> {{ $product->combo_includes }}</p>
                @else
                    <div class="mb-6"></div>
                @endif

                <div class="border-t border-gray-200 pt-8 mt-auto">
                    @if($product->is_coming_soon)
                    <p class="text-sm text-slate-600 font-semibold flex items-center gap-2">
                        <i class="fas fa-clock"></i> Coming Soon — available to order shortly
                    </p>
                    @elseif($product->isPurchasable())
                    <form
                        action="{{ route('cart.add') }}"
                        method="POST"
                        class="space-y-4"
                        @if($product->is_pick_any_combo)
                        x-data="{
                            qty: {{ max(1, (int) old('quantity', 1)) }},
                            maxQty: {{ $product->hasStockLimit() ? (int) $product->stock : 99 }},
                            setIndexes() {
                                return Array.from({ length: this.qty }, (_, index) => index);
                            }
                        }"
                        @endif
                    >
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                        @if($product->is_pick_any_combo)
                            <div class="rounded-2xl border border-amber-100 bg-amber-50/50 p-4 space-y-4">
                                <p class="text-sm font-semibold text-brand-dark">Select exactly 3 different single products for each combo</p>
                                <p class="text-xs text-gray-500">Each product can only be selected once per combo.</p>
                                <template x-for="setIndex in setIndexes()" :key="setIndex">
                                    <div x-data="pickAnyUniqueSelects()" class="rounded-xl border border-amber-100 bg-white p-4 space-y-3">
                                        <p class="text-sm font-bold text-brand-dark" x-show="qty > 1" x-text="'Combo ' + (setIndex + 1) + ' — choose 3 different products'"></p>
                                        <p class="text-sm font-bold text-brand-dark" x-show="qty <= 1">Choose exactly 3 different products</p>
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                            @for($choice = 0; $choice < 3; $choice++)
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-600 mb-1">Product {{ $choice + 1 }}</label>
                                                <select
                                                    class="input-field text-sm pick-any-select"
                                                    required
                                                    x-bind:name="'pick_any[{{ $product->id }}][' + setIndex + '][{{ $choice }}]'"
                                                >
                                                    <option value="">Select product</option>
                                                    @foreach($selectableProducts as $selectable)
                                                        <option value="{{ $selectable->id }}">{{ $selectable->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @endfor
                                        </div>
                                    </div>
                                </template>
                            </div>
                        @endif

                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
                            @if($product->is_pick_any_combo)
                                <div class="inline-flex items-center border border-gray-200 rounded-full overflow-hidden bg-white h-12 min-w-[8rem]">
                                    <input type="hidden" name="quantity" :value="qty">
                                    <button type="button" @click="if (qty > 1) qty--" class="w-10 h-full text-xl text-brand-dark hover:bg-amber-50 font-bold transition">−</button>
                                    <span class="w-full text-center text-brand-dark font-semibold select-none" x-text="qty"></span>
                                    <button type="button" @click="if (qty < maxQty) qty++" class="w-10 h-full text-xl text-brand-dark hover:bg-amber-50 font-bold transition">+</button>
                                </div>
                            @else
                                @include('partials.quantity-adjuster', [
                                    'value' => 1,
                                    'min' => 1,
                                    'max' => $product->hasStockLimit() ? $product->stock : null,
                                ])
                            @endif
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <button type="submit" class="btn-primary flex-1 h-12 flex items-center justify-center gap-2">
                                <i class="fas fa-shopping-basket"></i> Add to Cart
                            </button>
                        </div>
                    </form>
                    <p class="text-sm text-gray-500 mt-4 flex items-center gap-2">
                        <i class="fas fa-check text-brand-green"></i> In Stock
                    </p>
                    @else
                    <p class="text-sm text-red-600 font-medium">Out of Stock</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card p-6 sm:p-8 mb-16">
        <h2 class="text-2xl font-bold text-brand-dark mb-2">Product Details</h2>
        <p class="text-sm text-gray-500 mb-8">Complete information about this product</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Top left: Description --}}
            <div class="rounded-2xl border border-amber-100 bg-white overflow-hidden flex flex-col h-full">
                <h3 class="text-lg font-bold text-brand-dark px-5 py-4 flex items-center gap-2 border-b border-amber-100 bg-amber-50/40">
                    <i class="fas fa-align-left text-brand-orange"></i> Description
                </h3>
                <div class="p-5 flex-1 text-gray-700 leading-relaxed text-sm sm:text-base whitespace-pre-line bg-gradient-to-br from-amber-50/50 to-orange-50/20">
                    {{ $product->description ?: 'No description added yet.' }}
                </div>
            </div>

            {{-- Top right: Product Information --}}
            <div class="rounded-2xl border border-amber-100 bg-white overflow-hidden flex flex-col h-full">
                <h3 class="text-lg font-bold text-brand-dark px-5 py-4 flex items-center gap-2 border-b border-amber-100 bg-amber-50/40">
                    <i class="fas fa-info-circle text-brand-orange"></i> Product Information
                </h3>
                <div class="flex-1 overflow-hidden">
                    <table class="w-full text-sm h-full">
                        <tbody class="divide-y divide-amber-100">
                            <tr class="bg-amber-50/30">
                                <td class="px-4 py-3 text-gray-600 font-medium w-2/5">Product Name</td>
                                <td class="px-4 py-3 font-semibold text-brand-dark text-right">{{ $product->name }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 text-gray-600 font-medium">Category</td>
                                <td class="px-4 py-3 font-semibold text-brand-dark text-right">{{ $product->category?->name ?? '—' }}</td>
                            </tr>
                            <tr class="bg-amber-50/30">
                                <td class="px-4 py-3 text-gray-600 font-medium">Weight</td>
                                <td class="px-4 py-3 font-semibold text-brand-dark text-right">{{ $product->weight ?: '—' }}</td>
                            </tr>
                            <tr class="bg-amber-50/30">
                                <td class="px-4 py-3 text-gray-600 font-medium">Quantity Available</td>
                                <td class="px-4 py-3 font-semibold text-brand-dark text-right">
                                    @if($product->hasStockLimit())
                                        {{ number_format($product->stock) }} units
                                    @else
                                        Unlimited
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 text-gray-600 font-medium">Benefit Tag</td>
                                <td class="px-4 py-3 font-semibold text-brand-dark text-right">{{ $product->benefit_tag ?: '—' }}</td>
                            </tr>
                            <tr class="bg-amber-50/30">
                                <td class="px-4 py-3 text-gray-600 font-medium">MRP</td>
                                <td class="px-4 py-3 font-semibold text-brand-dark text-right">{{ $product->mrp ? '₹'.number_format($product->mrp, 0) : '—' }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 text-gray-600 font-medium">Selling Price</td>
                                <td class="px-4 py-3 font-bold text-brand-orange text-right">₹{{ number_format($product->price, 0) }}</td>
                            </tr>
                            @if($product->hasDiscount())
                            <tr class="bg-amber-50/30">
                                <td class="px-4 py-3 text-gray-600 font-medium">You Save</td>
                                <td class="px-4 py-3 font-semibold text-brand-green text-right">{{ $product->discountPercent() }}% off (₹{{ number_format($product->discountAmount(), 0) }})</td>
                            </tr>
                            @endif
                            @if($product->combo_includes)
                            <tr class="bg-amber-50/30">
                                <td class="px-4 py-3 text-gray-600 font-medium">Includes</td>
                                <td class="px-4 py-3 font-semibold text-brand-dark text-right text-xs leading-relaxed">{{ $product->combo_includes }}</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Bottom left: Key Benefits --}}
            <div class="rounded-2xl border border-amber-100 bg-white overflow-hidden flex flex-col h-full">
                <h3 class="text-lg font-bold text-brand-dark px-5 py-4 flex items-center gap-2 border-b border-amber-100 bg-amber-50/40">
                    <i class="fas fa-check-circle text-brand-orange"></i> Key Benefits
                </h3>
                <div class="flex-1 overflow-hidden">
                    @if(is_array($product->key_benefits) && count($product->key_benefits) > 0)
                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-amber-100">
                            @foreach($product->key_benefits as $index => $benefit)
                            <tr class="{{ $index % 2 === 0 ? 'bg-amber-50/30' : '' }}">
                                <td class="px-4 py-3 text-brand-green w-8"><i class="fas fa-leaf"></i></td>
                                <td class="px-4 py-3 text-gray-700">{{ $benefit }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="px-4 py-8 text-center text-gray-400 text-sm h-full flex items-center justify-center bg-amber-50/20">No key benefits added yet.</div>
                    @endif
                </div>
            </div>

            {{-- Bottom right: Nutritional Information --}}
            <div class="rounded-2xl border border-amber-100 bg-white overflow-hidden flex flex-col h-full">
                <h3 class="text-lg font-bold text-brand-dark px-5 py-4 flex items-center gap-2 border-b border-amber-100 bg-amber-50/40">
                    <i class="fas fa-apple-alt text-brand-orange"></i> Nutritional Information
                    <span class="text-sm font-normal text-gray-500">(per 100g)</span>
                </h3>
                <div class="flex-1 overflow-hidden">
                    @if(is_array($product->nutrition_info) && count($product->nutrition_info) > 0)
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-amber-50/80">
                                <th class="px-4 py-3 text-left text-brand-brown font-bold uppercase text-[11px] tracking-wide">Nutrient</th>
                                <th class="px-4 py-3 text-right text-brand-brown font-bold uppercase text-[11px] tracking-wide">Value</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-amber-100">
                            @foreach($product->nutrition_info as $index => $row)
                            <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-amber-50/30' }}">
                                <td class="px-4 py-3 text-gray-600">{{ $row['label'] ?? '—' }}</td>
                                <td class="px-4 py-3 font-semibold text-brand-dark text-right">{{ $row['value'] ?? '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="px-4 py-8 text-center text-gray-400 text-sm h-full flex items-center justify-center bg-amber-50/20">No nutritional information added yet.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($relatedProducts->count() > 0)
    <div class="mt-20">
        @include('partials.section-label', ['text' => 'Related Products'])
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-8">
            @foreach($relatedProducts as $index => $related)
                @include('partials.product-card', ['product' => $related, 'index' => $index])
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
