@extends('admin.layout')

@section('breadcrumb_parent_url', route('admin.products.index'))
@section('breadcrumb_parent_label', 'Products')

@section('title', $product->exists ? 'Edit Product' : 'Add Product')
@section('heading', $product->exists ? 'Edit Product' : 'Add Product')

@section('content')
@php
    $pickAnyCategoryId = $categories->firstWhere('slug', 'pick-any-3-combo')?->id;
    $selectedCategoryId = old('category_id', $product->category_id);
    $isPickAnyProduct = $selectedCategoryId && (int) $selectedCategoryId === (int) $pickAnyCategoryId;
@endphp

<form method="POST" enctype="multipart/form-data" action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}" class="max-w-3xl space-y-6">
    @csrf
    @if($product->exists) @method('PUT') @endif

    <div class="card p-4 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="md:col-span-2">
            <label class="block text-sm font-medium mb-1">Name</label>
            <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="input-field">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Category</label>
            <select name="category_id" class="input-field">
                <option value="">None</option>
                @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
            @if($isPickAnyProduct)
                <p class="text-xs text-amber-800 bg-amber-50 border border-amber-100 rounded-xl px-3 py-2 mt-2">Pick Any 3 category — customers choose 3 single products at checkout (no manual toggle needed).</p>
            @endif
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">MRP (₹)</label>
            <input type="number" step="0.01" name="mrp" value="{{ old('mrp', $product->mrp) }}" class="input-field" placeholder="169">
            <p class="text-xs text-gray-500 mt-1">Shown struck-through on the storefront when higher than selling price.</p>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Price (₹)</label>
            <input type="number" step="0.01" name="price" value="{{ old('price', $product->price) }}" required class="input-field">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Stock</label>
            <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" min="0" class="input-field" placeholder="Leave empty for unlimited">
            <p class="text-xs text-gray-500 mt-1">Leave blank for unlimited stock. If set, customers cannot order more than this quantity.</p>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Benefit Tag</label>
            <input type="text" name="benefit_tag" value="{{ old('benefit_tag', $product->benefit_tag) }}" placeholder="High Fiber" class="input-field">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Weight (display)</label>
            <input type="text" name="weight" value="{{ old('weight', $product->weight) }}" placeholder="500g" class="input-field">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Shipping Weight (kg)</label>
            <input type="number" step="0.01" min="0.01" name="weight_kg" value="{{ old('weight_kg', $product->weight_kg ?? 0.5) }}" class="input-field">
            <p class="text-xs text-gray-500 mt-1">Used for Velocity automatic shipping weight calculation.</p>
        </div>

        @include('admin.partials.image-upload', ['current' => $product->image, 'label' => 'Primary Product Image'])

        <div class="md:col-span-2">
            <label class="block text-sm font-medium mb-2">Additional Gallery Images</label>
            @if($product->exists && $product->images->count())
            <div class="flex flex-wrap gap-3 mb-3">
                @foreach($product->images as $image)
                <div class="relative">
                    <img src="{{ asset($image->path) }}" alt="" class="w-24 h-24 object-cover rounded-xl border border-amber-100">
                    <button type="submit"
                            form="delete-gallery-image-{{ $image->id }}"
                            class="absolute -top-2 -right-2 w-6 h-6 rounded-full bg-red-500 text-white text-xs leading-none hover:bg-red-600"
                            title="Remove"
                            onclick="return confirm('Remove this gallery image?')">×</button>
                </div>
                @endforeach
            </div>
            @endif
            <input type="file" name="gallery[]" accept="image/jpeg,image/png,image/jpg,image/webp" multiple class="block w-full text-sm">
            <div class="mt-2 rounded-xl bg-cream-bar/60 border border-cream-dark p-3 text-xs text-gray-600 space-y-1">
                <p class="font-semibold text-brand-chocolate">Image upload guidelines</p>
                <p>• Upload <strong>any number</strong> of gallery images — no limit.</p>
                <p>• Recommended resolution: <strong>1200×1200 px</strong> (minimum 800×800 px).</p>
                <p>• Format: JPG, PNG, or WebP · Max 5 MB per image.</p>
                <p>• Keep consistent lighting and square crop for best storefront display.</p>
            </div>
        </div>

        <div class="md:col-span-2">
            <label class="block text-sm font-medium mb-1">Product Video (optional)</label>
            @if($product->video)
                <video src="{{ asset($product->video) }}" class="w-full max-w-sm rounded-xl mb-2" controls></video>
            @endif
            <input type="file" name="video" accept="video/mp4,video/webm,video/quicktime" class="block w-full text-sm">
            <p class="text-xs text-gray-500 mt-1">MP4, WEBM, or MOV. Shown on product page if uploaded.</p>
        </div>

        <div class="md:col-span-2">
            <label class="block text-sm font-medium mb-1">Description</label>
            <textarea name="description" rows="4" class="input-field !rounded-2xl">{{ old('description', $product->description) }}</textarea>
        </div>
        @unless($isPickAnyProduct)
        <div class="md:col-span-2">
            <label class="block text-sm font-medium mb-1">Products in this Combo</label>
            <p class="text-xs text-gray-500 mb-2">Select only <strong>single-product packs</strong> included in this combo (other combos are not listed). Customers will see these on the product detail page, cart, and invoices.</p>
            @php
                $selectedComboIds = old('combo_product_ids', $product->comboProducts->pluck('id')->all());
            @endphp
            <select name="combo_product_ids[]" multiple size="8" class="input-field !rounded-2xl min-h-[180px]">
                @foreach($comboSelectableProducts as $comboProduct)
                    <option value="{{ $comboProduct->id }}" @selected(in_array($comboProduct->id, $selectedComboIds))>
                        {{ $comboProduct->name }}@if($comboProduct->weight) — {{ $comboProduct->weight }}@endif
                    </option>
                @endforeach
            </select>
            <p class="text-xs text-gray-500 mt-1">Hold Ctrl/Cmd to select multiple single products. Leave empty to use the text summary below only.</p>
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium mb-1">Combo Summary Text (optional fallback)</label>
            <textarea name="combo_includes" rows="2" class="input-field !rounded-2xl" placeholder="Multi Millet Dosa Mix – 200g + Foxtail Millet Dosa Mix – 200g">{{ old('combo_includes', $product->combo_includes) }}</textarea>
            <p class="text-xs text-gray-500 mt-1">Used only if no products are selected above.</p>
        </div>
        @else
        <div class="md:col-span-2">
            <label class="block text-sm font-medium mb-1">Combo description (optional)</label>
            <textarea name="combo_includes" rows="2" class="input-field !rounded-2xl" placeholder="Customer selects any 3 single breakfast mixes (200g each)">{{ old('combo_includes', $product->combo_includes) }}</textarea>
            <p class="text-xs text-gray-500 mt-1">Shown on the product page. Product choices are made by the customer at checkout.</p>
        </div>
        @endunless
        <div class="md:col-span-2">
            <label class="block text-sm font-medium mb-1">Key Benefits (one per line)</label>
            <textarea name="key_benefits" rows="4" class="input-field !rounded-2xl">{{ old('key_benefits', is_array($product->key_benefits) ? implode("\n", $product->key_benefits) : '') }}</textarea>
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium mb-1">Nutrition Info (Label: Value per line)</label>
            <textarea name="nutrition_info" rows="4" class="input-field !rounded-2xl" placeholder="Energy: 320 kcal">{{ old('nutrition_info', is_array($product->nutrition_info) ? collect($product->nutrition_info)->map(fn($n) => ($n['label'] ?? '').': '.($n['value'] ?? ''))->implode("\n") : '') }}</textarea>
        </div>
        <div class="flex flex-col sm:flex-row flex-wrap items-start sm:items-center gap-4 md:col-span-2">
            <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active ?? true))> Active</label>
            <label class="flex items-center gap-2"><input type="checkbox" name="is_coming_soon" value="1" @checked(old('is_coming_soon', $product->is_coming_soon))> Coming Soon</label>
            <label class="flex items-center gap-2"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product->is_featured))> Show in Best Sellers (homepage)</label>
            <label class="flex items-center gap-2"><input type="checkbox" name="is_best_seller" value="1" @checked(old('is_best_seller', $product->is_best_seller))> Best Seller badge</label>
            <label class="flex items-center gap-2"><input type="checkbox" name="is_hot" value="1" @checked(old('is_hot', $product->is_hot))> Hot Pick badge</label>
        </div>
        <p class="text-xs text-gray-500 md:col-span-2 -mt-2">Coming Soon products are visible on the shop but show a &ldquo;Coming Soon&rdquo; tag and cannot be added to cart.</p>
        <div>
            <label class="block text-sm font-medium mb-1">Featured Sort Order</label>
            <input type="number" name="featured_sort" value="{{ old('featured_sort', $product->featured_sort ?? 0) }}" class="input-field">
            <p class="text-xs text-gray-500 mt-1">Lower numbers appear first. Max 6 on homepage.</p>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row gap-3">
        <button type="submit" class="btn-primary w-full sm:w-auto text-center">Save Product</button>
        <a href="{{ route('admin.products.index') }}" class="btn-outline w-full sm:w-auto text-center">Cancel</a>
    </div>
</form>

@if($product->exists)
    @foreach($product->images as $image)
        <form id="delete-gallery-image-{{ $image->id }}"
              action="{{ route('admin.products.images.destroy', [$product, $image]) }}"
              method="POST"
              class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endforeach
@endif
@endsection
