@extends('layouts.master')

@section('title', 'FAQs | '.($storeSettings['brand_name'] ?? 'Yungstr Club'))
@section('meta_description', 'Frequently asked questions about Yungstr Club streetwear collections, sizing, shipping, and payments.')

@section('content')
<x-static-page title="Frequently Asked Questions" subtitle="Quick answers about our streetwear drops, orders, and delivery.">
    <div x-data="{ open: null }" class="space-y-3">
        @foreach([
            ['q' => 'What makes Yungstr Club products different?', 'a' => 'Our apparel is designed with custom heavyweight fabrics, custom oversized fits, and exclusive street-approved graphic designs that are built to last.'],
            ['q' => 'How do I choose the correct size?', 'a' => 'We provide detailed size guides on every product page. Most of our t-shirts and hoodies feature a custom oversized street fit. If you prefer a regular fit, consider sizing down.'],
            ['q' => 'Are your products ethically sourced?', 'a' => 'Yes. All Yungstr Club products are crafted with premium materials sourced from ethical manufacturers.'],
            ['q' => 'How do I place an order?', 'a' => 'Browse our shop, add products to cart, create an account or log in, enter your delivery address and PIN code, then pay online via Razorpay or choose Cash on Delivery where available.'],
            ['q' => 'How are delivery charges calculated?', 'a' => 'Delivery fees depend on your PIN code and location. Enter your 6-digit PIN at checkout to see zone-specific shipping charges and free-delivery eligibility before you pay.'],
            ['q' => 'When will I receive free delivery?', 'a' => 'Free delivery thresholds vary by zone. For example, Hyderabad metro areas often qualify for free delivery on orders above ₹399. Other regions may have different thresholds — your exact eligibility is shown after PIN code entry.'],
            ['q' => 'Can I track my order?', 'a' => 'Yes. After your order is confirmed and shipped, you can view order status and tracking details from your account dashboard under My Orders.'],
            ['q' => 'What payment methods do you accept?', 'a' => 'We accept online payments through Razorpay (UPI, cards, net banking) and Cash on Delivery (COD) in serviceable areas when enabled.'],
            ['q' => 'Can I return or exchange a product?', 'a' => 'Unopened, sealed packs in original condition may be eligible for return or replacement within 7 days of delivery if damaged or incorrect. See our Return & Refund Policy for full details.'],
            ['q' => 'How do I contact customer support?', 'a' => 'Reach us via the Contact page, email at '.$storeSettings['store_email'].', or WhatsApp/call at '.$storeSettings['store_phone'].'. We respond Mon–Sat during business hours.'],
        ] as $index => $faq)
        <div class="faq-item rounded-2xl border border-gray-200 overflow-hidden">
            <button type="button" class="w-full flex items-center justify-between gap-4 px-5 py-4 text-left bg-white hover:bg-cream-bar/30 transition" @click="open = open === {{ $index }} ? null : {{ $index }}">
                <span class="font-semibold text-brand-dark text-sm md:text-base">{{ $faq['q'] }}</span>
                <i class="fas fa-chevron-down text-brand-orange text-xs transition-transform" :class="open === {{ $index }} ? 'rotate-180' : ''"></i>
            </button>
            <div x-show="open === {{ $index }}" x-cloak x-transition class="px-5 pb-4 text-sm text-gray-600 leading-relaxed bg-white border-t border-gray-100">
                {{ $faq['a'] }}
            </div>
        </div>
        @endforeach
    </div>

    <p class="mt-8 text-sm text-gray-500">Still have questions? <a href="{{ route('contact') }}">Contact us</a> — we're happy to help.</p>
</x-static-page>
@endsection
