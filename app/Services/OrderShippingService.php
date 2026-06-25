<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;

class OrderShippingService
{
    public function __construct(
        private VelocityShippingService $velocity,
        private InvoiceService $invoices,
        private SettingService $settings,
        private OrderNotificationService $notifications,
    ) {}

    public function processConfirmedOrder(Order $order): void
    {
        if (! filter_var($this->settings->get('velocity_auto_ship', '1'), FILTER_VALIDATE_BOOLEAN)) {
            $this->invoices->ensureInvoice($order);
            $this->notifications->sendOrderConfirmation($order->fresh());

            return;
        }

        if (! $this->velocity->isEnabled()) {
            $this->invoices->ensureInvoice($order);
            $this->notifications->sendOrderConfirmation($order->fresh());

            return;
        }

        if ($order->awb_code || in_array($order->shipping_status, ['manifested', 'in_transit', 'delivered'], true)) {
            return;
        }

        try {
            $payload = $this->velocity->createForwardShipment($order);

            $trackingUrl = data_get($payload, 'tracking_data.track_url')
                ?? data_get($payload, 'track_url');

            $order->update([
                'awb_code' => $payload['awb_code'] ?? null,
                'velocity_order_id' => $payload['order_id'] ?? null,
                'velocity_shipment_id' => $payload['shipment_id'] ?? null,
                'carrier_name' => $payload['courier_name'] ?? null,
                'label_url' => $payload['label_url'] ?? null,
                'tracking_url' => $trackingUrl,
                'shipping_status' => ! empty($payload['awb_code']) ? 'manifested' : 'pending',
                'shipping_error' => null,
                'status' => ! empty($payload['awb_code']) ? 'shipped' : $order->status,
            ]);

            $this->invoices->ensureInvoice($order);
            $this->notifications->sendOrderConfirmation($order->fresh());
        } catch (\Throwable $e) {
            Log::error('Auto shipping failed', ['order_id' => $order->id, 'error' => $e->getMessage()]);
            $order->update([
                'shipping_status' => 'failed',
                'shipping_error' => $e->getMessage(),
            ]);
            $this->invoices->ensureInvoice($order);
            $this->notifications->sendOrderConfirmation($order->fresh());
        }
    }

    public function syncTracking(Order $order): void
    {
        if (! $order->awb_code) {
            return;
        }

        try {
            $result = $this->velocity->trackShipment($order->awb_code);
            $shipmentStatus = data_get($result, 'tracking_data.shipment_status')
                ?? data_get($result, 'tracking_data.shipment_track.0.current_status');

            $trackUrl = data_get($result, 'tracking_data.track_url');

            $updates = [
                'shipping_status' => $this->mapTrackingStatus($shipmentStatus),
            ];

            if ($trackUrl) {
                $updates['tracking_url'] = $trackUrl;
            }

            if (in_array($updates['shipping_status'], ['delivered'], true)) {
                $updates['status'] = 'delivered';
            } elseif (in_array($updates['shipping_status'], ['in_transit', 'out_for_delivery', 'picked_up'], true)) {
                $updates['status'] = 'shipped';
            }

            $order->update($updates);
        } catch (\Throwable $e) {
            Log::warning('Tracking sync failed', ['order_id' => $order->id, 'error' => $e->getMessage()]);
        }
    }

    public function retryShipment(Order $order): void
    {
        $order->update([
            'shipping_status' => 'pending',
            'shipping_error' => null,
        ]);

        $this->processConfirmedOrder($order->fresh());
    }

    private function mapTrackingStatus(?string $status): string
    {
        $status = strtolower((string) $status);

        return match (true) {
            str_contains($status, 'deliver') => 'delivered',
            str_contains($status, 'out for delivery') => 'out_for_delivery',
            str_contains($status, 'transit'), str_contains($status, 'picked') => 'in_transit',
            str_contains($status, 'cancel') => 'cancelled',
            default => 'manifested',
        };
    }
}
