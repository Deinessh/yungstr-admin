<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class CashfreeService
{
    public function __construct(private SettingService $settings) {}

    public function isConfigured(): bool
    {
        return $this->settings->cashfreeAppId() && $this->settings->cashfreeSecretKey();
    }

    public function isEnabled(): bool
    {
        return $this->settings->cashfreeEnabled() && $this->isConfigured();
    }

    public function mode(): string
    {
        return $this->settings->cashfreeEnvironment() === 'production' ? 'production' : 'sandbox';
    }

    public function apiBase(): string
    {
        return $this->mode() === 'production'
            ? 'https://api.cashfree.com'
            : 'https://sandbox.cashfree.com';
    }

    /**
     * @return array{order_id: string, payment_session_id: string}
     */
    public function createOrder(Order $order, User $user): array
    {
        $cfOrderId = 's7_'.$order->id.'_'.Str::lower(Str::random(6));

        $response = Http::withHeaders($this->headers())
            ->post($this->apiBase().'/pg/orders', [
                'order_id' => $cfOrderId,
                'order_amount' => round((float) $order->total_amount, 2),
                'order_currency' => 'INR',
                'customer_details' => [
                    'customer_id' => (string) $user->id,
                    'customer_email' => $user->email,
                    'customer_phone' => preg_replace('/\D/', '', $order->contact_number) ?: '9999999999',
                    'customer_name' => $order->shipping_name ?: $user->name,
                ],
                'order_meta' => [
                    'return_url' => route('payment.cashfree.return', ['order' => $order->id]),
                    'notify_url' => route('webhooks.cashfree'),
                ],
                'order_note' => 'S7 MilletCo order #'.$order->id,
            ]);

        if (! $response->successful()) {
            throw new \RuntimeException('Cashfree order creation failed: '.$response->body());
        }

        $data = $response->json();

        return [
            'order_id' => $data['order_id'] ?? $cfOrderId,
            'payment_session_id' => $data['payment_session_id'] ?? '',
        ];
    }

    public function fetchOrder(string $cashfreeOrderId): ?array
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->apiBase().'/pg/orders/'.$cashfreeOrderId);

        if (! $response->successful()) {
            return null;
        }

        return $response->json();
    }

    public function isPaid(?array $orderPayload): bool
    {
        $status = strtoupper((string) ($orderPayload['order_status'] ?? ''));

        return in_array($status, ['PAID', 'SUCCESS'], true);
    }

    /**
     * @return array<string, string>
     */
    protected function headers(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'x-api-version' => '2023-08-01',
            'x-client-id' => $this->settings->cashfreeAppId(),
            'x-client-secret' => $this->settings->cashfreeSecretKey(),
        ];
    }
}
