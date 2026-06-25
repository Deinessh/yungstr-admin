<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\OrderShippingService;
use Illuminate\Console\Command;

class SyncVelocityTrackingCommand extends Command
{
    protected $signature = 'velocity:sync-tracking';

    protected $description = 'Sync Velocity tracking status for shipped orders';

    public function handle(OrderShippingService $shipping): int
    {
        $orders = Order::query()
            ->whereNotNull('awb_code')
            ->whereNotIn('shipping_status', ['delivered', 'cancelled'])
            ->latest()
            ->limit(100)
            ->get();

        foreach ($orders as $order) {
            $shipping->syncTracking($order);
            $this->line("Synced order #{$order->id} ({$order->awb_code})");
        }

        $this->info('Tracking sync complete.');

        return self::SUCCESS;
    }
}
