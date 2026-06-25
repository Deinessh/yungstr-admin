@extends('admin.layout')

@section('breadcrumb_parent_url', route('admin.promo-banners.index'))
@section('breadcrumb_parent_label', 'Promo Banners')

@section('title', $banner->exists ? 'Edit Promo Banner' : 'Add Promo Banner')
@section('heading', $banner->exists ? 'Edit Promo Banner' : 'Add Promo Banner')

@section('content')
<form method="POST" enctype="multipart/form-data" action="{{ $banner->exists ? route('admin.promo-banners.update', $banner) : route('admin.promo-banners.store') }}" class="w-full max-w-2xl card p-4 sm:p-6 space-y-4">
    @csrf
    @if($banner->exists) @method('PUT') @endif

    <div class="rounded-2xl border border-cream-dark bg-cream-bar/30 p-4 text-sm text-gray-600 space-y-1">
        <p class="font-semibold text-brand-chocolate">Promo popup banner guidelines</p>
        <p>Popup appears <strong>5 seconds</strong> after the homepage loads. Upload a square or portrait promo graphic.</p>
        <p>• Recommended resolution: <strong>800×800 px</strong> (square) or <strong>800×1000 px</strong> (portrait).</p>
        <p>• Minimum: <strong>600×600 px</strong>. Keep text and offers centred — the popup scales down on mobile.</p>
        <p>• Format: JPG, PNG, or WebP · Max 5 MB.</p>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Title (admin reference only)</label>
        <input type="text" name="title" value="{{ old('title', $banner->title) }}" class="input-field" placeholder="Summer Sale">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Click-through Link (optional)</label>
        <input type="text" name="link_url" value="{{ old('link_url', $banner->link_url) }}" class="input-field" placeholder="/products">
        <p class="text-xs text-gray-500 mt-1">When set, clicking the banner image opens this URL.</p>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Sort Order</label>
        <input type="number" name="sort_order" value="{{ old('sort_order', $banner->sort_order ?? 0) }}" class="input-field">
        <p class="text-xs text-gray-500 mt-1">Lower numbers appear first when multiple banners are active.</p>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Banner Image @unless($banner->exists)<span class="text-red-500">*</span>@endunless</label>
        @if($banner->image)
            <img src="{{ asset($banner->image) }}" alt="" class="max-w-xs max-h-48 object-contain rounded-xl mb-2 border border-amber-100 bg-cream-bar/30 p-2">
        @endif
        <input type="file" name="image" accept="image/jpeg,image/png,image/jpg,image/webp" class="block w-full text-sm" @unless($banner->exists) required @endunless>
        <p class="text-xs text-gray-500 mt-1">Recommended: <strong>800×800 px</strong> or <strong>800×1000 px</strong> · JPG/PNG/WebP · Max 5 MB.</p>
    </div>

    <label class="flex items-center gap-2 text-sm">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $banner->is_active ?? true))> Active
    </label>

    <button class="btn-primary">Save Banner</button>
</form>
@endsection
