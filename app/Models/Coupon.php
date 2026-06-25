<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    protected $fillable = [
        'code', 'name', 'type', 'value', 'min_order_amount', 'max_uses',
        'used_count', 'is_active', 'is_system', 'is_referral_reward', 'is_personal_referral', 'user_id', 'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
            'is_system' => 'boolean',
            'is_referral_reward' => 'boolean',
            'is_personal_referral' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function isValidFor(float $subtotal, ?User $user = null): bool
    {
        return $this->invalidReason($subtotal, $user) === null;
    }

    public function invalidReason(float $subtotal, ?User $user = null): ?string
    {
        if (! $this->is_active) {
            if ($this->is_personal_referral) {
                return 'This coupon activates after you refer '.$this->requiredReferrals().' friends.';
            }

            return 'This coupon is currently disabled.';
        }

        if ($this->isExpired()) {
            return 'This coupon expired on '.$this->expires_at->format('M d, Y').'.';
        }

        if ($subtotal < $this->min_order_amount) {
            return 'Minimum order amount of ₹'.number_format($this->min_order_amount, 0).' required.';
        }

        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) {
            return 'This coupon has reached its usage limit.';
        }

        if ($this->user_id && $user && $this->user_id !== $user->id) {
            return 'This coupon is not valid for your account.';
        }

        return null;
    }

    public function isExpired(): bool
    {
        if (! $this->expires_at) {
            return false;
        }

        return now()->greaterThan($this->expires_at);
    }

    public function expiryLabel(): string
    {
        if (! $this->expires_at) {
            return 'No expiry';
        }

        if ($this->isExpired()) {
            return 'Expired '.$this->expires_at->format('M d, Y');
        }

        return 'Until '.$this->expires_at->format('M d, Y');
    }

    public function calculateDiscount(float $subtotal): float
    {
        if ($this->type === 'percent') {
            return round($subtotal * ($this->value / 100), 2);
        }

        return min($this->value, $subtotal);
    }

    protected function requiredReferrals(): int
    {
        return (int) app(\App\Services\SettingService::class)->referralsRequiredToUnlock();
    }
}
