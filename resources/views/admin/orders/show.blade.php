@extends('admin.layout')

@section('breadcrumb_parent_url', route('admin.orders.index'))
@section('breadcrumb_parent_label', 'Orders')

@section('title', 'Order #'.$order->id)
@section('heading', 'Order #'.str_pad($order->id, 6, '0', STR_PAD_LEFT))

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 card p-6 space-y-4">
        <h3 class="font-bold">Items</h3>
        @foreach($order->items as $item)
        <div class="flex flex-col sm:flex-row sm:justify-between gap-1 border-b border-gray-100 pb-3">
            <div class="min-w-0">
                <p class="font-semibold break-words">{{ $item->displayName() }}</p>
                <p class="text-xs text-gray-500">Qty {{ $item->quantity }} × ₹{{ number_format($item->price, 0) }}@if($item->displayMrp()) · MRP ₹{{ number_format($item->displayMrp(), 0) }}@endif</p>
                @if($item->displayComboIncludes())
                    <p class="text-xs text-gray-500 mt-1">Includes: {{ $item->displayComboIncludes() }}</p>
                @endif
                @if($item->displayPickAnySelections())
                    <p class="text-xs text-brand-green mt-1 whitespace-pre-line"><strong>Selected mixes:</strong><br>{{ $item->displayPickAnySelections() }}</p>
                @endif
            </div>
            <p class="font-bold shrink-0">₹{{ number_format($item->price * $item->quantity, 0) }}</p>
        </div>
        @endforeach
        <div class="pt-2 space-y-1 text-sm">
            <div class="flex justify-between"><span>Subtotal</span><span>₹{{ number_format($order->subtotal, 0) }}</span></div>
            <div class="flex justify-between"><span>Shipping</span><span>₹{{ number_format($order->shipping_fee, 0) }}</span></div>
            <div class="flex justify-between"><span>Discount</span><span>-₹{{ number_format($order->discount_amount, 0) }}</span></div>
            <div class="flex justify-between font-bold text-lg"><span>Total</span><span class="text-brand-orange">₹{{ number_format($order->total_amount, 0) }}</span></div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="card p-6 space-y-3 text-sm">
            <h3 class="font-bold">Customer</h3>
            <p>{{ $order->shipping_name ?: $order->user->name }}</p>
            <p>{{ $order->user->email }}</p>
            <p>{{ $order->contact_number }}</p>
            @if($order->delivery_date)
            <p><strong>Delivery date:</strong> {{ $order->delivery_date->format('M d, Y') }}</p>
            @endif
            @if($order->customer_notes)
            <p><strong>Order notes:</strong><br><span class="text-gray-600 whitespace-pre-line">{{ $order->customer_notes }}</span></p>
            @endif
            <p class="text-gray-600 whitespace-pre-line break-words">{{ $order->shipping_address }}</p>
            @if($order->shipping_city)
            <p>{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_pincode }}</p>
            @endif
        </div>

        <div class="card p-6 space-y-3 text-sm">
            <h3 class="font-bold">Payment</h3>
            <p>Method: {{ strtoupper($order->payment_method ?? 'N/A') }}</p>
            <p>Status: {{ $order->payment_status }}</p>
            @if($order->coupon_code)<p>Coupon: {{ $order->coupon_code }}</p>@endif
            @if($order->referral_code_used)<p>Referral: {{ $order->referral_code_used }}</p>@endif
            @if($order->invoice_number)
            <p>Invoice: <strong>{{ $order->invoice_number }}</strong></p>
            @elseif($order->canAccessInvoice())
            <p class="text-gray-500">Invoice will be generated when you open print or download.</p>
            @endif
            @if($order->canAccessInvoice())
            <div class="pt-1">
                @include('partials.invoice-actions', [
                    'order' => $order,
                    'downloadRoute' => 'admin.orders.invoice',
                    'printRoute' => 'admin.orders.invoice.print',
                ])
            </div>
            @endif
        </div>

        <div class="card p-6 space-y-3 text-sm">
            <h3 class="font-bold">Shipping (Velocity)</h3>
            <p>Status: <span class="font-semibold">{{ ucfirst(str_replace('_', ' ', $order->shipping_status ?? 'pending')) }}</span></p>
            @if($order->awb_code)
                <p>AWB: <strong>{{ $order->awb_code }}</strong></p>
                <p>Courier: {{ $order->carrier_name ?? '—' }}</p>
                @if($order->tracking_url)
                    <a href="{{ $order->tracking_url }}" target="_blank" rel="noopener" class="text-brand-orange font-medium">Track shipment →</a>
                @endif
                @if($order->label_url)
                    <a href="{{ $order->label_url }}" target="_blank" rel="noopener" class="block text-brand-orange font-medium">Download shipping label →</a>
                @endif
            @endif
            @if($order->shipping_error)
                <p class="text-red-600 text-xs">{{ $order->shipping_error }}</p>
            @endif
            <div class="flex flex-col gap-2 pt-2">
                @if($order->awb_code)
                <form method="POST" action="{{ route('admin.orders.sync-tracking', $order) }}">
                    @csrf
                    <button class="btn-outline w-full text-xs py-2">Sync Tracking Now</button>
                </form>
                @endif
                @if(in_array($order->shipping_status, ['failed', 'pending']) && ! $order->awb_code)
                <form method="POST" action="{{ route('admin.orders.retry-shipment', $order) }}">
                    @csrf
                    <button class="btn-primary w-full text-xs py-2">Retry Auto Shipment</button>
                </form>
                @endif
            </div>
        </div>

        <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="card p-6 space-y-3">
            @csrf @method('PATCH')
            <label class="block text-sm font-medium">Update Status</label>
            <select name="status" class="input-field">
                @foreach(['pending','confirmed','shipped','delivered','cancelled'] as $status)
                <option value="{{ $status }}" @selected($order->status === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            <button class="btn-primary w-full">Update</button>
        </form>
    </div>
</div>
@endsection
