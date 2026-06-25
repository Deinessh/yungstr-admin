<?php

namespace App\Services;

use App\Models\Order;
use App\Support\AddressParser;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VelocityShippingService
{
    private const BASE_URL = 'https://shazam.velocity.in/custom/api/v1';

    public function __construct(private SettingService $settings) {}

    public function isEnabled(): bool
    {
        return filter_var($this->settings->get('velocity_enabled', '0'), FILTER_VALIDATE_BOOLEAN)
            && $this->settings->get('velocity_username')
            && $this->settings->get('velocity_password')
            && $this->settings->get('velocity_warehouse_id');
    }

    public function testConnection(): array
    {
        if (! $this->settings->get('velocity_username')) {
            return ['ok' => false, 'message' => 'API Username is missing. Enter your Velocity login (+91…) and save settings first.'];
        }

        if (! $this->settings->get('velocity_password')) {
            return ['ok' => false, 'message' => 'API Password is not saved. Enter your Velocity password in the field above, click Save Shipping Settings, then test again.'];
        }

        if (! $this->settings->get('velocity_warehouse_id')) {
            return ['ok' => false, 'message' => 'Warehouse ID is missing. Copy it from the Velocity portal and save settings first.'];
        }

        try {
            $token = $this->getToken(true);

            return ['ok' => true, 'message' => 'Connected successfully. Token expires at '.($token['expires_at'] ?? 'unknown').'.'];
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => $e->getMessage()];
        }
    }

    public function createForwardShipment(Order $order): array
    {
        if (! $this->isEnabled()) {
            throw new \RuntimeException('Velocity Shipping is not configured.');
        }

        $order->loadMissing('items.product', 'user');

        $pincode = $order->shipping_pincode ?: AddressParser::extractPincode($order->shipping_address);
        if (! $pincode) {
            throw new \RuntimeException('Shipping pincode is required for Velocity shipment.');
        }

        $warehousePincode = $this->settings->get('velocity_warehouse_pincode');
        if ($warehousePincode) {
            $this->checkServiceability($warehousePincode, $pincode, $order);
        }

        [$firstName, $lastName] = AddressParser::splitName($order->shipping_name ?: $order->user->name);

        $payload = [
            'order_id' => 'S7-'.$order->id,
            'order_date' => $order->created_at->format('Y-m-d H:i'),
            'carrier_id' => $this->settings->get('velocity_default_carrier_id') ?: '',
            'billing_customer_name' => $firstName,
            'billing_last_name' => $lastName,
            'billing_address' => $order->shipping_address,
            'billing_city' => $order->shipping_city ?: 'City',
            'billing_pincode' => $pincode,
            'billing_state' => $order->shipping_state ?: 'State',
            'billing_country' => 'India',
            'billing_email' => $order->user->email,
            'billing_phone' => preg_replace('/\D/', '', $order->contact_number ?: ''),
            'shipping_is_billing' => true,
            'print_label' => true,
            'order_items' => $order->items->map(function ($item) {
                return [
                    'name' => $item->product?->name ?? 'Product',
                    'sku' => 'PROD-'.($item->product_id ?: $item->id),
                    'units' => $item->quantity,
                    'selling_price' => (float) $item->price,
                    'discount' => 0,
                    'tax' => 0,
                ];
            })->values()->all(),
            'payment_method' => $order->payment_method === 'cod' ? 'COD' : 'PREPAID',
            'sub_total' => (float) $order->total_amount,
            'cod_collectible' => $order->payment_method === 'cod' ? (float) $order->total_amount : 0,
            'length' => (float) $this->settings->get('velocity_package_length', 20),
            'breadth' => (float) $this->settings->get('velocity_package_breadth', 15),
            'height' => (float) $this->settings->get('velocity_package_height', 10),
            'weight' => max(0.1, $this->calculateOrderWeight($order)),
            'pickup_location' => $this->settings->get('velocity_pickup_location', 'Primary Warehouse'),
            'warehouse_id' => $this->settings->get('velocity_warehouse_id'),
        ];

        $response = $this->request('POST', '/forward-order-orchestration', $payload);

        if (($response['status'] ?? 0) != 1 && ($response['status'] ?? '') !== 'SUCCESS') {
            $message = $response['message'] ?? json_encode($response);
            throw new \RuntimeException('Velocity shipment failed: '.$message);
        }

        return $response['payload'] ?? $response;
    }

    public function trackShipment(string $awb): array
    {
        $response = $this->request('POST', '/order-tracking', ['awbs' => [$awb]]);

        return $response['result'][$awb] ?? $response;
    }

    public function cancelShipment(string $awb): array
    {
        return $this->request('POST', '/cancel-order', ['awbs' => [$awb]]);
    }

    private function checkServiceability(string $from, string $to, Order $order): void
    {
        $paymentMode = $order->payment_method === 'cod' ? 'cod' : 'prepaid';

        $response = $this->request('POST', '/serviceability', [
            'from' => $from,
            'to' => $to,
            'payment_mode' => $paymentMode,
            'shipment_type' => 'forward',
        ]);

        $results = data_get($response, 'result.serviceability_results', []);

        if (empty($results) && ($response['status'] ?? '') !== 'SUCCESS') {
            throw new \RuntimeException('Lane not serviceable between warehouse and customer pincode.');
        }
    }

    private function calculateOrderWeight(Order $order): float
    {
        $weight = 0.0;

        foreach ($order->items as $item) {
            $itemWeight = (float) ($item->product?->weight_kg ?? 0.5);
            $weight += $itemWeight * $item->quantity;
        }

        $default = (float) $this->settings->get('velocity_package_weight', 0.5);

        return max($default, round($weight, 2));
    }

    private function getToken(bool $forceRefresh = false): array
    {
        $cacheKey = 'velocity.auth_token';

        if (! $forceRefresh && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $username = trim((string) $this->settings->get('velocity_username'));
        $password = (string) $this->settings->get('velocity_password');

        if ($username === '' || $password === '') {
            throw new \RuntimeException('Velocity API username and password must be saved in Admin → Shipping before connecting.');
        }

        $response = Http::timeout(30)
            ->acceptJson()
            ->post(self::BASE_URL.'/auth-token', [
                'username' => $username,
                'password' => $password,
            ]);

        $data = $response->json() ?? [];

        if (empty($data['token'])) {
            $hint = 'Check that API Username (+91…) and Password match your Velocity / Shazam account.';
            if ($response->successful()) {
                throw new \RuntimeException('Velocity rejected the login (no token returned). '.$hint);
            }

            throw new \RuntimeException('Velocity auth failed ('.$response->status().'). '.$hint);
        }

        Cache::put($cacheKey, $data, now()->addHours(23));

        return $data;
    }

    private function request(string $method, string $path, array $payload = []): array
    {
        $token = $this->getToken()['token'];

        $response = Http::timeout(60)
            ->acceptJson()
            ->withHeaders(['Authorization' => $token])
            ->{$method === 'POST' ? 'post' : 'get'}(self::BASE_URL.$path, $payload);

        if ($response->status() === 401) {
            Cache::forget('velocity.auth_token');
            $token = $this->getToken(true)['token'];
            $response = Http::timeout(60)
                ->acceptJson()
                ->withHeaders(['Authorization' => $token])
                ->post(self::BASE_URL.$path, $payload);
        }

        if (! $response->successful()) {
            Log::warning('Velocity API error', ['path' => $path, 'body' => $response->body()]);
            throw new \RuntimeException('Velocity API error ('.$response->status().'): '.$response->body());
        }

        return $response->json() ?? [];
    }
}
