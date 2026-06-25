<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 'product_id', 'quantity', 'price',
        'product_name', 'product_weight', 'combo_includes', 'mrp', 'pick_any_selections',
    ];

    protected function casts(): array
    {
        return [
            'mrp' => 'decimal:2',
            'price' => 'decimal:2',
            'pick_any_selections' => 'array',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function displayName(): string
    {
        return $this->product_name ?: $this->product?->name ?: 'Product';
    }

    public function displayWeight(): ?string
    {
        return $this->product_weight ?: $this->product?->weight;
    }

    public function displayComboIncludes(): ?string
    {
        return $this->combo_includes ?: $this->product?->combo_includes;
    }

    public function displayMrp(): ?float
    {
        if ($this->mrp !== null) {
            return (float) $this->mrp;
        }

        return $this->product?->mrp !== null ? (float) $this->product->mrp : null;
    }

    public function pickAnySets(): array
    {
        return is_array($this->pick_any_selections) ? $this->pick_any_selections : [];
    }

    public function displayPickAnySelections(): ?string
    {
        return app(\App\Services\PickAnyComboService::class)->formatSetsForDisplay($this->pickAnySets());
    }
}
