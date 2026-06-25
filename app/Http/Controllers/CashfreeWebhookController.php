<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\CashfreeService;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CashfreeWebhookController extends Controller
{
    public function __invoke(Request $request, CashfreeService $cashfree, OrderService $orders): Response
    {
        $payload = $request->all();
        $cfOrderId = $payload['data']['order']['order_id']
            ?? $payload['order_id']
            ?? null;

        if (! $cfOrderId) {
            return response('ignored', 200);
        }

        $order = Order::where('cashfree_order_id', $cfOrderId)->first();

        if (! $order || $order->payment_status === 'paid') {
            return response('ok', 200);
        }

        $remote = $cashfree->fetchOrder($cfOrderId);

        if ($remote && $cashfree->isPaid($remote)) {
            $order->update([
                'cashfree_payment_id' => $remote['cf_payment_id'] ?? $remote['payment_session_id'] ?? null,
                'payment_status' => 'paid',
            ]);
            $orders->finalizeOrder($order->fresh(), $order->coupon);
        }

        return response('ok', 200);
    }
}
