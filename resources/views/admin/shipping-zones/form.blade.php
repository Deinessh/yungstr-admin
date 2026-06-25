@extends('admin.layout')

@section('breadcrumb_parent_url', route('admin.shipping-zones.index'))
@section('breadcrumb_parent_label', 'Shipping Zones')

@section('title', $zone->exists ? 'Edit Shipping Zone' : 'Add Shipping Zone')
@section('heading', $zone->exists ? 'Edit Shipping Zone' : 'Add Shipping Zone')

@section('content')
<h1 class="font-display text-2xl text-brand-chocolate mb-6 sr-only">{{ $zone->exists ? 'Edit Shipping Zone' : 'Add Shipping Zone' }}</h1>

<form method="POST" action="{{ $zone->exists ? route('admin.shipping-zones.update', $zone) : route('admin.shipping-zones.store') }}" class="w-full max-w-2xl card p-4 sm:p-6 space-y-5">
    @csrf
    @if($zone->exists) @method('PUT') @endif

    <div>
        <label class="block text-sm font-medium mb-1">Zone Name</label>
        <input type="text" name="name" value="{{ old('name', $zone->name) }}" required class="input-field" placeholder="Hyderabad">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Match Type</label>
        <select name="match_type" class="input-field" required>
            @foreach($matchTypes as $value => $label)
                <option value="{{ $value }}" @selected(old('match_type', $zone->match_type === 'pincode_prefix' ? 'pincode' : $zone->match_type) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <p class="text-xs text-gray-500 mt-1">At checkout we match in order: <strong>City → State → PIN code</strong>, then fallback.</p>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Match Values</label>
        <textarea name="match_values" rows="4" class="input-field !rounded-2xl" placeholder="Hyderabad&#10;Secunderabad">{{ old('match_values', $zone->match_values) }}</textarea>
        <p class="text-xs text-gray-500 mt-1">One per line or comma-separated.</p>
        <ul class="text-xs text-gray-500 mt-1 list-disc list-inside space-y-0.5">
            <li><strong>City:</strong> Hyderabad, Secunderabad</li>
            <li><strong>State:</strong> Andhra Pradesh, Telangana (PIN prefixes like 533 also work)</li>
            <li><strong>PIN:</strong> 533401 or prefix 533</li>
        </ul>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Standard Delivery Fee (₹)</label>
            <input type="number" step="0.01" min="0" name="shipping_fee" value="{{ old('shipping_fee', $zone->shipping_fee) }}" required class="input-field">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Free Shipping Above (₹)</label>
            <input type="number" step="0.01" min="0" name="free_shipping_threshold" value="{{ old('free_shipping_threshold', $zone->free_shipping_threshold) }}" required class="input-field">
        </div>
    </div>

    <div class="flex flex-wrap gap-6">
        <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $zone->is_active))> Active</label>
        <label class="flex items-center gap-2"><input type="checkbox" name="is_default" value="1" @checked(old('is_default', $zone->is_default))> Fallback zone (all other locations)</label>
    </div>

    <div class="flex gap-3">
        <button type="submit" class="btn-primary">Save Zone</button>
        <a href="{{ route('admin.shipping-zones.index') }}" class="btn-outline">Cancel</a>
    </div>
</form>
@endsection
