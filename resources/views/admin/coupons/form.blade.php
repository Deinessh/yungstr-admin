@extends('admin.layout')

@section('breadcrumb_parent_url', route('admin.coupons.index'))
@section('breadcrumb_parent_label', 'Coupons')

@section('title', $coupon->exists ? 'Edit Coupon' : 'Create Coupon')
@section('heading', $coupon->exists ? 'Edit Coupon' : 'Create Coupon')

@section('content')
<form method="POST" action="{{ $coupon->exists ? route('admin.coupons.update', $coupon) : route('admin.coupons.store') }}" class="w-full max-w-xl card p-4 sm:p-6 space-y-4">
    @csrf
    @if($coupon->exists) @method('PUT') @endif

    @if($coupon->exists && $coupon->is_system)
    <div class="p-4 bg-amber-50 border border-amber-100 rounded-xl text-sm text-amber-900">
        This is a default coupon. Uncheck <strong>Active</strong> to disable it at checkout, or delete it from the coupons list.
    </div>
    @endif

    <div>
        <label class="block text-sm font-medium mb-1">Coupon Code</label>
        <input type="text" name="code" value="{{ old('code', $coupon->code) }}" required class="input-field uppercase" placeholder="e.g. WELCOME50">
        <p class="text-xs text-gray-500 mt-1">Customers enter this code at checkout. Stored in uppercase.</p>
        @error('code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Coupon Name</label>
        <input type="text" name="name" value="{{ old('name', $coupon->name) }}" required class="input-field" placeholder="e.g. Welcome Discount">
        <p class="text-xs text-gray-500 mt-1">Display name for admin reference. You can change this anytime.</p>
        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Discount Type</label>
            <select name="type" class="input-field">
                <option value="fixed" @selected(old('type', $coupon->type) === 'fixed')>Fixed (₹)</option>
                <option value="percent" @selected(old('type', $coupon->type) === 'percent')>Percent (%)</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Discount Value</label>
            <input type="number" step="0.01" name="value" value="{{ old('value', $coupon->value) }}" required class="input-field">
        </div>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Minimum Order (₹)</label>
            <input type="number" step="0.01" name="min_order_amount" value="{{ old('min_order_amount', $coupon->min_order_amount) }}" class="input-field">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Max Uses</label>
            <input type="number" name="max_uses" value="{{ old('max_uses', $coupon->max_uses) }}" class="input-field" placeholder="Unlimited">
        </div>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Expiry Date (optional)</label>
        <input type="date" name="expires_at" value="{{ old('expires_at', optional($coupon->expires_at)->format('Y-m-d')) }}" class="input-field">
        <p class="text-xs text-gray-500 mt-1">Leave empty to keep the coupon active continuously. If set, the coupon works through the end of that date.</p>
    </div>
    <div>
        <input type="hidden" name="is_active" value="0">
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $coupon->is_active ?? true)) class="rounded border-gray-300 text-brand-orange focus:ring-brand-orange">
            <span>Active (uncheck to disable this coupon at checkout)</span>
        </label>
    </div>
    <div class="flex flex-col sm:flex-row gap-3 pt-2">
        <button type="submit" class="btn-primary">Save Coupon</button>
        @if($coupon->exists)
            <a href="{{ route('admin.coupons.index') }}" class="btn-outline text-center">Cancel</a>
            <button type="submit"
                    form="delete-coupon-form"
                    class="sm:ml-auto text-red-600 hover:text-red-700 font-semibold text-sm px-3 py-2"
                    onclick="return confirm('Delete this coupon permanently?')">Delete Coupon</button>
        @endif
    </div>
</form>

@if($coupon->exists)
    <form id="delete-coupon-form"
          action="{{ route('admin.coupons.destroy', $coupon) }}"
          method="POST"
          class="hidden">
        @csrf
        @method('DELETE')
    </form>
@endif
@endsection
