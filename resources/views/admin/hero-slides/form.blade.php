@extends('admin.layout')

@section('breadcrumb_parent_url', route('admin.hero-slides.index'))
@section('breadcrumb_parent_label', 'Hero Slides')

@section('title', $slide->exists ? 'Edit Slide' : 'Add Slide')
@section('heading', $slide->exists ? 'Edit Hero Slide' : 'Add Hero Slide')

@section('content')
<form method="POST" enctype="multipart/form-data" action="{{ $slide->exists ? route('admin.hero-slides.update', $slide) : route('admin.hero-slides.store') }}" class="w-full max-w-2xl card p-4 sm:p-6 space-y-4">
    @csrf
    @if($slide->exists) @method('PUT') @endif

    <div class="rounded-xl bg-cream-bar/60 border border-cream-dark p-3 text-xs text-gray-600">
        <p>Hero text, badge, and feature icons are shared across all slides. Edit them in <a href="{{ route('admin.settings.edit', ['tab' => 'home']) }}" class="text-brand-orange font-semibold hover:underline">Settings → Home page</a>.</p>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Slide Image {{ $slide->exists ? '' : '*' }}</label>
        @if($slide->image)
            <img src="{{ asset($slide->image) }}" alt="" class="w-full max-w-md h-32 object-cover rounded-xl mb-2 border border-amber-100">
        @endif
        <input type="file" name="image" accept="image/jpeg,image/png,image/jpg,image/webp" class="block w-full text-sm" {{ $slide->exists ? '' : 'required' }}>
        <div class="mt-2 rounded-xl bg-cream-bar/60 border border-cream-dark p-3 text-xs text-gray-600 space-y-1">
            <p class="font-semibold text-brand-chocolate">Hero image guidelines (right 60% panel)</p>
            <p>• Recommended: <strong>1200 × 520 px</strong> landscape · any similar ratio auto-fits</p>
            <p>• Format: JPG, PNG, or WebP · Max 5 MB</p>
            <p>• Tall product packs and wide banners scale automatically — nothing is cropped</p>
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Button Link</label>
        <input type="text" name="button_url" value="{{ old('button_url', $slide->button_url) }}" class="input-field" placeholder="/products">
        <p class="text-xs text-gray-500 mt-1">Where the orange button goes when this slide is showing.</p>
    </div>

    <button class="btn-primary">Save Slide</button>
</form>
@endsection
