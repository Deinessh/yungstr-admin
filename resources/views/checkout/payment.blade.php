@extends('layouts.master')

@section('content')
<div class="bg-cream py-12 min-h-screen px-4 lg:px-12">
    <div class="max-w-xl mx-auto card p-8 text-center">
        <h1 class="text-2xl font-extrabold text-brand-dark mb-2">Complete Payment</h1>
        <p class="text-gray-600 mb-6">Order #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }} · ₹{{ number_format($order->total_amount, 0) }}</p>
        <button id="rzp-button" class="btn-primary w-full py-4 text-lg">Pay with Razorpay</button>
        <a href="{{ route('checkout') }}" class="inline-block mt-4 text-sm text-gray-500 hover:text-brand-orange">Back to Checkout</a>
    </div>
</div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
document.getElementById('rzp-button').onclick = function(e) {
    e.preventDefault();
    var options = {
        key: @json($razorpayKey),
        amount: @json($amount),
        currency: 'INR',
        name: '{{ $storeSettings['brand_name'] ?? 'Yungstr Club' }}',
        description: 'Order #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}',
        order_id: @json($razorpayOrderId),
        handler: function (response) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = @json(route('order.verify-payment'));
            form.innerHTML = `@csrf
                <input type="hidden" name="order_id" value="{{ $order->id }}">
                <input type="hidden" name="razorpay_payment_id" value="${response.razorpay_payment_id}">
                <input type="hidden" name="razorpay_order_id" value="${response.razorpay_order_id}">
                <input type="hidden" name="razorpay_signature" value="${response.razorpay_signature}">`;
            document.body.appendChild(form);
            form.submit();
        },
        prefill: {
            name: @json(auth()->user()->name),
            email: @json(auth()->user()->email),
        },
        theme: { color: '{{ $storeSettings['theme_primary'] ?? '#000000' }}' }
    };
    new Razorpay(options).open();
};
</script>
@endsection
