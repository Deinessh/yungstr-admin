<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\DashboardAnalyticsService;

class DashboardController extends Controller
{
    public function __construct(private DashboardAnalyticsService $analytics) {}

    public function index()
    {
        $days = (int) request('days', 30);
        $days = in_array($days, [7, 30, 90], true) ? $days : 30;

        return view('admin.dashboard', [
            'days' => $days,
            'summary' => $this->analytics->summary($days),
            'chart' => $this->analytics->chartData($days),
            'statusBreakdown' => $this->analytics->orderStatusBreakdown(),
            'orderStatusCounts' => $this->analytics->fulfillmentStatusCounts(),
            'topProducts' => $this->analytics->topProducts(),
            'referralFunnel' => $this->analytics->referralFunnel(),
            'productsCount' => Product::count(),
            'usersCount' => User::where('is_admin', false)->count(),
            'contactsCount' => ContactSubmission::where('is_read', false)->count(),
            'pendingOrdersCount' => Order::visibleToCustomer()->whereIn('status', ['pending', 'confirmed'])->where('payment_status', '!=', 'failed')->count(),
            'lowStockCount' => Product::whereNotNull('stock')->where('stock', '<=', 5)->count(),
            'lowStockProducts' => Product::whereNotNull('stock')->where('stock', '<=', 10)->orderBy('stock')->take(5)->get(),
            'recentOrders' => Order::with('user')->visibleToCustomer()->latest()->take(6)->get(),
            'recentContacts' => ContactSubmission::latest()->take(5)->get(),
        ]);
    }
}
