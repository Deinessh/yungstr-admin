@extends('admin.layout')

@section('title', 'Referrals')
@section('heading', 'Referral Program')

@section('content')
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
    @foreach([
        ['label' => 'Codes Issued', 'value' => $funnel['codes_issued'], 'icon' => 'fa-qrcode'],
        ['label' => 'Successful Referrals', 'value' => $funnel['redemptions'], 'icon' => 'fa-user-check'],
        ['label' => 'Active Rewards', 'value' => $rewardCoupons, 'icon' => 'fa-gift'],
    ] as $stat)
    <div class="admin-stat-card">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[11px] text-brand-brown/70 uppercase tracking-wide font-semibold">{{ $stat['label'] }}</p>
                <p class="text-2xl font-extrabold text-brand-brown mt-1">{{ $stat['value'] }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-admin-peach flex items-center justify-center text-brand-orange">
                <i class="fas {{ $stat['icon'] }}"></i>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    <div class="card xl:col-span-2 overflow-hidden">
        <div class="px-5 py-4 border-b border-orange-100/80 font-bold text-brand-brown bg-admin-peach-light">Referrers</div>
        <table class="admin-table w-full text-sm">
            <thead>
                <tr>
                    <th class="text-left">Customer</th>
                    <th>Code</th>
                    <th>Referrals</th>
                    <th class="text-left hidden md:table-cell">Coupon Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($referrers as $user)
                @php $coupon = $user->coupons->first(); @endphp
                <tr>
                    <td class="px-4 py-3">
                        <p class="font-semibold text-brand-brown">{{ $user->name }}</p>
                        <p class="text-xs text-brand-brown/60">{{ $user->email }}</p>
                    </td>
                    <td class="px-4 py-3 text-center font-mono text-xs">{{ $user->referral_code ?: '—' }}</td>
                    <td class="px-4 py-3 text-center font-bold text-brand-orange">{{ $user->successful_referrals_count }}</td>
                    <td class="px-4 py-3 hidden md:table-cell">
                        @if($coupon)
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $coupon->is_active ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ $coupon->code }} · {{ $coupon->is_active ? 'Active' : 'Pending' }}
                            </span>
                        @else
                            <span class="text-xs text-brand-brown/50">No coupon yet</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-4 py-10 text-center text-brand-brown/50">No referral activity yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">{{ $referrers->links() }}</div>
    </div>

    <div class="card overflow-hidden">
        <div class="px-5 py-4 border-b border-orange-100/80 font-bold text-brand-brown bg-admin-peach-light">Recent Referrals</div>
        <div class="divide-y divide-orange-50">
            @forelse($recentReferrals as $referral)
            <div class="px-5 py-3">
                <p class="text-sm font-semibold text-brand-brown">{{ $referral->referrer?->name }} → {{ $referral->referredUser?->name }}</p>
                <p class="text-xs text-brand-brown/60 mt-1">{{ ucfirst($referral->status) }} · {{ $referral->created_at->format('M d, Y') }}</p>
            </div>
            @empty
            <p class="px-5 py-8 text-brand-brown/50 text-center text-sm">No referrals recorded.</p>
            @endforelse
        </div>
        <div class="p-4 border-t border-orange-50">
            <a href="{{ route('admin.referral-settings.edit') }}" class="text-sm text-brand-orange font-semibold hover:underline">Configure referral settings →</a>
        </div>
    </div>
</div>
@endsection
