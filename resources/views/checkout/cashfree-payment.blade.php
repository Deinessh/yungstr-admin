@extends('layouts.master')

@section('content')
<div class="max-w-lg mx-auto px-4 py-16 text-center">
    <h1 class="text-2xl font-bold text-brand-dark mb-2">Complete Payment</h1>
    <p class="text-gray-600 mb-6">Order #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }} — ₹{{ number_format($order->total_amount, 0) }}</p>
    <p class="text-sm text-gray-500 mb-8">Secure payment via Cashfree. Please wait…</p>
    <div id="cashfree-pay" class="inline-block btn-primary px-8 py-3 cursor-pointer">Pay Now</div>
</div>

@push('scripts')
<script src="https://sdk.cashfree.com/js/v3/cashfree.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const cashfree = Cashfree({ mode: @json($cashfreeMode) });
    const checkoutOptions = {
        paymentSessionId: @json($paymentSessionId),
        redirectTarget: '_self',
    };

    cashfree.checkout(checkoutOptions).catch(function () {
        document.getElementById('cashfree-pay').textContent = 'Retry Payment';
    });

    document.getElementById('cashfree-pay')?.addEventListener('click', function () {
        cashfree.checkout(checkoutOptions);
    });
});
</script>
@endpush
@endsection
