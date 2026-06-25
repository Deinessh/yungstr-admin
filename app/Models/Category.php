<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'image'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function isComboPackCategory(): bool
    {
        return str_contains($this->slug ?? '', 'combo-packs');
    }

    public function isPickAnyCategory(): bool
    {
        return ($this->slug ?? '') === 'pick-any-3-combo';
    }
}
