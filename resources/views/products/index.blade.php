@extends('layouts.master')

@section('content')
<section class="page-hero">
    <div class="max-w-7xl mx-auto px-4 lg:px-12 text-center">
        @include('partials.section-label', ['text' => 'Catalogue'])
        <h1 class="section-title">Browse our premium streetwear collection</h1>
        <p class="mt-3 text-gray-600 max-w-2xl mx-auto">Premium streetwear drops designed for comfort and style — filter by category, price, or shop our best sellers and hot picks.</p>
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 lg:px-12 py-12">
    <div class="flex flex-col lg:flex-row gap-8">
        <aside class="lg:w-64 shrink-0">
            <form method="GET" class="card p-5 space-y-5 sticky top-24">
                <div>
                    <label for="search" class="block text-sm font-semibold text-brand-dark mb-2">Search</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Search products..." class="input-field text-sm">
                </div>

                <div>
                    <p class="text-sm font-semibold text-brand-dark mb-2">Category</p>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm">
                            <input type="radio" name="category" value="" @checked(!request('category')) onchange="this.form.submit()">
                            All Products
                        </label>
                        @foreach($categories as $category)
                        <label class="flex items-center gap-2 text-sm">
                            <input type="radio" name="category" value="{{ $category->id }}" @checked(request('category') == $category->id) onchange="this.form.submit()">
                            {{ $category->name }}
                        </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <p class="text-sm font-semibold text-brand-dark mb-2">Highlights</p>
                    <label class="flex items-center gap-2 text-sm mb-2">
                        <input type="checkbox" name="best_seller" value="1" @checked(request('best_seller'))>
                        Best Sellers
                    </label>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="hot" value="1" @checked(request('hot'))>
                        Hot Picks
                    </label>
                </div>

                @if($maxPrice > 0)
                <div>
                    <label for="max_price" class="block text-sm font-semibold text-brand-dark mb-2">Max Price: ₹{{ request('max_price', $maxPrice) }}</label>
                    <input type="range" name="max_price" id="max_price" min="50" max="{{ $maxPrice }}" step="10" value="{{ request('max_price', $maxPrice) }}" class="w-full accent-brand-orange" oninput="this.previousElementSibling.textContent = 'Max Price: ₹' + this.value">
                </div>
                @endif

                <div>
                    <label for="sort" class="block text-sm font-semibold text-brand-dark mb-2">Sort By</label>
                    <select name="sort" id="sort" class="input-field text-sm" onchange="this.form.submit()">
                        <option value="featured" @selected(request('sort', 'featured') === 'featured')>Featured</option>
                        <option value="price_asc" @selected(request('sort') === 'price_asc')>Price: Low to High</option>
                        <option value="price_desc" @selected(request('sort') === 'price_desc')>Price: High to Low</option>
                        <option value="name" @selected(request('sort') === 'name')>Name A–Z</option>
                    </select>
                </div>

                <div class="flex flex-col gap-2">
                    <button type="submit" class="btn-primary text-sm py-2.5">Apply Filters</button>
                    <a href="{{ route('products.index') }}" class="btn-outline text-sm py-2.5 text-center">Reset</a>
                </div>
            </form>
        </aside>

        <div class="flex-1 min-w-0">
            @if(request()->hasAny(['search', 'category', 'best_seller', 'hot', 'max_price', 'sort']))
            <div class="flex flex-wrap items-center gap-2 mb-6">
                <span class="text-sm text-gray-600">Active filters:</span>
                @if(request('search'))<span class="text-xs bg-amber-100 text-brand-dark px-3 py-1 rounded-full">Search: {{ request('search') }}</span>@endif
                @if($activeCategory)<span class="text-xs bg-amber-100 text-brand-dark px-3 py-1 rounded-full">{{ $activeCategory->name }}</span>@endif
                @if(request('best_seller'))<span class="text-xs bg-amber-100 text-brand-dark px-3 py-1 rounded-full">Best Sellers</span>@endif
                @if(request('hot'))<span class="text-xs bg-amber-100 text-brand-dark px-3 py-1 rounded-full">Hot Picks</span>@endif
            </div>
            @endif

            @if($products->count())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
                @foreach($products as $index => $product)
                    @include('partials.product-card', ['product' => $product, 'index' => $index])
                @endforeach
            </div>
            <div class="mt-12 flex justify-center">{{ $products->links() }}</div>
            @else
            <div class="card p-12 text-center">
                <p class="text-gray-500 mb-4">No products match your filters.</p>
                <a href="{{ route('products.index') }}" class="btn-primary inline-flex">View All Products</a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
