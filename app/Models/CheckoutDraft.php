<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CheckoutDraft extends Model
{
    protected $fillable = [
        'user_id', 'cart_data', 'shipping_address', 'shipping_name', 'shipping_city',
        'shipping_state', 'shipping_pincode', 'contact_number', 'delivery_date',
        'customer_notes', 'coupon_code', 'referral_code', 'payment_method',
    ];

    protected function casts(): array
    {
        return [
            'cart_data' => 'array',
            'delivery_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
