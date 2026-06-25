@extends('layouts.master')

@section('title', 'Privacy Policy | '.($storeSettings['brand_name'] ?? 'Yungstr Club'))
@section('meta_description', 'Privacy policy explaining how Yungstr Club collects, uses, and protects your personal information.')

@section('content')
<x-static-page title="Privacy Policy" subtitle="How we collect, use, and protect your information.">
    <p><strong>Last updated:</strong> {{ now()->format('F j, Y') }}</p>

    <h2>1. Introduction</h2>
    <p>{{ $storeSettings['brand_name'] }} ("we", "us") respects your privacy. This policy explains what information we collect when you use our website, create an account, or place an order, and how we use it.</p>

    <h2>2. Information We Collect</h2>
    <h3>Information you provide</h3>
    <ul>
        <li><strong>Account details:</strong> name, email address, mobile number, password.</li>
        <li><strong>Order details:</strong> shipping address, city, state, PIN code, contact number, payment method.</li>
        <li><strong>Communications:</strong> messages sent via our contact form or customer support.</li>
    </ul>
    <h3>Information collected automatically</h3>
    <ul>
        <li>Browser type, device information, and pages visited (via standard server logs and cookies).</li>
        <li>Order and session data needed to operate the store and checkout.</li>
    </ul>

    <h2>3. How We Use Your Information</h2>
    <ul>
        <li>To process and deliver your orders.</li>
        <li>To calculate zone-wise shipping and communicate order status.</li>
        <li>To manage your account and provide customer support.</li>
        <li>To send order confirmations, invoices, and important service updates.</li>
        <li>To improve our website, products, and services.</li>
        <li>To comply with legal obligations.</li>
    </ul>

    <h2>4. Payment Information</h2>
    <p>Online payments are processed by Razorpay, a third-party payment gateway. We do not store your full card or UPI credentials on our servers. Razorpay handles payment data according to their own privacy and security standards.</p>

    <h2>5. Sharing of Information</h2>
    <p>We do not sell your personal data. We may share information with:</p>
    <ul>
        <li><strong>Delivery partners</strong> (e.g. courier/shipping services) to fulfil your order.</li>
        <li><strong>Payment processors</strong> to complete transactions.</li>
        <li><strong>Service providers</strong> who assist in hosting, email, or analytics — under confidentiality obligations.</li>
        <li><strong>Authorities</strong> when required by law.</li>
    </ul>

    <h2>6. Cookies</h2>
    <p>We use essential cookies and session data to keep you logged in, maintain your cart, and remember checkout preferences. You can control cookies through your browser settings, though some features may not work if cookies are disabled.</p>

    <h2>7. Data Retention</h2>
    <p>We retain account and order information for as long as needed to provide services, comply with law, and resolve disputes. You may request account deletion by contacting us.</p>

    <h2>8. Security</h2>
    <p>We use reasonable technical and organisational measures to protect your data, including encrypted connections (HTTPS) and secure password storage. No method of transmission over the internet is 100% secure.</p>

    <h2>9. Your Rights</h2>
    <p>You may request access to, correction of, or deletion of your personal data by emailing <a href="mailto:{{ $storeSettings['store_email'] }}">{{ $storeSettings['store_email'] }}</a>. We will respond within a reasonable timeframe.</p>

    <h2>10. Children's Privacy</h2>
    <p>Our services are not directed at children under 18. We do not knowingly collect personal information from minors.</p>

    <h2>11. Changes to This Policy</h2>
    <p>We may update this Privacy Policy from time to time. The "Last updated" date at the top will reflect the latest version.</p>

    <h2>12. Contact Us</h2>
    <p>For privacy-related questions:<br>
    Email: <a href="mailto:{{ $storeSettings['store_email'] }}">{{ $storeSettings['store_email'] }}</a><br>
    Phone: {{ $storeSettings['store_phone'] }}<br>
    <a href="{{ route('contact') }}">Contact form</a></p>
</x-static-page>
@endsection
