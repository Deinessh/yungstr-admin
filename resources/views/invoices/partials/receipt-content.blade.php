<div class="header">
    <div class="left">
        @if(!empty($invoiceLogoPath))
            <img src="{{ $invoiceLogoPath }}" alt="{{ $legalCompanyName ?? $brandName }}" style="max-height: 56px; max-width: 220px; margin-bottom: 8px;">
        @endif
        <p class="title">{{ $legalCompanyName ?? $brandName }}</p>
        @if(($legalCompanyName ?? '') !== '' && ($legalCompanyName ?? '') !== $brandName)
            <p class="muted">{{ $brandName }}</p>
        @endif
        <p class="muted">{{ $websiteName }}</p>
        @if($invoiceAddress)
            <p class="muted pre-line">{{ $invoiceAddress }}</p>
        @endif
        @if($invoiceGstin)
            <p class="muted">GSTIN: {{ $invoiceGstin }}</p>
        @endif
        <p class="muted">{{ $storeEmail }}</p>
        <p class="muted">{{ $storePhone }}</p>
    </div>
    <div class="right">
        <p><strong>TAX INVOICE / RECEIPT</strong></p>
        <p>Invoice No: <strong>{{ $order->invoice_number }}</strong></p>
        <p>Order No: #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</p>
        <p>Date: {{ ($order->invoiced_at ?? now())->format('d M Y') }}</p>
        @if($order->awb_code)
            <p>AWB: {{ $order->awb_code }}</p>
        @endif
    </div>
</div>

<p><strong>Bill To</strong><br>
    {{ $order->shipping_name ?: $order->user->name }}<br>
    {{ $order->user->email }}<br>
    {{ $order->contact_number }}<br>
    <span class="pre-line">{{ $order->shipping_address }}</span>
    @if($order->shipping_city), {{ $order->shipping_city }}@endif
    @if($order->shipping_state), {{ $order->shipping_state }}@endif
    @if($order->shipping_pincode) - {{ $order->shipping_pincode }}@endif
</p>

@if($order->customer_notes)
<p><strong>Order Notes:</strong><br><span class="pre-line">{{ $order->customer_notes }}</span></p>
@endif

<table class="items-table">
    <thead>
        <tr>
            <th>Item</th>
            <th>Qty</th>
            <th>MRP (₹)</th>
            <th>Rate (₹)</th>
            <th>Amount (₹)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($order->items as $item)
        <tr>
            <td>
                {{ $item->displayName() }}
                @if($item->displayWeight())
                    <div class="item-meta">Pack: {{ $item->displayWeight() }}</div>
                @endif
                @if($item->displayComboIncludes())
                    <div class="item-meta">Includes: {{ $item->displayComboIncludes() }}</div>
                @endif
                @if($item->displayPickAnySelections())
                    <div class="item-meta">Selected mixes:<br><span class="pre-line">{{ $item->displayPickAnySelections() }}</span></div>
                @endif
            </td>
            <td>{{ $item->quantity }}</td>
            <td>{{ $item->displayMrp() ? number_format($item->displayMrp(), 2) : '—' }}</td>
            <td>{{ number_format($item->price, 2) }}</td>
            <td>{{ number_format($item->price * $item->quantity, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<table class="totals">
    <tr><td class="label">Subtotal:</td><td>₹{{ number_format($order->subtotal, 2) }}</td></tr>
    <tr><td class="label">Shipping:</td><td>{{ $order->shipping_fee > 0 ? '₹'.number_format($order->shipping_fee, 2) : 'FREE' }}</td></tr>
    @if($order->shipping_zone_name)
    <tr><td class="label">Delivery Zone:</td><td>{{ $order->shipping_zone_name }}</td></tr>
    @endif
    <tr><td class="label">Discount:</td><td>-₹{{ number_format($order->discount_amount, 2) }}</td></tr>
    @if($order->coupon_code)
    <tr><td class="label">Coupon:</td><td>{{ $order->coupon_code }}</td></tr>
    @endif
    <tr><td class="label grand">Total:</td><td class="grand">₹{{ number_format($order->total_amount, 2) }}</td></tr>
    <tr><td class="label">Payment:</td><td>{{ strtoupper($order->payment_method) }} ({{ $order->payment_status }})</td></tr>
</table>

<div class="clearfix"></div>

@if($order->shipping_zone_name)
<div class="policy">
    <strong>Shipping:</strong> Delivered to {{ $order->shipping_zone_name }}.
    @if($order->shipping_fee <= 0)
        This order qualified for free delivery.
    @endif
</div>
@endif

<div class="footer-note">
    This is a computer-generated invoice from {{ $websiteName }}. Thank you for shopping with {{ $brandName }}.
</div>
