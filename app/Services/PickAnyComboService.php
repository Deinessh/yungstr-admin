<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

class PickAnyComboService
{
    public const CHOICES_PER_SET = 3;

    public function selectableProducts(): Collection
    {
        return Product::query()
            ->active()
            ->where('is_coming_soon', false)
            ->where('is_pick_any_combo', false)
            ->whereHas('category', fn ($query) => $query->where('slug', 'single-product-packs'))
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function validSelectableIds(): array
    {
        return $this->selectableProducts()->pluck('id')->map(fn ($id) => (int) $id)->all();
    }

    public function validateSet(array $productIds): ?string
    {
        $ids = array_values(array_filter(array_map('intval', $productIds)));

        if (count($ids) !== self::CHOICES_PER_SET) {
            return 'Please select exactly 3 products for each Pick Any 3 combo.';
        }

        if (count($ids) !== count(array_unique($ids))) {
            return 'Please choose 3 different products in each Pick Any 3 combo.';
        }

        $validIds = $this->validSelectableIds();

        foreach ($ids as $id) {
            if (! in_array($id, $validIds, true)) {
                return 'One or more selected products are not available for Pick Any 3 combos.';
            }
        }

        return null;
    }

    public function validateSets(array $sets, int $quantity): ?string
    {
        if (count($sets) !== $quantity) {
            return "Please complete all product selections for your Pick Any 3 combo(s).";
        }

        foreach ($sets as $set) {
            if ($error = $this->validateSet(is_array($set) ? $set : [])) {
                return $error;
            }
        }

        return null;
    }

    public function buildSet(array $productIds): array
    {
        $ids = array_values(array_map('intval', $productIds));
        $products = Product::whereIn('id', $ids)->get()->keyBy('id');

        return array_map(fn (int $id) => [
            'product_id' => $id,
            'name' => $products[$id]->name,
        ], $ids);
    }

    public function buildSets(array $rawSets): array
    {
        return array_map(fn (array $set) => $this->buildSet($set), $rawSets);
    }

    public function extractRawSetsFromRequest(array $input, int $productId, int $quantity): array
    {
        $raw = $input[(string) $productId] ?? $input[$productId] ?? [];
        $sets = [];

        for ($index = 0; $index < $quantity; $index++) {
            $set = $raw[$index] ?? [];
            $sets[] = array_values(is_array($set) ? $set : []);
        }

        return $sets;
    }

    public function formatSetsForDisplay(?array $sets): ?string
    {
        if (empty($sets)) {
            return null;
        }

        $lines = [];

        foreach ($sets as $index => $set) {
            $names = collect($set)->pluck('name')->filter()->values()->all();

            if (count($sets) > 1) {
                $lines[] = 'Combo '.($index + 1).': '.implode(', ', $names);
            } else {
                $lines[] = implode(', ', $names);
            }
        }

        return implode("\n", $lines);
    }

    public function formatSetNames(array $set): string
    {
        return collect($set)->pluck('name')->filter()->implode(', ');
    }
}
