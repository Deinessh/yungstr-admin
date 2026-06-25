<?php

namespace App\Mail;

use App\Models\Coupon;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReferralCouponActivatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user, public Coupon $coupon) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your referral coupon is now active — use within 7 days!',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.referral-coupon-activated',
        );
    }
}
