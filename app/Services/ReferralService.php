<?php

namespace App\Services;

use App\Mail\ReferralCouponActivatedMail;
use App\Mail\ReferralCouponIssuedMail;
use App\Models\Coupon;
use App\Models\Referral;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class ReferralService
{
    public function __construct(
        private SettingService $settings,
        private CouponService $coupons,
        private MailConfigService $mailConfig,
    ) {}

    public function attachReferrer(User $user, ?string $referralCode): void
    {
        if (! $referralCode || $user->referred_by_user_id) {
            return;
        }

        $code = strtoupper(trim($referralCode));
        $referrer = User::where('referral_code', $code)->first();

        if (! $referrer || $referrer->id === $user->id) {
            return;
        }

        $user->update(['referred_by_user_id' => $referrer->id]);

        Referral::firstOrCreate(
            ['referrer_id' => $referrer->id, 'referred_user_id' => $user->id],
            ['status' => 'pending']
        );
    }

    public function attachReferrerOnRegistration(User $user, ?string $referralCode): void
    {
        $this->attachReferrer($user, $referralCode);
    }

    public function handleCompletedOrder(\App\Models\Order $order): void
    {
        $user = $order->user;

        $this->completeReferralOnFirstOrder($order);

        if (! $this->personalCoupon($user) && $user->hasPlacedOrder()) {
            $this->createPersonalReferralCoupon($user);
        }
    }

    public function completeReferralOnFirstOrder(\App\Models\Order $order): void
    {
        $referredUser = $order->user;

        if (! $referredUser->referred_by_user_id) {
            return;
        }

        $referral = Referral::where('referrer_id', $referredUser->referred_by_user_id)
            ->where('referred_user_id', $referredUser->id)
            ->where('status', 'pending')
            ->first();

        if (! $referral) {
            return;
        }

        $hasOtherSuccessfulOrder = $referredUser->orders()
            ->where('id', '!=', $order->id)
            ->where(function ($query) {
                $query->where('payment_status', 'paid')
                    ->orWhere(function ($q) {
                        $q->where('payment_method', 'cod')
                            ->whereNotIn('status', ['draft', 'cancelled']);
                    });
            })
            ->exists();

        if ($hasOtherSuccessfulOrder) {
            return;
        }

        $referral->update([
            'status' => 'completed',
            'order_id' => $order->id,
            'completed_at' => now(),
        ]);

        $referrer = User::find($referredUser->referred_by_user_id);
        if (! $referrer) {
            return;
        }

        $referrer->increment('successful_referrals_count');
        $this->checkAndActivatePersonalCoupon($referrer);
    }

    public function personalCoupon(User $user): ?Coupon
    {
        return $user->coupons()->where('is_personal_referral', true)->latest()->first();
    }

    public function createPersonalReferralCoupon(User $user): Coupon
    {
        $code = $this->generateReferralCode($user);

        $coupon = Coupon::create([
            'code' => $code,
            'name' => 'Personal Referral Reward',
            'type' => $this->settings->referralRewardType(),
            'value' => $this->settings->referralRewardDiscount(),
            'min_order_amount' => 0,
            'max_uses' => 1,
            'is_active' => false,
            'is_referral_reward' => true,
            'is_personal_referral' => true,
            'user_id' => $user->id,
        ]);

        $user->update(['referral_code' => $code]);

        $this->sendIssuedMail($user, $coupon);

        return $coupon;
    }

    protected function checkAndActivatePersonalCoupon(User $referrer): void
    {
        if ($referrer->successful_referrals_count < $this->settings->referralsRequiredToUnlock()) {
            return;
        }

        $coupon = $referrer->coupons()
            ->where('is_personal_referral', true)
            ->where('is_active', false)
            ->latest()
            ->first();

        if (! $coupon) {
            return;
        }

        $expiryDays = $this->settings->referralRewardExpiryDays();

        $coupon->update([
            'is_active' => true,
            'expires_at' => now()->addDays($expiryDays),
        ]);

        $referrer->update(['referral_unlocked' => true]);

        $this->sendActivatedMail($referrer, $coupon);
    }

    protected function generateReferralCode(User $user): string
    {
        $letters = strtolower(preg_replace('/[^a-z]/', '', substr($user->name, 0, 4)) ?? '');
        $letters = str_pad($letters, 4, 'x');

        do {
            $counter = $this->settings->nextReferralCodeCounter();
            $code = 's7'.$letters.$counter;
        } while (
            User::where('referral_code', strtoupper($code))->exists()
            || Coupon::where('code', strtoupper($code))->exists()
        );

        return strtoupper($code);
    }

    protected function sendIssuedMail(User $user, Coupon $coupon): void
    {
        try {
            $this->mailConfig->apply();
            Mail::to($user->email)->send(new ReferralCouponIssuedMail($user, $coupon));
        } catch (\Throwable) {
            // Mail failure should not block order completion.
        }
    }

    protected function sendActivatedMail(User $referrer, Coupon $coupon): void
    {
        try {
            $this->mailConfig->apply();
            Mail::to($referrer->email)->send(new ReferralCouponActivatedMail($referrer, $coupon));
        } catch (\Throwable) {
            //
        }
    }
}
