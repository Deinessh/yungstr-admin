@extends('admin.layout')

@section('title', 'Orders')
@section('heading', 'Orders')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
    <form method="GET" class="flex gap-2">
        <select name="status" class="input-field text-sm" onchange="this.form.submit()">
            <option value="">All statuses</option>
            @foreach(['pending','confirmed','shipped','delivered','cancelled'] as $status)
            <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
    </form>
</div>

<div class="card overflow-x-auto -mx-4 sm:mx-0 rounded-none sm:rounded-2xl border-x-0 sm:border-x">
    <table class="w-full text-sm min-w-[860px]">
        <thead class="text-left">
            <tr>
                <th class="px-4 py-3">Order</th>
                <th class="px-4 py-3">Customer</th>
                <th class="px-4 py-3">Payment</th>
                <th class="px-4 py-3">Total</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Invoice / Receipt</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($orders as $order)
            <tr>
                <td class="px-4 py-3 font-semibold">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</td>
                <td class="px-4 py-3">{{ $order->user->name }}</td>
                <td class="px-4 py-3 uppercase text-xs">{{ $order->payment_method }} / {{ $order->payment_status }}</td>
                <td class="px-4 py-3 font-bold text-brand-orange">₹{{ number_format($order->total_amount, 0) }}</td>
                <td class="px-4 py-3">{{ $order->status }}</td>
                <td class="px-4 py-3">
                    @if($order->canAccessInvoice())
                        @include('partials.invoice-actions', [
                            'order' => $order,
                            'downloadRoute' => 'admin.orders.invoice',
                            'printRoute' => 'admin.orders.invoice.print',
                            'size' => 'xs',
                        ])
                    @else
                        <span class="text-xs text-gray-400">—</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-right"><a href="{{ route('admin.orders.show', $order) }}" class="text-brand-orange hover:underline">View</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $orders->links() }}</div>
@endsection
