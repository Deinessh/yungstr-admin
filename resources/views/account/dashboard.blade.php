@extends('layouts.master')

@section('content')
<div class="bg-cream py-12 min-h-screen px-4 lg:px-12">
    <div class="max-w-5xl mx-auto">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <h1 class="text-3xl font-extrabold text-brand-dark">My Account</h1>
            <div class="flex flex-wrap gap-3">
                @if($draft && !empty($draft->cart_data))
                <a href="{{ route('account.resume-checkout') }}" class="btn-primary text-sm">Resume Checkout</a>
                @endif
                <a href="{{ route('profile.edit') }}" class="btn-outline text-sm">Edit Profile</a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="card p-6 lg:col-span-2 space-y-6">
                <div>
                    <h2 class="font-bold text-brand-dark mb-4">Referral Program</h2>
                    @if($user->referral_code)
                        <div class="space-y-3 text-sm">
                            <p>Your referral code: <strong class="text-brand-orange text-lg">{{ $user->referral_code }}</strong></p>
                            <p>Successful referrals: <strong>{{ $user->successful_referrals_count }}</strong> / {{ $referralsRequired }}</p>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-brand-green h-2 rounded-full" style="width: {{ min(100, ($user->successful_referrals_count / max($referralsRequired, 1)) * 100) }}%"></div>
                            </div>
                            <p class="text-xs text-gray-500">Share link: {{ route('register') }}?ref={{ $user->referral_code }}</p>
                        </div>
                    @else
                        <p class="text-sm text-gray-600">Place your first order to receive your personal referral coupon code.</p>
                    @endif
                </div>

                @if($personalCoupon)
                <div class="p-4 bg-amber-50 rounded-xl border border-amber-100">
                    <p class="text-sm font-semibold text-brand-dark mb-1">Your Personal Coupon</p>
                    <p class="text-brand-orange font-bold text-lg">{{ $personalCoupon->code }}</p>
                    @if($personalCoupon->is_active && ! $personalCoupon->isExpired())
                        <p class="text-sm text-brand-green font-medium mt-2">Active — use at checkout before {{ $personalCoupon->expires_at?->format('M d, Y') }}</p>
                        <p class="text-xs text-gray-600 mt-1">
                            @if($personalCoupon->type === 'percent')
                                {{ rtrim(rtrim(number_format($personalCoupon->value, 2), '0'), '.') }}% off your order
                            @else
                                ₹{{ number_format($personalCoupon->value, 0) }} off your order
                            @endif
                        </p>
                    @elseif($personalCoupon->isExpired())
                        <p class="text-sm text-red-600 mt-2">Expired on {{ $personalCoupon->expires_at?->format('M d, Y') }}</p>
                    @else
                        <p class="text-sm text-gray-600 mt-2">Pending activation — refer {{ max($referralsRequired - $user->successful_referrals_count, 0) }} more friends who place their first successful order using your code.</p>
                    @endif
                </div>
                @endif

                @if($referrals->count() > 0)
                <div>
                    <h3 class="font-bold text-brand-dark mb-3 text-sm">Your Referrals</h3>
                    <div class="overflow-x-auto -mx-2">
                        <table class="w-full text-sm min-w-[420px]">
                            <thead class="text-left text-gray-500">
                                <tr><th class="px-2 py-2">Friend</th><th class="px-2 py-2">Status</th><th class="px-2 py-2">Date</th></tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($referrals as $referral)
                                <tr>
                                    <td class="px-2 py-2">{{ $referral->referredUser?->name ?? 'User' }}</td>
                                    <td class="px-2 py-2">
                                        @if($referral->status === 'completed')
                                            <span class="text-brand-green font-medium">Successful</span>
                                        @else
                                            <span class="text-amber-600 font-medium">Pending</span>
                                        @endif
                                    </td>
                                    <td class="px-2 py-2 text-gray-500">{{ ($referral->completed_at ?? $referral->created_at)->format('M d, Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>

            <div class="card p-6">
                <h2 class="font-bold text-brand-dark mb-4">Account</h2>
                <p class="text-sm text-gray-600">{{ $user->name }}</p>
                <p class="text-sm text-gray-600">{{ $user->email }}</p>
                <a href="{{ route('orders.index') }}" class="inline-block mt-4 text-brand-green font-medium hover:text-brand-orange">View all orders →</a>
            </div>
        </div>

        @if(isset($pendingPayments) && $pendingPayments->count() > 0)
        <div class="card p-6 mb-8">
            <h2 class="font-bold text-brand-dark mb-4">Pending Payments</h2>
            <div class="space-y-3">
                @foreach($pendingPayments as $pending)
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-gray-100 pb-3 last:border-0 last:pb-0">
                    <div>
                        <p class="font-semibold">#{{ str_pad($pending->id, 6, '0', STR_PAD_LEFT) }}</p>
                        <p class="text-xs text-gray-500">₹{{ number_format($pending->total_amount, 0) }} · {{ $pending->created_at->format('M d, Y') }}</p>
                    </div>
                    <a href="{{ route('orders.resume-payment', $pending) }}" class="btn-primary text-sm text-center">Resume Payment</a>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <h2 class="text-xl font-bold text-brand-dark mb-4">Recent Orders</h2>
        @if($orders->count() > 0)
            <div class="space-y-4">
                @foreach($orders->take(5) as $order)
                <div class="card p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <p class="font-bold">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</p>
                        <p class="text-xs text-gray-500">{{ $order->created_at->format('M d, Y') }} · {{ strtoupper($order->payment_method) }}</p>
                    </div>
                    <div class="font-bold text-brand-orange">₹{{ number_format($order->total_amount, 0) }}</div>
                    <span class="text-xs uppercase bg-brand-green-soft text-brand-green px-3 py-1 rounded-full w-max">{{ $order->status }}</span>
                </div>
                @endforeach
            </div>
        @else
            <div class="card p-8 text-center text-gray-500">No orders yet.</div>
        @endif
    </div>
</div>
@endsection
