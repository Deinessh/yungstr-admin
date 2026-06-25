<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'total_amount', 'subtotal', 'shipping_fee', 'shipping_zone_id', 'shipping_zone_name', 'discount_amount',
        'status', 'payment_method', 'payment_status', 'razorpay_order_id',
        'razorpay_payment_id', 'cashfree_order_id', 'cashfree_payment_id',
        'coupon_id', 'coupon_code', 'referral_code_used',
        'shipping_address', 'shipping_name', 'shipping_city', 'shipping_state', 'shipping_pincode',
        'contact_number', 'delivery_date', 'customer_notes', 'cart_snapshot',
        'awb_code', 'velocity_order_id', 'velocity_shipment_id', 'carrier_name',
        'label_url', 'tracking_url', 'shipping_status', 'shipping_error',
        'invoice_number', 'invoiced_at', 'confirmation_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'cart_snapshot' => 'array',
            'delivery_date' => 'date',
            'invoiced_at' => 'datetime',
            'confirmation_sent_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function shippingZone(): BelongsTo
    {
        return $this->belongsTo(ShippingZone::class);
    }

    public function scopeVisibleToCustomer($query)
    {
        return $query->where(function ($q) {
            $q->where('payment_status', 'paid')
                ->orWhere(function ($inner) {
                    $inner->where('payment_method', 'cod')
                        ->whereNotIn('status', ['draft', 'cancelled']);
                });
        })->where('status', '!=', 'draft');
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid'
            || ($this->payment_method === 'cod' && $this->status !== 'cancelled' && $this->status !== 'draft');
    }

    public function canResume(): bool
    {
        return $this->status === 'draft' || $this->payment_status === 'pending';
    }

    public function canAccessInvoice(): bool
    {
        return ! in_array($this->status, ['draft', 'cancelled'], true);
    }
}
