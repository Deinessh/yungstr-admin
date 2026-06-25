@extends('admin.layout')

@section('breadcrumb_parent_url', route('admin.testimonials.index'))
@section('breadcrumb_parent_label', 'Testimonials')

@section('title', $testimonial->exists ? 'Edit Testimonial' : 'Add Testimonial')
@section('heading', $testimonial->exists ? 'Edit Testimonial' : 'Add Testimonial')

@section('content')
<form method="POST" action="{{ $testimonial->exists ? route('admin.testimonials.update', $testimonial) : route('admin.testimonials.store') }}" class="w-full max-w-2xl card p-4 sm:p-6 space-y-4">
    @csrf
    @if($testimonial->exists) @method('PUT') @endif

    <div>
        <label class="block text-sm font-medium mb-1">Customer Name</label>
        <input type="text" name="name" value="{{ old('name', $testimonial->name) }}" required class="input-field">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Role / Location (optional)</label>
        <input type="text" name="role" value="{{ old('role', $testimonial->role) }}" class="input-field" placeholder="Verified Buyer">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Quote</label>
        <textarea name="quote" rows="4" required class="input-field !rounded-2xl">{{ old('quote', $testimonial->quote) }}</textarea>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Rating</label>
            <select name="rating" class="input-field">
                @for($i = 5; $i >= 1; $i--)
                <option value="{{ $i }}" @selected(old('rating', $testimonial->rating ?: 5) == $i)>{{ $i }} stars</option>
                @endfor
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Sort Order</label>
            <input type="number" name="sort_order" value="{{ old('sort_order', $testimonial->sort_order ?? 0) }}" class="input-field">
        </div>
    </div>
    <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $testimonial->exists ? $testimonial->is_active : true))> Show on homepage</label>

    <div class="flex gap-3 pt-2">
        <button type="submit" class="btn-primary">Save</button>
        <a href="{{ route('admin.testimonials.index') }}" class="btn-outline">Cancel</a>
    </div>
</form>
@endsection
