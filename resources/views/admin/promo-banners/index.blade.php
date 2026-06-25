@extends('admin.layout')

@section('title', 'Promo Banners')
@section('heading', 'Homepage Promo Banners')

@section('content')
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
    <div class="text-sm text-gray-500 space-y-1">
        <p>Upload promotional banners shown as a popup on the homepage after 5 seconds. Visitors can close the popup; it stays hidden for the rest of their session.</p>
        <p class="text-xs">Upload size: <strong>800×800 px</strong> or <strong>800×1000 px</strong> (min. 600×600 px).</p>
    </div>
    <a href="{{ route('admin.promo-banners.create') }}" class="btn-primary text-sm w-full sm:w-auto text-center">Add Banner</a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
    @forelse($banners as $banner)
    <div class="card overflow-hidden">
        <div class="h-48 bg-cream-bar/40 flex items-center justify-center overflow-hidden p-3">
            <img src="{{ asset($banner->image) }}" alt="{{ $banner->title ?: 'Promo banner' }}" class="max-h-full max-w-full object-contain">
        </div>
        <div class="p-4 space-y-2 text-sm">
            <div class="flex justify-between gap-2">
                <p class="font-bold">{{ $banner->title ?: 'Untitled banner' }}</p>
                <span class="text-xs {{ $banner->is_active ? 'text-brand-orange' : 'text-gray-400' }}">{{ $banner->is_active ? 'Active' : 'Hidden' }}</span>
            </div>
            @if($banner->link_url)
                <p class="text-xs text-gray-500 truncate">Link: {{ $banner->link_url }}</p>
            @endif
            <p class="text-xs text-gray-500">Order: {{ $banner->sort_order }}</p>
            <div class="flex gap-3 pt-2">
                <a href="{{ route('admin.promo-banners.edit', $banner) }}" class="text-brand-orange hover:underline">Edit</a>
                <form action="{{ route('admin.promo-banners.destroy', $banner) }}" method="POST" onsubmit="return confirm('Delete this banner?')">
                    @csrf @method('DELETE')
                    <button class="text-red-600 hover:underline">Delete</button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full card p-8 text-center text-gray-500">No promo banners yet. Add one to show a popup on the homepage.</div>
    @endforelse
</div>
@endsection
