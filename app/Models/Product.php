<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'name', 'slug', 'description', 'benefit_tag', 'combo_includes',
        'price', 'mrp', 'stock', 'image', 'video', 'is_featured', 'is_best_seller', 'is_hot',
        'is_pick_any_combo', 'featured_sort', 'is_active', 'is_coming_soon',
        'weight', 'weight_kg', 'key_benefits', 'nutrition_info',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'is_best_seller' => 'boolean',
            'is_hot' => 'boolean',
            'is_pick_any_combo' => 'boolean',
            'is_active' => 'boolean',
            'is_coming_soon' => 'boolean',
            'key_benefits' => 'array',
            'nutrition_info' => 'array',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function comboProducts(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'product_combo_items',
            'combo_product_id',
            'included_product_id'
        )->withPivot('sort_order')->orderByPivot('sort_order');
    }

    public function isFixedComboPack(): bool
    {
        if ($this->is_pick_any_combo) {
            return false;
        }

        return $this->category?->isComboPackCategory() ?? false;
    }

    public function comboIncludesSummary(): ?string
    {
        if ($this->comboProducts->isNotEmpty()) {
            return $this->comboProducts->map(function (Product $product) {
                $label = $product->name;
                if ($product->weight) {
                    $label .= ' – '.$product->weight;
                }

                return $label;
            })->implode(' + ');
        }

        return $this->combo_includes;
    }

    public function galleryImages(): array
    {
        $images = $this->images->pluck('path')->all();

        if ($this->image && ! in_array($this->image, $images, true)) {
            array_unshift($images, $this->image);
        }

        return $images ?: ($this->image ? [$this->image] : []);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)->orderBy('featured_sort');
    }

    public function scopePickAnySelectable($query)
    {
        return $query->active()
            ->where('is_coming_soon', false)
            ->where('is_pick_any_combo', false)
            ->whereHas('category', fn ($category) => $category->where('slug', 'single-product-packs'));
    }

    public function hasStockLimit(): bool
    {
        return $this->stock !== null;
    }

    public function hasAvailableStock(int $quantity): bool
    {
        if (! $this->hasStockLimit()) {
            return $quantity >= 1;
        }

        return $quantity >= 1 && $this->stock >= $quantity;
    }

    public function isComingSoon(): bool
    {
        return (bool) $this->is_coming_soon;
    }

    public function isPurchasable(): bool
    {
        if (! $this->is_active || $this->is_coming_soon) {
            return false;
        }

        if (! $this->hasStockLimit()) {
            return true;
        }

        return $this->stock > 0;
    }

    public function hasDiscount(): bool
    {
        return $this->mrp !== null && (float) $this->mrp > (float) $this->price;
    }

    public function discountPercent(): ?int
    {
        if (! $this->hasDiscount()) {
            return null;
        }

        return (int) round((((float) $this->mrp - (float) $this->price) / (float) $this->mrp) * 100);
    }

    public function discountAmount(): ?float
    {
        if (! $this->hasDiscount()) {
            return null;
        }

        return round((float) $this->mrp - (float) $this->price, 2);
    }
}
