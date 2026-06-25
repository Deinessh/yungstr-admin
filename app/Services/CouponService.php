<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\User;
use Illuminate\Support\Collection;

class CouponService
{
    public function findByCode(string $code): ?Coupon
    {
        return Coupon::where('code', strtoupper(trim($code)))->first();
    }

    public function validate(string $code, float $subtotal, ?User $user = null): array
    {
        $coupon = $this->findByCode($code);

        if (! $coupon) {
            return ['valid' => false, 'message' => 'Invalid coupon code.'];
        }

        $reason = $coupon->invalidReason($subtotal, $user);
        if ($reason) {
            return ['valid' => false, 'message' => $reason];
        }

        if ($user && $coupon->usages()->where('user_id', $user->id)->exists()) {
            return ['valid' => false, 'message' => 'You have already used this coupon.'];
        }

        return [
            'valid' => true,
            'coupon' => $coupon,
            'discount' => $coupon->calculateDiscount($subtotal),
            'message' => 'Coupon applied successfully.',
        ];
    }

    public function availableForCheckout(User $user, float $subtotal): Collection
    {
        return Coupon::query()
            ->where('is_active', true)
            ->where(function ($query) use ($user) {
                $query->whereNull('user_id')
                    ->orWhere('user_id', $user->id);
            })
            ->orderByDesc('is_personal_referral')
            ->orderBy('code')
            ->get()
            ->filter(function (Coupon $coupon) use ($subtotal, $user) {
                if ($coupon->invalidReason($subtotal, $user)) {
                    return false;
                }

                return ! $coupon->usages()->where('user_id', $user->id)->exists();
            })
            ->values();
    }

    public function apply(Coupon $coupon, User $user, int $orderId): void
    {
        CouponUsage::create([
            'coupon_id' => $coupon->id,
            'user_id' => $user->id,
            'order_id' => $orderId,
        ]);

        $coupon->increment('used_count');
    }
}
