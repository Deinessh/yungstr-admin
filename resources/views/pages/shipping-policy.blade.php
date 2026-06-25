@extends('layouts.master')

@section('title', 'Shipping Policy | '.($storeSettings['brand_name'] ?? 'Yungstr Club'))
@section('meta_description', 'Shipping and delivery policy for Yungstr Club orders across India, including zone-wise rates and free delivery eligibility.')

@section('content')
<x-static-page title="Shipping Policy" subtitle="How we deliver Yungstr Club products to your doorstep.">
    <p><strong>Last updated:</strong> {{ now()->format('F j, Y') }}</p>

    <h2>Delivery Areas</h2>
    <p>We deliver across India to serviceable PIN codes. Enter your 6-digit PIN code at checkout to confirm delivery availability and see your location-specific shipping charges.</p>

    <h2>Zone-Wise Shipping Charges</h2>
    <p>Delivery fees and free-shipping thresholds depend on your delivery zone (based on PIN code, city, or state). Typical zones include:</p>
    <ul>
        <li><strong>Hyderabad Metro</strong> — standard delivery fee applies below the free-shipping threshold; free delivery on qualifying order values (commonly orders above ₹399).</li>
        <li><strong>Telangana (other areas)</strong> — separate rates and free-delivery threshold apply.</li>
        <li><strong>Rest of India</strong> — pan-India delivery with zone-specific fees and free-shipping minimums.</li>
    </ul>
    <p>Your exact shipping cost and free-delivery eligibility are calculated automatically when you enter your PIN code — nothing is charged until you confirm your order.</p>

    <h2>Order Processing</h2>
    <p>Orders are processed after payment confirmation (prepaid) or order confirmation (Cash on Delivery). You will receive an order confirmation email with your order details.</p>

    <h2>Estimated Delivery Time</h2>
    <p>Delivery timelines vary by location and courier partner. Most orders are dispatched within 1–3 business days of confirmation. Metro and tier-1 cities typically receive orders within 3–7 business days; other locations may take 5–10 business days.</p>

    <h2>Order Tracking</h2>
    <p>Once shipped, you can track your order from your account dashboard. An AWB/tracking number will be shared when your shipment is handed to the courier.</p>

    <h2>Undeliverable or Wrong Address</h2>
    <p>Please ensure your shipping address, city, state, and PIN code are correct at checkout. We are not responsible for delays or failed delivery due to incorrect address details provided by the customer.</p>

    <h2>Contact</h2>
    <p>For shipping queries, contact us at <a href="mailto:{{ $storeSettings['store_email'] }}">{{ $storeSettings['store_email'] }}</a> or <a href="{{ route('contact') }}">send a message</a>.</p>
</x-static-page>
@endsection
