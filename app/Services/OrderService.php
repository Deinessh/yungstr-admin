<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        private CartService $cart,
        private CouponService $coupons,
        private ReferralService $referrals,
        private SettingService $settings,
        private OrderShippingService $shipping,
    ) {}

    public function createFromCheckout(User $user, array $data): Order
    {
        $cart = $this->cart->getCart();

        if (empty($cart)) {
            throw new \RuntimeException('Cart is empty.');
        }

        foreach ($cart as $item) {
            $product = Product::find($item['id']);
            if (! $product || ! $product->isPurchasable() || ! $product->hasAvailableStock($item['quantity'])) {
                $message = $product?->isComingSoon()
                    ? "{$item['name']} is coming soon and cannot be ordered yet."
                    : "Insufficient stock for {$item['name']}.";

                throw new \RuntimeException($message);
            }
        }

        $discount = 0;
        $coupon = null;
        $couponCode = $data['coupon_code'] ?? null;

        if ($couponCode) {
            $validation = $this->coupons->validate($couponCode, $this->cart->subtotal(), $user);
            if (! $validation['valid']) {
                throw new \RuntimeException($validation['message']);
            }
            $coupon = $validation['coupon'];
            $discount = $validation['discount'];
        }

        $location = [
            'pincode' => $data['shipping_pincode'] ?? null,
            'city' => $data['shipping_city'] ?? null,
            'state' => $data['shipping_state'] ?? null,
        ];

        $this->cart->setShippingLocation($location);

        $totals = $this->cart->totals(null, $discount, $location);
        $quote = $totals['shipping_quote'];

        return DB::transaction(function () use ($user, $data, $cart, $coupon, $discount, $totals, $quote) {
            $order = Order::create([
                'user_id' => $user->id,
                'subtotal' => $totals['subtotal'],
                'shipping_fee' => $totals['shipping'],
                'shipping_zone_id' => $quote['zone_id'] ?? null,
                'shipping_zone_name' => $quote['zone_name'] ?? null,
                'discount_amount' => $totals['discount'],
                'total_amount' => $totals['total'],
                'status' => 'pending',
                'payment_method' => $data['payment_method'],
                'payment_status' => $data['payment_method'] === 'cod' ? 'pending' : 'pending',
                'coupon_id' => $coupon?->id,
                'coupon_code' => $coupon?->code,
                'referral_code_used' => $user->referrer?->referral_code,
                'shipping_address' => $data['shipping_address'],
                'shipping_name' => $data['shipping_name'] ?? $user->name,
                'shipping_city' => $data['shipping_city'] ?? null,
                'shipping_state' => $data['shipping_state'] ?? null,
                'shipping_pincode' => $data['shipping_pincode'] ?? null,
                'contact_number' => $data['contact_number'],
                'delivery_date' => ! empty($data['delivery_date']) ? $data['delivery_date'] : null,
                'customer_notes' => $data['customer_notes'] ?? null,
                'cart_snapshot' => $cart,
            ]);

            foreach ($cart as $item) {
                $product = Product::with('comboProducts')->find($item['id']);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'product_name' => $product?->name ?? $item['name'],
                    'product_weight' => $product?->weight,
                    'combo_includes' => $product?->comboIncludesSummary(),
                    'mrp' => $product?->mrp,
                    'pick_any_selections' => $item['pick_any_sets'] ?? null,
                ]);

                if ($product?->hasStockLimit()) {
                    Product::where('id', $item['id'])->decrement('stock', $item['quantity']);
                }
            }

            $this->syncUserPhone($user, $data['contact_number'] ?? null);

            if ($data['payment_method'] === 'cod') {
                $this->finalizeOrder($order, $coupon);
            }

            return $order;
        });
    }

    public function finalizeOrder(Order $order, ?Coupon $coupon = null): void
    {
        $order->update([
            'status' => 'confirmed',
            'payment_status' => $order->payment_method === 'cod' ? 'pending' : 'paid',
        ]);

        $coupon = $coupon ?? $order->coupon;
        if ($coupon) {
            $alreadyUsed = \App\Models\CouponUsage::where('order_id', $order->id)->exists();
            if (! $alreadyUsed) {
                $this->coupons->apply($coupon, $order->user, $order->id);
            }
        }

        $this->referrals->handleCompletedOrder($order);
        $this->cart->clearDraft($order->user_id);
        session()->forget('cart');
        session()->forget('checkout_form');
        session()->forget('shipping_location');

        $this->shipping->processConfirmedOrder($order->fresh());
    }

    public function markPaymentFailed(Order $order): void
    {
        foreach ($order->items as $item) {
            $product = Product::find($item->product_id);
            if ($product?->hasStockLimit()) {
                Product::where('id', $item->product_id)->increment('stock', $item->quantity);
            }
        }

        $order->update([
            'status' => 'cancelled',
            'payment_status' => 'failed',
        ]);
    }

    protected function syncUserPhone(User $user, ?string $contactNumber): void
    {
        if ($user->phone || ! $contactNumber) {
            return;
        }

        $phone = preg_replace('/\D/', '', $contactNumber);

        if (strlen($phone) === 12 && str_starts_with($phone, '91')) {
            $phone = substr($phone, 2);
        }

        if (! preg_match('/^\d{10}$/', $phone)) {
            return;
        }

        if (User::where('phone', $phone)->where('id', '!=', $user->id)->exists()) {
            return;
        }

        $user->update(['phone' => $phone]);
    }
}
