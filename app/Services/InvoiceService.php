<?php

namespace App\Services;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class InvoiceService
{
    public function __construct(private SettingService $settings) {}

    public function ensureInvoice(Order $order): Order
    {
        if ($order->invoice_number) {
            return $order;
        }

        $prefix = $this->settings->get('invoice_prefix', 'S7');
        $counter = (int) $this->settings->get('invoice_counter', 0) + 1;
        $this->settings->set('invoice_counter', (string) $counter);

        $invoiceNumber = sprintf('%s-%s-%06d', $prefix, now()->format('Y'), $counter);

        $order->update([
            'invoice_number' => $invoiceNumber,
            'invoiced_at' => now(),
        ]);

        return $order->fresh();
    }

    public function pdf(Order $order)
    {
        return Pdf::loadView('invoices.pdf', $this->viewData($order))->setPaper('a4');
    }

    public function storePdf(Order $order): string
    {
        $relativePath = 'invoices/'.$order->invoice_number.'.pdf';
        Storage::disk('local')->put($relativePath, $this->pdf($order)->output());

        return $relativePath;
    }

    public function viewData(Order $order): array
    {
        $order->loadMissing('items.product', 'user', 'shippingZone');
        $this->ensureInvoice($order);
        $order->refresh();

        return [
            'order' => $order,
            'brandName' => $this->settings->brandName(),
            'legalCompanyName' => $this->settings->invoiceLegalCompanyName(),
            'websiteName' => $this->settings->websiteName(),
            'storeEmail' => $this->settings->storeEmail(),
            'storePhone' => $this->settings->storePhone(),
            'invoiceGstin' => $this->settings->get('invoice_gstin'),
            'invoiceAddress' => $this->settings->get('invoice_address'),
            'invoiceLogoPath' => $this->invoiceLogoAbsolutePath(),
            'freeShippingThreshold' => $order->shipping_zone_id
                ? (float) optional($order->shippingZone)->free_shipping_threshold
                : $this->settings->freeShippingThreshold(),
            'shippingFee' => $order->shipping_fee,
            'shippingZoneName' => $order->shipping_zone_name,
        ];
    }

    protected function invoiceLogoAbsolutePath(): ?string
    {
        $path = $this->settings->invoiceLogoPath();

        if (! $path) {
            $siteLogo = $this->settings->get('logo_path');
            $path = $siteLogo ?: null;
        }

        if (! $path) {
            return null;
        }

        $absolute = public_path(ltrim($path, '/'));

        return is_file($absolute) ? $absolute : null;
    }

    public function printView(Order $order, string $downloadUrl, ?string $backUrl = null, bool $autoprint = false)
    {
        return view('invoices.print', array_merge($this->viewData($order), [
            'downloadUrl' => $downloadUrl,
            'backUrl' => $backUrl,
            'autoprint' => $autoprint,
        ]));
    }
}
