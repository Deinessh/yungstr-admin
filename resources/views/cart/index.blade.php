@extends('layouts.master')

@section('content')
<div class="bg-cream py-12 min-h-screen px-4 lg:px-12">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-extrabold text-brand-dark mb-8">Your Cart</h1>

        @if(count($cart) > 0)
        <div class="flex flex-col lg:flex-row gap-8">
            <div class="lg:w-2/3">
                <div class="card overflow-hidden">
                    <ul class="divide-y divide-gray-100">
                        @foreach($cart as $id => $details)
                        <li class="p-6 flex flex-col sm:flex-row items-center gap-6">
                            <div class="w-20 h-24 bg-[#FAF8F2] rounded-xl flex items-center justify-center shrink-0 overflow-hidden p-2">
                                <img src="{{ asset($details['image'] ?? 'images/placeholder-product.svg') }}" alt="{{ $details['name'] }}" class="max-h-full max-w-full object-contain" onerror="this.src='{{ asset('images/placeholder-product.svg') }}'">
                            </div>
                            <div class="flex-1 text-center sm:text-left">
                                <h3 class="text-lg font-bold text-brand-dark">{{ $details['name'] }}</h3>
                                @if(!empty($details['is_coming_soon']))
                                    <span class="product-tag-coming-soon mt-1">Coming Soon</span>
                                @endif
                                @if(!empty($details['checkout_blocked_reason']))
                                    <p class="text-xs text-red-600 font-medium mt-1">{{ $details['checkout_blocked_reason'] }}</p>
                                @endif
                                <p class="text-brand-orange font-semibold mt-1">₹{{ number_format($details['price'], 0) }}</p>
                                @if(!empty($details['pick_any_sets']))
                                    @include('partials.pick-any-selections-display', [
                                        'sets' => $details['pick_any_sets'],
                                        'class' => 'text-xs text-gray-600 mt-2 text-left',
                                    ])
                                @endif
                            </div>
                            <div class="flex items-center gap-6">
                                <form action="{{ route('cart.update') }}" method="POST" class="flex items-center gap-3">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $id }}">
                                    @include('partials.quantity-adjuster', [
                                        'value' => $details['quantity'],
                                        'min' => 1,
                                        'max' => array_key_exists('stock', $details) && $details['stock'] !== null ? $details['stock'] : null,
                                        'autoSubmit' => true,
                                    ])
                                </form>
                                <div class="text-right w-24 font-bold text-brand-dark sm:block">₹{{ number_format($details['price'] * $details['quantity'], 0) }}</div>
                                <form action="{{ route('cart.remove') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $id }}">
                                    <button type="submit" class="text-red-500 hover:text-red-700 bg-red-50 p-2 rounded-lg hover:bg-red-100 transition">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="lg:w-1/3">
                <div
                    class="card p-6 sticky top-24 space-y-5"
                    x-data="cartOrderSummary({
                        subtotal: {{ $totals['subtotal'] }},
                        initialShipping: {{ ($shippingQuote['resolved'] ?? false) ? ($shippingQuote['shipping_fee'] ?? 0) : 'null' }},
                        initialTotal: {{ ($shippingQuote['resolved'] ?? false) ? $totals['total'] : $totals['subtotal'] }},
                        quoteResolved: {{ ($shippingQuote['resolved'] ?? false) ? 'true' : 'false' }},
                    })"
                    @shipping-quote-updated.window="applyQuote($event.detail)"
                >
                    @include('partials.shipping-pincode-checker', [
                        'subtotal' => $totals['subtotal'],
                        'shippingQuote' => $shippingQuote,
                        'shippingLocation' => $shippingLocation,
                    ])

                    <div>
                        <h2 class="text-xl font-bold text-brand-dark mb-4">Order Summary</h2>
                        <div class="space-y-4 text-sm">
                            <div class="flex justify-between text-gray-600"><span>Subtotal</span><span>₹{{ number_format($totals['subtotal'], 0) }}</span></div>
                            <div class="flex justify-between text-gray-600">
                                <span>Shipping</span>
                                <span class="font-medium" :class="resolved && (shipping || 0) <= 0 ? 'text-brand-green' : 'text-brand-dark'" x-text="shippingLabel()"></span>
                            </div>
                            <div class="border-t border-gray-200 pt-4 flex justify-between font-bold text-lg text-brand-dark">
                                <span>Total</span>
                                <span x-text="totalLabel()"></span>
                            </div>
                        </div>
                    </div>

                    @if(!($canCheckout ?? true))
                    <div class="mb-4 p-4 bg-red-50 rounded-xl border border-red-100 text-sm text-red-700">
                        Remove coming soon or unavailable items before checkout.
                    </div>
                    @endif

                    @guest
                    <div class="mb-4 p-4 bg-amber-50 rounded-xl border border-amber-100 text-sm text-gray-700">
                        Please login or create an account before checkout.
                    </div>
                    @if($canCheckout ?? true)
                    <a href="{{ route('login') }}" class="btn-primary w-full flex justify-center py-4">Login to Checkout</a>
                    <a href="{{ route('register') }}" class="btn-outline w-full flex justify-center py-4 mt-3">Create Account</a>
                    @else
                    <button type="button" disabled class="btn-primary w-full flex justify-center py-4 opacity-50 cursor-not-allowed">Login to Checkout</button>
                    @endif
                    @else
                    @if($canCheckout ?? true)
                    <a href="{{ route('checkout') }}" class="btn-primary w-full flex justify-center py-4">Proceed to Checkout</a>
                    @else
                    <button type="button" disabled class="btn-primary w-full flex justify-center py-4 opacity-50 cursor-not-allowed">Proceed to Checkout</button>
                    @endif
                    @endguest

                    <a href="{{ route('products.index') }}" class="block text-center mt-4 text-gray-500 hover:text-brand-amber font-medium">Continue Shopping</a>
                </div>
            </div>
        </div>
        @else
        <div class="card p-12 text-center max-w-2xl mx-auto">
            <div class="w-24 h-24 bg-brand-green-soft rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-shopping-bag text-brand-green text-3xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-brand-dark mb-2">Your cart is empty</h2>
            <p class="text-gray-500 mb-8">Looks like you haven't added any items to your cart yet.</p>
            <a href="{{ route('products.index') }}" class="btn-primary">Start Shopping</a>
        </div>
        @endif
    </div>
</div>
@endsection
