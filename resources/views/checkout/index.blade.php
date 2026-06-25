@extends('layouts.master')

@section('content')
@php
    $checkoutShippingLabel = ($shippingQuote['resolved'] ?? false)
        ? (($totals['shipping'] ?? 0) > 0 ? '₹'.number_format($totals['shipping'], 0) : 'FREE')
        : 'Enter PIN code';
    $checkoutDiscountLabel = ($totals['discount'] ?? 0) > 0
        ? '-₹'.number_format($totals['discount'], 0)
        : '';
    $anyPaymentEnabled = $razorpayEnabled || ($cashfreeEnabled ?? false) || $codEnabled;
    $defaultPayment = $razorpayEnabled ? 'razorpay' : (($cashfreeEnabled ?? false) ? 'cashfree' : 'cod');
@endphp
<div class="bg-cream py-12 min-h-screen px-4 lg:px-12">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-extrabold text-brand-dark mb-8">Checkout</h1>

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-50 text-red-700 rounded-xl border border-red-100">{{ session('error') }}</div>
        @endif
        @if(session('success'))
            <div class="mb-4 p-4 bg-brand-green-soft/50 text-brand-dark rounded-xl border border-brand-green/20">{{ session('success') }}</div>
        @endif
        @if(!empty($couponError))
            <div class="mb-4 p-4 bg-red-50 text-red-700 rounded-xl border border-red-100">{{ $couponError }}</div>
        @endif

        <div class="card overflow-hidden">
            <div class="p-6 md:p-8">
                <form action="{{ route('order.place') }}" method="POST" id="checkout-form"
                    x-data="checkoutShipping({
                        subtotal: {{ $totals['subtotal'] }},
                        discount: {{ $totals['discount'] }},
                        quote: @js($shippingQuote),
                        quoteUrl: @js(route('shipping.quote')),
                        csrf: @js(csrf_token()),
                        initialShippingLabel: @js($checkoutShippingLabel),
                        initialTotal: {{ $totals['total'] }},
                    })"
                >
                    @csrf
                    @if($appliedCouponCode ?? null)
                        <input type="hidden" name="coupon_code" value="{{ $appliedCouponCode }}">
                    @endif
                    <div class="mb-8">
                        <h2 class="text-xl font-bold text-brand-dark mb-4 pb-2 border-b border-gray-200">Shipping Information</h2>
                        @if(filled($saved['shipping_pincode'] ?? null) && filled($saved['shipping_address'] ?? null))
                            <div class="mb-4 p-4 bg-brand-green-soft/50 text-brand-dark rounded-xl border border-brand-green/20 text-sm">
                                We've pre-filled your saved delivery address from your last order. Update it only if needed.
                            </div>
                        @endif
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label for="shipping_name" class="block text-sm font-medium text-brand-dark mb-1">Full Name</label>
                                <input type="text" name="shipping_name" id="shipping_name" class="input-field" value="{{ old('shipping_name', $saved['shipping_name'] ?? auth()->user()->name) }}">
                            </div>
                            <div>
                                <label for="contact_number" class="block text-sm font-medium text-brand-dark mb-1">Contact Number</label>
                                <input type="text" name="contact_number" id="contact_number" required class="input-field" placeholder="e.g. 9876543210" value="{{ old('contact_number', $saved['contact_number'] ?? '') }}">
                                @error('contact_number') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="shipping_address" class="block text-sm font-medium text-brand-dark mb-1">Street Address</label>
                                <textarea name="shipping_address" id="shipping_address" rows="2" required class="input-field !rounded-2xl" placeholder="House/Flat No, Street, Landmark">{{ old('shipping_address', $saved['shipping_address'] ?? '') }}</textarea>
                                @error('shipping_address') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="shipping_pincode" class="block text-sm font-medium text-brand-dark mb-1">PIN Code</label>
                                <input
                                    type="text"
                                    name="shipping_pincode"
                                    id="shipping_pincode"
                                    required
                                    maxlength="6"
                                    pattern="\d{6}"
                                    inputmode="numeric"
                                    class="input-field max-w-xs"
                                    value="{{ old('shipping_pincode', $saved['shipping_pincode'] ?? '') }}"
                                    @input.debounce.400ms="refreshQuote()"
                                    @blur="refreshQuote()"
                                >
                                @error('shipping_pincode') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                <p class="text-xs text-gray-500 mt-1" x-show="lookupLoading">Looking up your area…</p>
                                <p class="text-xs mt-1" :class="quote.resolved ? 'text-brand-green' : 'text-gray-500'" x-text="quote.message || 'Enter your 6-digit PIN — city and state will be filled automatically.'"></p>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="shipping_city" class="block text-sm font-medium text-brand-dark mb-1">City <span class="text-gray-400 font-normal text-xs">(auto-filled)</span></label>
                                    <input
                                        type="text"
                                        name="shipping_city"
                                        id="shipping_city"
                                        required
                                        readonly
                                        class="input-field bg-gray-50"
                                        value="{{ old('shipping_city', $saved['shipping_city'] ?? '') }}"
                                        @focus="$el.removeAttribute('readonly')"
                                        title="Filled from your PIN. Click to edit if needed."
                                    >
                                    @error('shipping_city') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label for="shipping_state" class="block text-sm font-medium text-brand-dark mb-1">State <span class="text-gray-400 font-normal text-xs">(auto-filled)</span></label>
                                    <input
                                        type="text"
                                        name="shipping_state"
                                        id="shipping_state"
                                        required
                                        readonly
                                        class="input-field bg-gray-50"
                                        value="{{ old('shipping_state', $saved['shipping_state'] ?? '') }}"
                                        @focus="$el.removeAttribute('readonly')"
                                        title="Filled from your PIN. Click to edit if needed."
                                    >
                                    @error('shipping_state') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div>
                                <label for="customer_notes" class="block text-sm font-medium text-brand-dark mb-1">
                                    Order Notes <span class="text-gray-400 font-normal">(optional)</span>
                                </label>
                                <textarea name="customer_notes" id="customer_notes" rows="2" class="input-field !rounded-2xl" placeholder="Any special instructions for your order">{{ old('customer_notes', $saved['customer_notes'] ?? '') }}</textarea>
                                @error('customer_notes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    @php
                        $pickAnyCartItems = collect($cart)->filter(fn ($item) => !empty($item['is_pick_any_combo']) || \App\Models\Product::find($item['id'])?->is_pick_any_combo);
                    @endphp
                    @if($pickAnyCartItems->isNotEmpty())
                    <div class="mb-8">
                        <h2 class="text-xl font-bold text-brand-dark mb-4 pb-2 border-b border-gray-200">Pick Any 3 Selections</h2>
                        <p class="text-sm text-gray-600 mb-4">Choose exactly 3 different single products for each Pick Any 3 combo in your cart.</p>
                        <div class="space-y-6">
                            @foreach($pickAnyCartItems as $cartKey => $item)
                                <div class="rounded-2xl border border-amber-100 bg-amber-50/40 p-5">
                                    <p class="font-bold text-brand-dark mb-3">{{ $item['name'] }} × {{ $item['quantity'] }}</p>
                                    @include('partials.pick-any-selectors', [
                                        'cartKey' => $cartKey,
                                        'quantity' => $item['quantity'],
                                        'selectableProducts' => $selectableProducts,
                                        'sets' => $item['pick_any_sets'] ?? [],
                                    ])
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div class="mb-8">
                        <h2 class="text-xl font-bold text-brand-dark mb-4 pb-2 border-b border-gray-200">Coupon Code</h2>
                        @if($appliedCouponCode ?? null)
                            <div class="rounded-2xl border border-brand-green/30 bg-brand-green-soft/40 px-4 py-3 flex flex-row items-center justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-brand-dark">Coupon applied</p>
                                    <p class="text-brand-orange font-bold">{{ $appliedCouponCode }}</p>
                                </div>
                                <button
                                    type="submit"
                                    formaction="{{ route('checkout.remove-coupon') }}"
                                    formmethod="POST"
                                    class="text-sm text-gray-600 hover:text-red-600 font-medium shrink-0"
                                >Remove</button>
                            </div>
                        @else
                            <div class="flex flex-row items-center gap-2 w-full max-w-xl">
                                <input
                                    type="text"
                                    name="coupon_code"
                                    class="input-field flex-1 min-w-0 uppercase"
                                    placeholder="Paste coupon code"
                                    value="{{ old('coupon_code', $saved['coupon_code'] ?? '') }}"
                                    autocomplete="off"
                                >
                                <button
                                    type="submit"
                                    formaction="{{ route('checkout.apply-coupon') }}"
                                    formmethod="POST"
                                    class="btn-primary shrink-0 px-6 whitespace-nowrap"
                                >Apply</button>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">Have a promo or personal referral coupon? Paste it here, then click Apply.</p>
                        @endif
                    </div>

                    <div class="mb-8">
                        <h2 class="text-xl font-bold text-brand-dark mb-4 pb-2 border-b border-gray-200">Payment Method</h2>
                        <div class="space-y-3">
                            @if($razorpayEnabled)
                            <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-xl cursor-pointer hover:border-brand-green">
                                <input type="radio" name="payment_method" value="razorpay" @checked(old('payment_method', $saved['payment_method'] ?? $defaultPayment) === 'razorpay') {{ $anyPaymentEnabled ? 'required' : '' }}>
                                <span class="font-medium">Pay Online (Razorpay)</span>
                            </label>
                            @endif
                            @if($cashfreeEnabled ?? false)
                            <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-xl cursor-pointer hover:border-brand-green">
                                <input type="radio" name="payment_method" value="cashfree" @checked(old('payment_method', $saved['payment_method'] ?? $defaultPayment) === 'cashfree') {{ $anyPaymentEnabled ? 'required' : '' }}>
                                <span class="font-medium">Pay Online (Cashfree)</span>
                            </label>
                            @endif
                            @if($codEnabled)
                            <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-xl cursor-pointer hover:border-brand-green">
                                <input type="radio" name="payment_method" value="cod" @checked(old('payment_method', $saved['payment_method'] ?? $defaultPayment) === 'cod') {{ $anyPaymentEnabled ? 'required' : '' }}>
                                <span class="font-medium">Cash on Delivery (COD)</span>
                            </label>
                            @endif
                            @if(!$anyPaymentEnabled)
                                <p class="text-sm text-red-600">No payment methods are currently available. Please contact support.</p>
                            @endif
                        </div>
                    </div>

                    <div class="mb-8 bg-amber-50/50 p-6 rounded-2xl border border-amber-100">
                        <h2 class="text-xl font-bold text-brand-dark mb-4">Order Summary</h2>
                        <ul class="space-y-3 mb-4 text-sm">
                            @foreach($cart as $item)
                            <li class="flex items-start gap-3">
                                <div class="w-12 h-14 bg-[#FAF8F2] rounded-lg flex items-center justify-center shrink-0 overflow-hidden p-1">
                                    <img src="{{ asset($item['image'] ?? 'images/placeholder-product.svg') }}" alt="{{ $item['name'] }}" class="max-h-full max-w-full object-contain" onerror="this.src='{{ asset('images/placeholder-product.svg') }}'">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between gap-3">
                                        <span class="text-gray-600">{{ $item['name'] }} x {{ $item['quantity'] }}</span>
                                        <span class="font-medium text-brand-dark shrink-0">₹{{ number_format($item['price'] * $item['quantity'], 0) }}</span>
                                    </div>
                                    @if(!empty($item['pick_any_sets']))
                                        @include('partials.pick-any-selections-display', [
                                            'sets' => $item['pick_any_sets'],
                                            'class' => 'text-xs text-gray-500 mt-1',
                                        ])
                                    @endif
                                </div>
                            </li>
                            @endforeach
                        </ul>
                        <div class="space-y-2 text-sm border-t border-amber-100 pt-4">
                            <div class="flex justify-between"><span>Subtotal</span><span>₹{{ number_format($totals['subtotal'], 0) }}</span></div>
                            <div class="flex justify-between">
                                <span>Shipping</span>
                                <span class="font-medium text-brand-dark" x-text="shippingDisplay">{{ $checkoutShippingLabel }}</span>
                            </div>
                            @if(($totals['discount'] ?? 0) > 0)
                            <div class="flex justify-between text-brand-green" x-show="discount > 0" x-cloak>
                                <span>Discount{{ ($appliedCouponCode ?? null) ? ' ('.$appliedCouponCode.')' : '' }}</span>
                                <span x-text="'-₹' + Math.round(discount).toLocaleString('en-IN')">{{ $checkoutDiscountLabel }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between items-center font-bold text-xl text-brand-dark pt-2">
                                <span>Total Payable</span>
                                <span class="text-brand-orange" x-text="totalDisplay">₹{{ number_format($totals['total'], 0) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row items-stretch gap-4">
                        <button type="submit" class="btn-primary flex-1 flex justify-center text-lg py-4" {{ !$anyPaymentEnabled ? 'disabled' : '' }}>Place Order</button>
                        <button formaction="{{ route('checkout.save-draft') }}" formmethod="POST" class="btn-outline flex-1 flex justify-center py-4">Save & Continue Later</button>
                        <a href="{{ route('cart.index') }}" class="btn-outline flex-1 flex justify-center py-4 items-center">Back to Cart</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
