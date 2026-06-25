@extends('admin.layout')

@section('title', 'Hero Slides')
@section('heading', 'Homepage Hero Slides')

@section('content')
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
    <div class="text-sm text-gray-500 space-y-1">
        <p>Upload images for the right hero panel only. Text and icons are shared on every slide.</p>
        <p class="text-xs">Recommended image: <strong>1200 × 520 px</strong> for the right 60% panel · Edit hero text in <a href="{{ route('admin.settings.edit', ['tab' => 'home']) }}" class="text-brand-orange font-semibold hover:underline">Settings → Home page</a>.</p>
    </div>
    <a href="{{ route('admin.hero-slides.create') }}" class="btn-primary text-sm w-full sm:w-auto text-center">Add Slide</a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
    @forelse($slides as $slide)
    <div class="card overflow-hidden">
        <div class="h-40 bg-amber-50/50 flex items-center justify-center overflow-hidden">
            @if($slide->image)
                <img src="{{ asset($slide->image) }}" alt="" class="w-full h-full object-cover">
            @else
                <span class="text-gray-400 text-sm">No image</span>
            @endif
        </div>
        <div class="p-4 space-y-2 text-sm">
            <div class="flex justify-between gap-2">
                <p class="font-bold truncate">{{ $slide->button_url ?: '/products' }}</p>
                <span class="text-xs {{ $slide->is_active ? 'text-brand-orange' : 'text-gray-400' }} shrink-0">{{ $slide->is_active ? 'Active' : 'Hidden' }}</span>
            </div>
            <p class="text-xs text-gray-500">Order: {{ $slide->sort_order }}</p>
            <div class="flex gap-3 pt-2">
                <a href="{{ route('admin.hero-slides.edit', $slide) }}" class="text-brand-orange hover:underline">Edit</a>
                <form action="{{ route('admin.hero-slides.destroy', $slide) }}" method="POST" onsubmit="return confirm('Delete slide?')">
                    @csrf @method('DELETE')
                    <button class="text-red-600 hover:underline">Delete</button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full card p-8 text-center text-gray-500">No slides yet. Add one to enable the homepage slider.</div>
    @endforelse
</div>
@endsection
