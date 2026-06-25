<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Referral;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardAnalyticsService
{
    public function summary(int $days = 30): array
    {
        $start = now()->subDays($days - 1)->startOfDay();
        $previousStart = $start->copy()->subDays($days);
        $orders = Order::visibleToCustomer()->where('created_at', '>=', $start)->get();
        $previousOrders = Order::visibleToCustomer()
            ->whereBetween('created_at', [$previousStart, $start->copy()->subSecond()])
            ->get();

        $revenue = $orders->sum('total_amount');
        $previousRevenue = $previousOrders->sum('total_amount');
        $orderCount = $orders->count();
        $previousOrderCount = $previousOrders->count();
        $paidCount = $orders->where('payment_status', 'paid')->count()
            + $orders->where('payment_method', 'cod')->whereNotIn('status', ['cancelled'])->count();
        $successRate = $orderCount > 0 ? round(($paidCount / $orderCount) * 100, 1) : 0;

        return [
            'revenue' => $revenue,
            'revenue_change' => $this->percentChange($previousRevenue, $revenue),
            'orders' => $orderCount,
            'orders_change' => $this->percentChange($previousOrderCount, $orderCount),
            'avg_order_value' => $orderCount > 0 ? round($revenue / $orderCount, 0) : 0,
            'avg_change' => $this->percentChange(
                $previousOrderCount > 0 ? $previousRevenue / $previousOrderCount : 0,
                $orderCount > 0 ? $revenue / $orderCount : 0
            ),
            'success_rate' => $successRate,
        ];
    }

    public function chartData(int $days = 30): array
    {
        $start = now()->subDays($days - 1)->startOfDay();
        $rows = Order::visibleToCustomer()
            ->where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as day, COUNT(*) as orders, SUM(total_amount) as revenue')
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->keyBy('day');

        $labels = [];
        $revenues = [];
        $orders = [];

        for ($i = 0; $i < $days; $i++) {
            $day = $start->copy()->addDays($i)->format('Y-m-d');
            $labels[] = Carbon::parse($day)->format('M d');
            $row = $rows->get($day);
            $revenues[] = (float) ($row->revenue ?? 0);
            $orders[] = (int) ($row->orders ?? 0);
        }

        return compact('labels', 'revenues', 'orders');
    }

    public function orderStatusBreakdown(): array
    {
        $base = Order::visibleToCustomer();

        return [
            'paid' => (clone $base)->where('payment_status', 'paid')->count(),
            'pending' => (clone $base)->where('payment_status', 'pending')->where('payment_method', '!=', 'cod')->count(),
            'cod' => (clone $base)->where('payment_method', 'cod')->whereNotIn('status', ['cancelled'])->count(),
            'failed' => (clone $base)->where('payment_status', 'failed')->count(),
        ];
    }

    /** @return array<string, int> */
    public function fulfillmentStatusCounts(): array
    {
        $base = Order::visibleToCustomer();

        return [
            'delivered' => (clone $base)->where('status', 'delivered')->count(),
            'processing' => (clone $base)->where('status', 'confirmed')->count(),
            'shipped' => (clone $base)->where('status', 'shipped')->count(),
            'pending' => (clone $base)->where('status', 'pending')->count(),
            'cancelled' => (clone $base)->where('status', 'cancelled')->count(),
        ];
    }

    public function topProducts(int $limit = 5): array
    {
        return OrderItem::query()
            ->select('product_id', DB::raw('SUM(quantity) as units'), DB::raw('SUM(quantity * price) as revenue'))
            ->whereHas('order', fn ($q) => $q->visibleToCustomer())
            ->with('product:id,name')
            ->groupBy('product_id')
            ->orderByDesc('units')
            ->limit($limit)
            ->get()
            ->map(fn ($row) => [
                'name' => $row->product?->name ?? 'Unknown',
                'units' => (int) $row->units,
                'revenue' => (float) $row->revenue,
            ])
            ->all();
    }

    public function referralFunnel(): array
    {
        $codesIssued = User::whereNotNull('referral_code')->count();
        $redemptions = Referral::where('status', 'completed')->count();
        $rewardsEarned = Coupon::where('is_referral_reward', true)->where('is_active', true)->count();

        return [
            'codes_issued' => $codesIssued,
            'redemptions' => $redemptions,
            'rewards_earned' => $rewardsEarned,
        ];
    }

    private function percentChange(float $previous, float $current): ?float
    {
        if ($previous <= 0) {
            return $current > 0 ? 100.0 : null;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }
}
