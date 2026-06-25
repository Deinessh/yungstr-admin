@extends('layouts.master')

@section('title', 'Terms & Conditions | '.($storeSettings['brand_name'] ?? 'Yungstr Club'))
@section('meta_description', 'Terms and conditions for using the Yungstr Club website and purchasing our premium streetwear products.')

@section('content')
<x-static-page title="Terms & Conditions" subtitle="Please read these terms before using our website or placing an order.">
    <p><strong>Last updated:</strong> {{ now()->format('F j, Y') }}</p>

    <h2>1. Agreement</h2>
    <p>By accessing {{ $storeSettings['website_name'] ?? $storeSettings['brand_name'] }} ("we", "us", "our") or placing an order, you agree to these Terms & Conditions and our Privacy Policy.</p>

    <h2>2. Products</h2>
    <p>We sell premium streetwear apparel, accessories, and urban lifestyle products. Product images are for illustration; colors and detailing may vary slightly. Please check the sizing charts on product pages before ordering.</p>

    <h2>3. Orders & Pricing</h2>
    <ul>
        <li>All prices are in Indian Rupees (₹) and include applicable taxes unless stated otherwise.</li>
        <li>We reserve the right to correct pricing errors and cancel orders placed at incorrect prices.</li>
        <li>An order is confirmed only after successful payment (prepaid) or order acceptance (COD).</li>
        <li>We may limit quantities per customer or refuse orders at our discretion.</li>
    </ul>

    <h2>4. Payment</h2>
    <p>Online payments are processed securely via Razorpay. Cash on Delivery is available in selected areas when enabled. You agree to provide accurate payment and billing information.</p>

    <h2>5. Shipping</h2>
    <p>Delivery terms are governed by our <a href="{{ route('pages.shipping-policy') }}">Shipping Policy</a>. Shipping charges and free-delivery eligibility are determined by your delivery PIN code and zone.</p>

    <h2>6. Returns & Refunds</h2>
    <p>Returns and refunds are handled as described in our <a href="{{ route('pages.returns-refunds') }}">Return & Refund Policy</a>.</p>

    <h2>7. Account</h2>
    <p>You are responsible for maintaining the confidentiality of your account credentials. You may log in using your registered email or 10-digit mobile number. Notify us immediately of any unauthorized use.</p>

    <h2>8. Intellectual Property</h2>
    <p>All content on this website — including logos, text, images, and product descriptions — is owned by {{ $storeSettings['brand_name'] }} or its licensors and may not be copied or used without permission.</p>

    <h2>9. Limitation of Liability</h2>
    <p>To the fullest extent permitted by law, we are not liable for indirect, incidental, or consequential damages arising from use of our products or website. Our liability is limited to the value of the order in question.</p>

    <h2>10. Governing Law</h2>
    <p>These terms are governed by the laws of India. Disputes shall be subject to the jurisdiction of courts in Telangana, India.</p>

    <h2>11. Changes</h2>
    <p>We may update these terms at any time. Continued use of the website after changes constitutes acceptance of the revised terms.</p>

    <h2>12. Contact</h2>
    <p>For questions about these terms: <a href="mailto:{{ $storeSettings['store_email'] }}">{{ $storeSettings['store_email'] }}</a></p>
</x-static-page>
@endsection
