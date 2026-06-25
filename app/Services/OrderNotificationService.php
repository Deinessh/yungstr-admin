<?php

namespace App\Services;

use App\Mail\OrderConfirmedMail;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderNotificationService
{
    public function __construct(
        private InvoiceService $invoices,
    ) {}

    public function sendOrderConfirmation(Order $order): void
    {
        $order = $order->fresh(['items.product', 'user']);

        if (! $order || $order->confirmation_sent_at) {
            return;
        }

        $this->invoices->ensureInvoice($order);
        $order->refresh();

        try {
            Mail::to($order->user->email)->send(new OrderConfirmedMail($order));

            $order->update(['confirmation_sent_at' => now()]);
        } catch (\Throwable $e) {
            Log::error('Order confirmation email failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
