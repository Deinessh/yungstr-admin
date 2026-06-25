<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShippingZone extends Model
{
    protected $fillable = [
        'name', 'match_type', 'match_values', 'shipping_fee',
        'free_shipping_threshold', 'is_active', 'is_default',
    ];

    protected function casts(): array
    {
        return [
            'shipping_fee' => 'float',
            'free_shipping_threshold' => 'float',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function matchValuesList(): array
    {
        if (! $this->match_values) {
            return [];
        }

        return array_values(array_filter(array_map(
            fn ($value) => trim($value),
            preg_split('/[\n,]+/', $this->match_values) ?: []
        )));
    }

    public static function matchTypes(): array
    {
        return [
            'city' => 'City',
            'state' => 'State',
            'pincode' => 'PIN code (exact 6-digit or prefix e.g. 500)',
            'default' => 'Fallback (all other locations)',
        ];
    }
}
