<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SettingService;
use Illuminate\Http\Request;

class ReferralSettingController extends Controller
{
    public function __construct(private SettingService $settings) {}

    public function edit()
    {
        return view('admin.referral-settings.edit', [
            'settings' => [
                'referrals_required_to_unlock' => $this->settings->referralsRequiredToUnlock(),
                'referral_reward_type' => $this->settings->referralRewardType(),
                'referral_reward_discount' => $this->settings->referralRewardDiscount(),
                'referral_reward_expiry_days' => $this->settings->referralRewardExpiryDays(),
            ],
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'referrals_required_to_unlock' => 'required|integer|min:1',
            'referral_reward_type' => 'required|in:fixed,percent',
            'referral_reward_discount' => 'required|numeric|min:0',
            'referral_reward_expiry_days' => 'required|integer|min:1|max:365',
        ]);

        $this->settings->setMany($data);

        return back()->with('success', 'Referral settings saved.');
    }
}
