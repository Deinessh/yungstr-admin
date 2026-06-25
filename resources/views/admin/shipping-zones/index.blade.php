@extends('admin.layout')

@section('title', 'Shipping Zones')
@section('heading', 'Shipping Zones')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="font-display text-2xl text-brand-chocolate">Shipping Zones</h1>
        <p class="text-sm text-gray-500 mt-1">Delivery fees by city, state, or PIN code.</p>
    </div>
    <a href="{{ route('admin.shipping-zones.create') }}" class="btn-primary text-sm w-full sm:w-auto text-center">Add Zone</a>
</div>

@if(session('success'))
    <div class="mb-4 p-4 bg-green-50 text-green-700 rounded-xl border border-green-100">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="mb-4 p-4 bg-red-50 text-red-700 rounded-xl border border-red-100">{{ session('error') }}</div>
@endif

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-cream-bar/60 text-left">
                <tr>
                    <th class="px-4 py-3 font-semibold">Zone</th>
                    <th class="px-4 py-3 font-semibold">Match Type</th>
                    <th class="px-4 py-3 font-semibold">Values</th>
                    <th class="px-4 py-3 font-semibold">Delivery Fee</th>
                    <th class="px-4 py-3 font-semibold">Free Above</th>
                    <th class="px-4 py-3 font-semibold">Status</th>
                    <th class="px-4 py-3 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($zones as $zone)
                <tr>
                    <td class="px-4 py-3 font-medium text-brand-chocolate">
                        {{ $zone->name }}
                        @if($zone->is_default)
                            <span class="ml-2 text-[10px] uppercase tracking-wide bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">Fallback</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-600">
                        @php
                            $typeLabels = \App\Models\ShippingZone::matchTypes();
                            $typeLabel = $typeLabels[$zone->match_type] ?? ($zone->match_type === 'pincode_prefix' ? $typeLabels['pincode'] : $zone->match_type);
                        @endphp
                        {{ $typeLabel }}
                    </td>
                    <td class="px-4 py-3 text-gray-600 max-w-xs">
                        @if($zone->match_values)
                            <span class="text-xs">{{ str_replace("\n", ', ', $zone->match_values) }}</span>
                        @else
                            <span class="text-xs text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">₹{{ number_format($zone->shipping_fee, 0) }}</td>
                    <td class="px-4 py-3">₹{{ number_format($zone->free_shipping_threshold, 0) }}</td>
                    <td class="px-4 py-3">
                        <span class="text-xs font-semibold px-2 py-1 rounded-full {{ $zone->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ $zone->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        <a href="{{ route('admin.shipping-zones.edit', $zone) }}" class="text-brand-orange hover:underline mr-3">Edit</a>
                        @if(!$zone->is_default)
                        <form action="{{ route('admin.shipping-zones.destroy', $zone) }}" method="POST" class="inline" onsubmit="return confirm('Delete this shipping zone?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Delete</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">No shipping zones yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6 rounded-2xl border border-cream-dark bg-cream-bar/40 p-4 text-sm text-gray-700 space-y-2">
    <p class="font-bold text-brand-chocolate">How delivery zones are matched</p>
    <ol class="list-decimal list-inside space-y-1">
        <li><strong>City</strong> — if the customer's city matches a city zone</li>
        <li><strong>State</strong> — state name in admin zones; at checkout state is auto-detected from PIN via India Post API (cached)</li>
        <li><strong>PIN code</strong> — if no state match, check PIN zones (6-digit or prefix)</li>
        <li><strong>Fallback</strong> — all other locations</li>
    </ol>
    <p class="text-xs text-gray-600">Example: Hyderabad city → free above ₹399 · Telangana state → free above ₹599 · Rest of India fallback → free above ₹799.</p>
</div>
@endsection
