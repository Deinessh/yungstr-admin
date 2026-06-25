@extends('layouts.master')

@section('content')
<div class="bg-cream py-12 min-h-screen px-4 lg:px-12">
    <div class="max-w-5xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-extrabold text-brand-dark">My Orders</h1>
            <a href="{{ route('products.index') }}" class="text-brand-green font-medium hover:text-brand-orange transition">Continue Shopping</a>
        </div>

        @if($orders->count() > 0)
            <div class="space-y-6">
                @foreach($orders as $order)
                <div class="card overflow-hidden">
                    <div class="bg-amber-50/50 px-6 py-4 border-b border-amber-100 flex flex-wrap gap-4 items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Order ID</p>
                            <p class="font-bold text-brand-dark">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Date Placed</p>
                            <p class="font-medium text-brand-dark">{{ $order->created_at->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Total Amount</p>
                            <p class="font-bold text-brand-orange">₹{{ number_format($order->total_amount, 0) }}</p>
                        </div>
                        <div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wide bg-brand-green-soft text-brand-green">{{ $order->status }}</span>
                            <span class="block text-[10px] text-gray-500 mt-1">{{ strtoupper($order->payment_method ?? 'N/A') }} · {{ $order->payment_status }}</span>
                        </div>
                        <div class="flex flex-col gap-2 items-start">
                        @if($order->awb_code && $order->tracking_url)
                        <a href="{{ $order->tracking_url }}" target="_blank" rel="noopener" class="text-sm text-brand-orange font-medium">Track shipment</a>
                        @endif
                        @if($order->isPaid() && $order->canAccessInvoice())
                            <a href="{{ route('orders.invoice', $order) }}" class="inline-flex items-center gap-1 text-[11px] font-semibold px-2.5 py-1.5 rounded-lg border border-cream-dark bg-white text-brand-chocolate hover:bg-cream-bar transition">
                                <i class="fas fa-file-pdf"></i> Download Invoice
                            </a>
                        @endif
                        </div>
                    </div>
                    <div class="p-6">
                        <h4 class="font-bold text-brand-dark mb-4 border-b border-gray-100 pb-2">Items</h4>
                        <div class="space-y-4">
                            @foreach($order->items as $item)
                            <div class="flex items-center gap-4">
                                <div class="w-16 h-16 bg-[#FAF8F2] rounded-lg flex items-center justify-center shrink-0">
                                    <i class="fas fa-box text-brand-green"></i>
                                </div>
                                <div class="flex-1">
                                    <h5 class="font-bold text-brand-dark">{{ $item->product ? $item->product->name : 'Product Unavailable' }}</h5>
                                    <p class="text-sm text-gray-500">Qty: {{ $item->quantity }} × ₹{{ number_format($item->price, 0) }}</p>
                                    @if($item->displayPickAnySelections())
                                        <p class="text-xs text-brand-green mt-1 whitespace-pre-line"><strong>Selected mixes:</strong><br>{{ $item->displayPickAnySelections() }}</p>
                                    @endif
                                </div>
                                <div class="font-bold text-brand-dark">₹{{ number_format($item->price * $item->quantity, 0) }}</div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="card p-12 text-center">
                <div class="w-20 h-20 bg-brand-green-soft rounded-full flex items-center justify-center mx-auto mb-6 text-brand-green">
                    <i class="fas fa-box-open text-3xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-brand-dark mb-2">No orders found</h2>
                <p class="text-gray-500 mb-8">You haven't placed any orders with us yet.</p>
                <a href="{{ route('products.index') }}" class="btn-primary">Explore Products</a>
            </div>
        @endif
    </div>
</div>
@endsection
