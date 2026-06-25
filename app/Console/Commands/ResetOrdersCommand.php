<?php

namespace App\Console\Commands;

use App\Models\CouponUsage;
use App\Models\Order;
use App\Models\Product;
use App\Models\Referral;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetOrdersCommand extends Command
{
    protected $signature = 'orders:reset {--force : Run without confirmation}';

    protected $description = 'Delete all orders and reset order/invoice numbering from 1';

    public function handle(): int
    {
        $count = Order::count();

        if ($count === 0) {
            $this->resetSequences();
            Setting::updateOrCreate(['key' => 'invoice_counter'], ['value' => '0']);

            $this->info('No orders found. Invoice counter reset to 0.');

            return self::SUCCESS;
        }

        if (! $this->option('force') && ! $this->confirm("Delete all {$count} orders and reset numbering? Product stock will be restored where applicable.")) {
            $this->warn('Cancelled.');

            return self::FAILURE;
        }

        DB::transaction(function () {
            $this->restoreStock();

            Referral::query()->whereNotNull('order_id')->update(['order_id' => null]);
            CouponUsage::query()->delete();
            Order::query()->delete();
        });

        $this->resetSequences();
        Setting::updateOrCreate(['key' => 'invoice_counter'], ['value' => '0']);

        $this->info("Removed {$count} orders. Next order will be #1; invoices start from 1 again.");

        return self::SUCCESS;
    }

    protected function restoreStock(): void
    {
        $orders = Order::with('items')->where('status', '!=', 'cancelled')->get();

        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                $product = Product::find($item->product_id);

                if ($product?->hasStockLimit()) {
                    Product::where('id', $product->id)->increment('stock', $item->quantity);
                }
            }
        }
    }

    protected function resetSequences(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE orders AUTO_INCREMENT = 1');
            DB::statement('ALTER TABLE order_items AUTO_INCREMENT = 1');
            DB::statement('ALTER TABLE coupon_usages AUTO_INCREMENT = 1');
        }

        if ($driver === 'sqlite') {
            DB::statement("DELETE FROM sqlite_sequence WHERE name IN ('orders', 'order_items', 'coupon_usages')");
        }
    }
}
