<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Referral;
use App\Models\User;
use App\Services\DashboardAnalyticsService;

class ReferralController extends Controller
{
    public function __construct(private DashboardAnalyticsService $analytics) {}

    public function index()
    {
        $funnel = $this->analytics->referralFunnel();
        $referrers = User::where('is_admin', false)
            ->where(function ($q) {
                $q->whereNotNull('referral_code')
                    ->orWhere('successful_referrals_count', '>', 0);
            })
            ->with(['coupons' => fn ($q) => $q->where('is_personal_referral', true)->latest()])
            ->orderByDesc('successful_referrals_count')
            ->paginate(20);

        $recentReferrals = Referral::with(['referrer:id,name,email', 'referredUser:id,name,email'])
            ->latest()
            ->take(10)
            ->get();

        $rewardCoupons = Coupon::where('is_referral_reward', true)->count();

        return view('admin.referrals.index', compact('funnel', 'referrers', 'recentReferrals', 'rewardCoupons'));
    }
}
