<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;

class CheckoutAddressService
{
    public function resolve(User $user, array $sessionSaved = []): array
    {
        if ($this->hasAddress($sessionSaved)) {
            return $this->mergeDefaults($user, $sessionSaved);
        }

        $draft = $user->checkoutDraft;
        if ($draft && $this->hasAddress([
            'shipping_pincode' => $draft->shipping_pincode,
            'shipping_address' => $draft->shipping_address,
        ])) {
            return $this->mergeDefaults($user, [
                'shipping_name' => $draft->shipping_name,
                'shipping_address' => $draft->shipping_address,
                'shipping_city' => $draft->shipping_city,
                'shipping_state' => $draft->shipping_state,
                'shipping_pincode' => $draft->shipping_pincode,
                'contact_number' => $draft->contact_number,
                'delivery_date' => $draft->delivery_date?->format('Y-m-d'),
                'customer_notes' => $draft->customer_notes,
                'coupon_code' => $draft->coupon_code,
                'referral_code' => $draft->referral_code,
                'payment_method' => $draft->payment_method,
            ]);
        }

        $lastOrder = Order::query()
            ->where('user_id', $user->id)
            ->whereNotNull('shipping_pincode')
            ->where(function ($query) {
                $query->where('payment_status', 'paid')
                    ->orWhere(function ($q) {
                        $q->where('payment_method', 'cod')
                            ->whereNotIn('status', ['draft', 'cancelled']);
                    });
            })
            ->latest()
            ->first();

        if ($lastOrder) {
            return $this->mergeDefaults($user, [
                'shipping_name' => $lastOrder->shipping_name,
                'shipping_address' => $lastOrder->shipping_address,
                'shipping_city' => $lastOrder->shipping_city,
                'shipping_state' => $lastOrder->shipping_state,
                'shipping_pincode' => $lastOrder->shipping_pincode,
                'contact_number' => $lastOrder->contact_number,
            ]);
        }

        return $this->mergeDefaults($user, []);
    }

    protected function hasAddress(array $data): bool
    {
        return filled($data['shipping_pincode'] ?? null)
            && filled($data['shipping_address'] ?? null);
    }

    protected function mergeDefaults(User $user, array $data): array
    {
        return array_merge([
            'shipping_name' => $user->name,
            'shipping_address' => '',
            'shipping_city' => '',
            'shipping_state' => '',
            'shipping_pincode' => '',
            'contact_number' => $user->phone ?? '',
            'delivery_date' => '',
            'customer_notes' => '',
            'coupon_code' => '',
            'referral_code' => '',
            'payment_method' => 'razorpay',
        ], array_filter($data, fn ($value) => $value !== null));
    }
}
