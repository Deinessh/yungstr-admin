<?php

namespace App\Services;

class CartService
{
    public function __construct(
        private SettingService $settings,
        private ShippingZoneService $shippingZones,
    ) {}

    public function getCart(): array
    {
        return session()->get('cart', []);
    }

    public function shippingLocation(): ?array
    {
        $location = session('shipping_location');

        if (! is_array($location) || empty($location['pincode'])) {
            return null;
        }

        return [
            'pincode' => $location['pincode'],
            'city' => $location['city'] ?? null,
            'state' => $location['state'] ?? null,
        ];
    }

    public function setShippingLocation(array $location): void
    {
        session()->put('shipping_location', [
            'pincode' => $location['pincode'] ?? null,
            'city' => $location['city'] ?? null,
            'state' => $location['state'] ?? null,
        ]);
    }

    public function enrichFromProducts(array $cart): array
    {
        foreach ($cart as $id => &$item) {
            $product = \App\Models\Product::find($id);
            if (! $product) {
                $item['is_coming_soon'] = false;
                $item['is_purchasable'] = false;
                $item['checkout_blocked_reason'] = 'This product is no longer available.';

                continue;
            }

            $item['stock'] = $product->stock;
            $item['image'] = $product->image;
            $item['is_pick_any_combo'] = $product->is_pick_any_combo;
            $item['is_coming_soon'] = $product->isComingSoon();
            $item['is_purchasable'] = $product->isPurchasable();
            $item['checkout_blocked_reason'] = $this->checkoutBlockReason($product);
        }
        unset($item);

        return $cart;
    }

    /**
     * @return array<int, string> Product ID => reason
     */
    public function checkoutBlockers(array $cart): array
    {
        $blockers = [];

        foreach ($cart as $id => $item) {
            $product = \App\Models\Product::find($id);

            if ($reason = $this->checkoutBlockReason($product)) {
                $blockers[(int) $id] = $reason;

                continue;
            }

            if (! empty($item['pick_any_sets']) && is_array($item['pick_any_sets'])) {
                foreach ($item['pick_any_sets'] as $set) {
                    foreach ($set as $choice) {
                        $choiceProduct = \App\Models\Product::find($choice['product_id'] ?? 0);

                        if ($choiceProduct?->isComingSoon()) {
                            $blockers[(int) $id] = 'One or more selected products in '.$item['name'].' are coming soon.';

                            break 2;
                        }
                    }
                }
            }
        }

        return $blockers;
    }

    public function hasCheckoutBlockers(array $cart): bool
    {
        return count($this->checkoutBlockers($cart)) > 0;
    }

    /**
     * Remove items that cannot be checked out and persist the cart session.
     *
     * @return array<int, string> Removed product ID => reason
     */
    public function removeUnpurchasableItems(): array
    {
        $cart = $this->getCart();
        $blockers = $this->checkoutBlockers($cart);

        foreach (array_keys($blockers) as $productId) {
            unset($cart[$productId]);
        }

        session()->put('cart', $cart);

        return $blockers;
    }

    protected function checkoutBlockReason(?\App\Models\Product $product): ?string
    {
        if (! $product || ! $product->is_active) {
            return 'This product is no longer available.';
        }

        if ($product->isComingSoon()) {
            return $product->name.' is coming soon and cannot be checked out.';
        }

        if (! $product->isPurchasable()) {
            return $product->name.' is out of stock.';
        }

        return null;
    }

    public function subtotal(?array $cart = null): float
    {
        $cart = $cart ?? $this->getCart();
        $total = 0;

        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return round($total, 2);
    }

    public function shippingQuote(?array $cart = null, ?array $location = null): array
    {
        $subtotal = $this->subtotal($cart);
        $location = $location ?? $this->shippingLocation();

        if (! $location || empty($location['pincode'])) {
            return [
                'resolved' => false,
                'zone' => null,
                'zone_id' => null,
                'zone_name' => null,
                'shipping_fee' => null,
                'standard_shipping_fee' => null,
                'free_shipping_threshold' => null,
                'free_shipping_remaining' => null,
                'qualifies_for_free_shipping' => false,
                'message' => 'Enter your PIN code to see delivery charges.',
            ];
        }

        return $this->shippingZones->quote(
            $subtotal,
            $location['pincode'],
            $location['city'] ?? null,
            $location['state'] ?? null
        );
    }

    public function shippingFee(?array $cart = null, ?array $location = null): float
    {
        $quote = $this->shippingQuote($cart, $location);

        if (! $quote['resolved']) {
            throw new \RuntimeException('Please enter a valid delivery PIN code before placing your order.');
        }

        return (float) $quote['shipping_fee'];
    }

    public function totals(?array $cart = null, float $discount = 0, ?array $location = null): array
    {
        $cart = $cart ?? $this->getCart();
        $subtotal = $this->subtotal($cart);
        $quote = $this->shippingQuote($cart, $location);
        $shipping = $quote['resolved'] ? (float) $quote['shipping_fee'] : 0.0;
        $discount = min($discount, $subtotal);
        $total = max($subtotal + $shipping - $discount, 0);

        return array_merge(compact('subtotal', 'shipping', 'discount', 'total'), [
            'shipping_quote' => $quote,
        ]);
    }

    public function saveDraft(int $userId, array $data): void
    {
        \App\Models\CheckoutDraft::updateOrCreate(
            ['user_id' => $userId],
            array_merge($data, ['cart_data' => $this->getCart()])
        );
    }

    public function restoreDraft(int $userId): bool
    {
        $draft = \App\Models\CheckoutDraft::where('user_id', $userId)->first();

        if (! $draft || empty($draft->cart_data)) {
            return false;
        }

        session()->put('cart', $draft->cart_data);

        return true;
    }

    public function clearDraft(int $userId): void
    {
        \App\Models\CheckoutDraft::where('user_id', $userId)->delete();
    }
}
