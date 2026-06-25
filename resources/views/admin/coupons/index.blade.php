@extends('admin.layout')

@section('title', 'Coupons')
@section('heading', 'Coupons')

@section('content')
<div class="flex flex-col sm:flex-row sm:justify-end gap-3 mb-6">
    <a href="{{ route('admin.coupons.create') }}" class="btn-primary text-sm w-full sm:w-auto text-center">Create Coupon</a>
</div>
<div class="card overflow-x-auto -mx-4 sm:mx-0 rounded-none sm:rounded-2xl border-x-0 sm:border-x">
    <table class="w-full text-sm min-w-[760px]">
        <thead class="text-left">
            <tr>
                <th class="px-4 py-3">Coupon Code</th>
                <th class="px-4 py-3">Coupon Name</th>
                <th class="px-4 py-3">Type</th>
                <th class="px-4 py-3">Value</th>
                <th class="px-4 py-3">Uses</th>
                <th class="px-4 py-3">Expiry</th>
                <th class="px-4 py-3">Active</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($coupons as $coupon)
            <tr>
                <td class="px-4 py-3 font-semibold text-brand-orange">
                    {{ $coupon->code }}
                    @if($coupon->is_system)
                        <span class="ml-1 text-[10px] uppercase bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full">Default</span>
                    @endif
                </td>
                <td class="px-4 py-3">{{ $coupon->name }}</td>
                <td class="px-4 py-3">{{ $coupon->type === 'percent' ? 'Percent' : 'Fixed' }}</td>
                <td class="px-4 py-3">{{ $coupon->type === 'percent' ? rtrim(rtrim(number_format($coupon->value, 2), '0'), '.').'%' : '₹'.number_format($coupon->value, 0) }}</td>
                <td class="px-4 py-3">{{ $coupon->used_count }}@if($coupon->max_uses)/{{ $coupon->max_uses }}@endif</td>
                <td class="px-4 py-3 text-xs">
                    @if($coupon->expires_at)
                        <span class="{{ $coupon->isExpired() ? 'text-red-600' : 'text-gray-600' }}">{{ $coupon->expiryLabel() }}</span>
                    @else
                        <span class="text-brand-green">No expiry</span>
                    @endif
                </td>
                <td class="px-4 py-3">
                    @if($coupon->is_active)
                        <span class="text-brand-green font-medium">Yes</span>
                    @else
                        <span class="text-gray-400 font-medium">Disabled</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-right whitespace-nowrap">
                    <a href="{{ route('admin.coupons.edit', $coupon) }}" class="text-brand-orange hover:underline mr-3">Edit</a>
                    <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" class="inline" onsubmit="return confirm('Delete coupon {{ $coupon->code }}?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="px-4 py-8 text-center text-gray-500">No coupons yet. Create one to get started.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $coupons->links() }}</div>
@endsection
