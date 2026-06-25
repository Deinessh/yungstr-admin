<?php

namespace App\Mail;

use App\Models\Order;
use App\Services\InvoiceService;
use App\Services\SettingService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderConfirmedMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $brandName;

    public string $websiteName;

    public float $freeShippingThreshold;

    public float $shippingFee;

    public function __construct(public Order $order)
    {
        $settings = app(SettingService::class);
        $this->brandName = $settings->brandName();
        $this->websiteName = $settings->websiteName();
        $this->freeShippingThreshold = $settings->freeShippingThreshold();
        $this->shippingFee = $settings->shippingFee();
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Order Confirmed #'.str_pad((string) $this->order->id, 6, '0', STR_PAD_LEFT).' — '.$this->brandName,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.order-confirmed',
        );
    }

    public function attachments(): array
    {
        if (! $this->order->invoice_number) {
            return [];
        }

        return [
            Attachment::fromData(
                fn () => app(InvoiceService::class)->pdf($this->order)->output(),
                $this->order->invoice_number.'.pdf'
            )->withMime('application/pdf'),
        ];
    }
}
