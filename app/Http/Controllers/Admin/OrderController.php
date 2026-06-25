<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\InvoiceService;
use App\Services\OrderShippingService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        private InvoiceService $invoices,
        private OrderShippingService $shipping,
    ) {}

    public function index(Request $request)
    {
        $query = Order::with('user')->visibleToCustomer()->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('items.product', 'user', 'coupon');

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate(['status' => 'required|in:pending,confirmed,shipped,delivered,cancelled']);

        $order->update(['status' => $request->status]);

        if ($request->status === 'confirmed' && ! $order->awb_code) {
            $this->shipping->processConfirmedOrder($order->fresh());
        }

        return back()->with('success', 'Order status updated.');
    }

    public function invoice(Order $order)
    {
        abort_unless($order->canAccessInvoice(), 404);

        $order = $this->invoices->ensureInvoice($order->fresh(['items.product', 'user']));

        return $this->invoices->pdf($order)->download($order->invoice_number.'.pdf');
    }

    public function printInvoice(Order $order)
    {
        abort_unless($order->canAccessInvoice(), 404);

        return $this->invoices->printView(
            $order,
            route('admin.orders.invoice', $order),
            route('admin.orders.show', $order),
            request()->boolean('autoprint')
        );
    }

    public function retryShipment(Order $order)
    {
        $this->shipping->retryShipment($order);

        return back()->with('success', 'Shipment creation retried. Check shipping details below.');
    }

    public function syncTracking(Order $order)
    {
        $this->shipping->syncTracking($order);

        return back()->with('success', 'Tracking synced from Velocity.');
    }
}
