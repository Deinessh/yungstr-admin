@extends('admin.layout')

@section('title', 'Dashboard')
@section('heading', 'Dashboard')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <p class="text-sm text-gray-500">Store performance overview</p>
    <form method="GET" class="flex items-center gap-2">
        @foreach([7 => '7 days', 30 => '30 days', 90 => '90 days'] as $value => $label)
        <button type="submit" name="days" value="{{ $value }}"
            class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ $days === $value ? 'bg-admin-orange text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-200 hover:border-gray-300' }}">
            {{ $label }}
        </button>
        @endforeach
    </form>
</div>

{{-- Top stats --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4 mb-6">
    @php
    $topStats = [
        ['label' => 'Total Orders', 'value' => number_format($summary['orders']), 'change' => $summary['orders_change'], 'icon' => 'fa-shopping-bag', 'tone' => 'green', 'link' => route('admin.orders.index')],
        ['label' => 'Total Revenue', 'value' => '₹'.number_format($summary['revenue'], 0), 'change' => $summary['revenue_change'], 'icon' => 'fa-indian-rupee-sign', 'tone' => 'orange'],
        ['label' => 'Total Customers', 'value' => number_format($usersCount), 'change' => null, 'icon' => 'fa-users', 'tone' => 'green'],
        ['label' => 'Pending Orders', 'value' => number_format($pendingOrdersCount), 'change' => null, 'icon' => 'fa-clock', 'tone' => 'orange', 'link' => route('admin.orders.index', ['status' => 'pending']), 'linkText' => 'View all'],
        ['label' => 'Out of Stock', 'value' => number_format($lowStockCount), 'change' => null, 'icon' => 'fa-box-open', 'tone' => 'red', 'link' => route('admin.products.index'), 'linkText' => 'View products'],
    ];
    @endphp
    @foreach($topStats as $stat)
    <div class="admin-stat-card">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <p class="text-xs text-gray-500 font-medium">{{ $stat['label'] }}</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stat['value'] }}</p>
                @if($stat['change'] !== null)
                    <p class="text-xs mt-1 font-medium {{ $stat['change'] >= 0 ? 'text-admin-green' : 'text-red-600' }}">
                        {{ $stat['change'] >= 0 ? '+' : '' }}{{ $stat['change'] }}% vs previous period
                    </p>
                @elseif(!empty($stat['linkText']))
                    <a href="{{ $stat['link'] }}" class="text-xs mt-1 font-medium text-admin-orange hover:underline inline-block">{{ $stat['linkText'] }}</a>
                @endif
            </div>
            <div class="admin-stat-icon admin-stat-icon--{{ $stat['tone'] }}">
                <i class="fas {{ $stat['icon'] }}"></i>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
    {{-- Sales chart --}}
    <div class="card xl:col-span-2 p-5">
        <div class="flex items-center justify-between gap-3 mb-4">
            <h3 class="font-bold text-gray-900">Sales Overview</h3>
            <div class="flex items-center gap-4 text-xs text-gray-500">
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-admin-green"></span> Revenue</span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-admin-orange"></span> Orders</span>
            </div>
        </div>
        <div class="h-72">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    {{-- Top products --}}
    <div class="card p-5">
        <h3 class="font-bold text-gray-900 mb-4">Top Selling Products</h3>
        <div class="space-y-4">
            @forelse($topProducts as $product)
            <div class="flex items-center justify-between gap-3 text-sm">
                <div class="min-w-0 flex-1">
                    <p class="font-semibold text-gray-800 truncate">{{ $product['name'] }}</p>
                    <p class="text-xs text-gray-500">{{ $product['units'] }} units sold</p>
                </div>
                <span class="font-bold text-admin-orange shrink-0">₹{{ number_format($product['revenue'], 0) }}</span>
            </div>
            @empty
            <p class="text-sm text-gray-400 text-center py-8">No sales data yet.</p>
            @endforelse
        </div>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
    {{-- Recent orders --}}
    <div class="card xl:col-span-2 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-900">Recent Orders</h3>
            <a href="{{ route('admin.orders.index') }}" class="text-xs font-semibold text-admin-orange hover:underline">View all</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[520px]">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentOrders as $order)
                    @php
                        $pill = match($order->status) {
                            'delivered' => 'admin-status-pill--delivered',
                            'shipped' => 'admin-status-pill--shipped',
                            'confirmed' => 'admin-status-pill--processing',
                            'cancelled' => 'admin-status-pill--cancelled',
                            default => 'admin-status-pill--pending',
                        };
                    @endphp
                    <tr class="hover:bg-gray-50/80">
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.orders.show', $order) }}" class="font-semibold text-admin-green hover:underline">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</a>
                        </td>
                        <td class="px-4 py-3 text-gray-700">{{ $order->user->name ?? 'Guest' }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $order->created_at->format('M d, Y') }}</td>
                        <td class="px-4 py-3 font-semibold text-gray-900">₹{{ number_format($order->total_amount, 0) }}</td>
                        <td class="px-4 py-3"><span class="admin-status-pill {{ $pill }}">{{ ucfirst($order->status) }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-10 text-center text-gray-400">No orders yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Order status donut --}}
    <div class="card p-5">
        <h3 class="font-bold text-gray-900 mb-4">Order Status Overview</h3>
        <div class="h-48 flex items-center justify-center">
            <canvas id="statusChart"></canvas>
        </div>
        <div class="grid grid-cols-2 gap-2 mt-4 text-xs">
            @foreach([
                ['key' => 'delivered', 'label' => 'Delivered', 'color' => 'bg-admin-green'],
                ['key' => 'processing', 'label' => 'Processing', 'color' => 'bg-admin-orange'],
                ['key' => 'shipped', 'label' => 'Shipped', 'color' => 'bg-blue-500'],
                ['key' => 'cancelled', 'label' => 'Cancelled', 'color' => 'bg-gray-400'],
            ] as $item)
            <div class="flex items-center gap-2 text-gray-600">
                <span class="w-2 h-2 rounded-full {{ $item['color'] }}"></span>
                <span>{{ $item['label'] }}</span>
                <span class="ml-auto font-semibold text-gray-800">{{ $orderStatusCounts[$item['key']] ?? 0 }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    {{-- Low stock --}}
    <div class="card p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-gray-900">Low Stock Alert</h3>
            <a href="{{ route('admin.products.index') }}" class="text-xs font-semibold text-admin-orange hover:underline">Manage products</a>
        </div>
        <div class="space-y-3">
            @forelse($lowStockProducts as $product)
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-gray-50 border border-gray-100 flex items-center justify-center overflow-hidden shrink-0">
                    @if($product->image)
                        <img src="{{ asset($product->image) }}" alt="" class="max-h-full max-w-full object-contain">
                    @else
                        <i class="fas fa-box text-gray-300"></i>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $product->name }}</p>
                    <p class="text-xs text-gray-500">{{ $product->stock }} left in stock</p>
                </div>
                <span class="text-[11px] font-bold px-2 py-0.5 rounded-full {{ $product->stock <= 2 ? 'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-800' }}">
                    {{ $product->stock <= 2 ? 'Very Low' : 'Low' }}
                </span>
            </div>
            @empty
            <p class="text-sm text-gray-400 text-center py-6">All products are well stocked.</p>
            @endforelse
        </div>
    </div>

    {{-- Referral funnel --}}
    <div class="card p-5">
        <h3 class="font-bold text-gray-900 mb-4">Referral Funnel</h3>
        <div class="grid grid-cols-3 gap-3 mb-4">
            @foreach([
                ['label' => 'Codes Issued', 'value' => $referralFunnel['codes_issued']],
                ['label' => 'Redemptions', 'value' => $referralFunnel['redemptions']],
                ['label' => 'Rewards', 'value' => $referralFunnel['rewards_earned']],
            ] as $step)
            <div class="rounded-lg bg-admin-green-light/60 p-3 text-center">
                <p class="text-xl font-bold text-gray-900">{{ $step['value'] }}</p>
                <p class="text-[10px] text-gray-500 mt-0.5">{{ $step['label'] }}</p>
            </div>
            @endforeach
        </div>
        @php $maxFunnel = max($referralFunnel['codes_issued'], 1); @endphp
        <div class="space-y-2">
            @foreach([
                ['label' => 'Codes Issued', 'value' => $referralFunnel['codes_issued'], 'width' => 100],
                ['label' => 'Redemptions', 'value' => $referralFunnel['redemptions'], 'width' => ($referralFunnel['redemptions'] / $maxFunnel) * 100],
                ['label' => 'Rewards Earned', 'value' => $referralFunnel['rewards_earned'], 'width' => ($referralFunnel['rewards_earned'] / $maxFunnel) * 100],
            ] as $bar)
            <div class="flex items-center gap-3 text-xs">
                <span class="w-24 text-gray-500">{{ $bar['label'] }}</span>
                <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-admin-green rounded-full" style="width: {{ $bar['width'] }}%"></div>
                </div>
                <span class="w-6 text-right font-semibold text-gray-800">{{ $bar['value'] }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Quick actions --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
    @foreach([
        ['title' => 'Track Performance', 'desc' => 'View orders & revenue trends', 'icon' => 'fa-chart-line', 'url' => route('admin.dashboard')],
        ['title' => 'Manage Products', 'desc' => 'Update catalogue & stock', 'icon' => 'fa-box', 'url' => route('admin.products.index')],
        ['title' => 'Understand Customers', 'desc' => 'Messages & referrals', 'icon' => 'fa-users', 'url' => route('admin.contacts.index')],
        ['title' => 'Grow Your Business', 'desc' => 'Coupons, QR & marketing', 'icon' => 'fa-bullhorn', 'url' => route('admin.settings.edit', ['tab' => 'marketing'])],
    ] as $action)
    <a href="{{ $action['url'] }}" class="admin-quick-card group">
        <div class="admin-quick-card__icon group-hover:bg-admin-sidebar-active group-hover:text-white transition-colors">
            <i class="fas {{ $action['icon'] }}"></i>
        </div>
        <div class="min-w-0">
            <p class="font-semibold text-gray-900">{{ $action['title'] }}</p>
            <p class="text-xs text-gray-500 mt-0.5">{{ $action['desc'] }}</p>
        </div>
    </a>
    @endforeach
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const green = '#2E7D32';
    const orange = '#EF6C00';

    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: @json($chart['labels']),
                datasets: [
                    {
                        label: 'Revenue (₹)',
                        data: @json($chart['revenues']),
                        borderColor: green,
                        backgroundColor: 'rgba(46, 125, 50, 0.08)',
                        fill: true,
                        tension: 0.35,
                        yAxisID: 'y',
                    },
                    {
                        label: 'Orders',
                        data: @json($chart['orders']),
                        borderColor: orange,
                        backgroundColor: 'transparent',
                        tension: 0.35,
                        yAxisID: 'y1',
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { color: '#9CA3AF', font: { size: 11 } } },
                    y: { beginAtZero: true, position: 'left', ticks: { callback: v => '₹' + v, color: '#9CA3AF', font: { size: 11 } }, grid: { color: '#F3F4F6' } },
                    y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false }, ticks: { color: '#9CA3AF', font: { size: 11 } } },
                },
            },
        });
    }

    const statusCtx = document.getElementById('statusChart');
    if (statusCtx) {
        const counts = @json(array_values($orderStatusCounts));
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Delivered', 'Processing', 'Shipped', 'Pending', 'Cancelled'],
                datasets: [{
                    data: [
                        {{ $orderStatusCounts['delivered'] }},
                        {{ $orderStatusCounts['processing'] }},
                        {{ $orderStatusCounts['shipped'] }},
                        {{ $orderStatusCounts['pending'] }},
                        {{ $orderStatusCounts['cancelled'] }},
                    ],
                    backgroundColor: [green, orange, '#3B82F6', '#F59E0B', '#9CA3AF'],
                    borderWidth: 0,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: { legend: { display: false } },
            },
        });
    }
});
</script>
@endpush
