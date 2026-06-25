<?php

namespace App\Services;

use Razorpay\Api\Api;

class RazorpayService
{
    public function __construct(private SettingService $settings) {}

    public function isConfigured(): bool
    {
        return $this->settings->razorpayKeyId() && $this->settings->razorpayKeySecret();
    }

    public function isEnabled(): bool
    {
        return $this->settings->razorpayEnabled();
    }

    public function api(): Api
    {
        return new Api(
            $this->settings->razorpayKeyId(),
            $this->settings->razorpayKeySecret()
        );
    }

    public function createOrder(float $amountInRupees, string $receipt, array $notes = []): array
    {
        $order = $this->api()->order->create([
            'receipt' => $receipt,
            'amount' => (int) round($amountInRupees * 100),
            'currency' => 'INR',
            'notes' => $notes,
        ]);

        return $order->toArray();
    }

    public function verifySignature(string $orderId, string $paymentId, string $signature): bool
    {
        try {
            $this->api()->utility->verifyPaymentSignature([
                'razorpay_order_id' => $orderId,
                'razorpay_payment_id' => $paymentId,
                'razorpay_signature' => $signature,
            ]);

            return true;
        } catch (\Throwable) {
            return false;
        }
    }
}
