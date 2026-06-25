<x-mail::message>
# Thank you for your order, {{ $order->shipping_name ?: $order->user->name }}!

Your order with **{{ $brandName }}** has been confirmed.

**Order #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}**  
@if($order->invoice_number)
Invoice: **{{ $order->invoice_number }}**  
@endif
Payment: **{{ strtoupper($order->payment_method) }}** ({{ ucfirst($order->payment_status) }})

## Order Summary

<x-mail::table>
| Item | Qty | Price |
|:-----|:---:|------:|
@foreach($order->items as $item)
| {{ $item->displayName() }}@if($item->displayComboIncludes())<br><span style="font-size:12px;color:#666;">Includes: {{ $item->displayComboIncludes() }}</span>@endif@if($item->displayPickAnySelections())<br><span style="font-size:12px;color:#666;">Selected: {{ str_replace("\n", '; ', $item->displayPickAnySelections()) }}</span>@endif | {{ $item->quantity }} | ₹{{ number_format($item->price * $item->quantity, 0) }} |
@endforeach
</x-mail::table>

**Subtotal:** ₹{{ number_format($order->subtotal, 0) }}  
**Shipping:** {{ $order->shipping_fee > 0 ? '₹'.number_format($order->shipping_fee, 0) : 'FREE' }}  
@if($order->discount_amount > 0)
**Discount:** -₹{{ number_format($order->discount_amount, 0) }}@if($order->coupon_code) ({{ $order->coupon_code }})@endif  
@endif
**Total:** **₹{{ number_format($order->total_amount, 0) }}**

## Shipping Address

{{ $order->shipping_name ?: $order->user->name }}  
{{ $order->contact_number }}  
{{ $order->shipping_address }}  
@if($order->shipping_city){{ $order->shipping_city }}, @endif{{ $order->shipping_state }} {{ $order->shipping_pincode }}

@if($order->customer_notes)
## Your Notes

{{ $order->customer_notes }}
@endif

@if($order->delivery_date)
**Preferred delivery date:** {{ $order->delivery_date->format('d M Y') }}
@endif

## Shipping Policy

- Orders below ₹{{ number_format($freeShippingThreshold, 0) }}: ₹{{ number_format($shippingFee, 0) }} delivery charge  
- Orders of ₹{{ number_format($freeShippingThreshold, 0) }} and above: **FREE shipping**

@if($order->invoice_number)
Your invoice **{{ $order->invoice_number }}** is attached to this email as a PDF.
@endif

@if($order->tracking_url)
Track your shipment: [{{ $order->awb_code ?: 'Tracking link' }}]({{ $order->tracking_url }})
@endif

<x-mail::button :url="route('orders.index')">
View My Orders
</x-mail::button>

Thanks,<br>
{{ $brandName }}
</x-mail::message>
