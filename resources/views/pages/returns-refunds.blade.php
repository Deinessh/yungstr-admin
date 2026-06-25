@extends('layouts.master')

@section('title', 'Return & Refund Policy | '.($storeSettings['brand_name'] ?? 'Yungstr Club'))
@section('meta_description', 'Return and refund policy for Yungstr Club streetwear products — eligibility, process, and timelines.')

@section('content')
<x-static-page title="Return & Refund Policy" subtitle="Our commitment to your satisfaction with every order.">
    <p><strong>Last updated:</strong> {{ now()->format('F j, Y') }}</p>

    <h2>Our Promise</h2>
    <p>We want you to enjoy premium quality streetwear. If something goes wrong with your order, we'll work with you to make it right.</p>

    <h2>Eligible Returns</h2>
    <p>You may request a return or replacement if:</p>
    <ul>
        <li>The product received is <strong>damaged, defective, or leaked</strong> upon delivery.</li>
        <li>You received the <strong>wrong product</strong> or an incomplete order.</li>
        <li>The pack is <strong>unopened, sealed, and in original condition</strong> (for certain cases, at our discretion).</li>
    </ul>
    <p>Worn, washed, or altered apparel products cannot be returned for hygiene and brand standards reasons, except in cases of proven defect or damage.</p>

    <h2>Return Window</h2>
    <p>Please report any issue within <strong>7 days of delivery</strong>. Include your order number and clear photos of the product and packaging.</p>

    <h2>How to Request a Return or Refund</h2>
    <ol>
        <li>Email us at <a href="mailto:{{ $storeSettings['store_email'] }}">{{ $storeSettings['store_email'] }}</a> or use our <a href="{{ route('contact') }}">Contact form</a>.</li>
        <li>Share your order number, reason for return, and photos (if applicable).</li>
        <li>Our team will review and respond within 2–3 business days.</li>
        <li>If approved, we will arrange a replacement or process a refund to your original payment method.</li>
    </ol>

    <h2>Refunds</h2>
    <p>Approved refunds are processed within <strong>5–7 business days</strong> after verification. Prepaid orders are refunded to the original payment source. COD orders may receive a bank transfer or store credit as applicable.</p>
    <p>Shipping charges are non-refundable unless the return is due to our error (wrong/damaged product).</p>

    <h2>Replacements</h2>
    <p>Where possible, we prefer sending a replacement at no extra cost rather than a refund — especially for damaged or incorrect items.</p>

    <h2>Non-Returnable Items</h2>
    <ul>
        <li>Worn, washed, or partially used apparel items (unless defective).</li>
        <li>Products purchased during clearance or special promotions marked as non-returnable.</li>
        <li>Issues reported after the 7-day window without valid reason.</li>
    </ul>

    <h2>Contact</h2>
    <p>Questions about returns? Reach us at {{ $storeSettings['store_phone'] }} or <a href="{{ route('contact') }}">contact us online</a>.</p>
</x-static-page>
@endsection
