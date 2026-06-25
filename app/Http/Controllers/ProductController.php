<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')->active();
        $activeCategory = null;

        if ($request->filled('category')) {
            $categoryParam = $request->category;
            $activeCategory = is_numeric($categoryParam)
                ? Category::find($categoryParam)
                : Category::where('slug', $categoryParam)->first();

            if ($activeCategory) {
                $query->where('category_id', $activeCategory->id);
            }
        }

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%")
                    ->orWhere('benefit_tag', 'like', "%{$term}%");
            });
        }

        if ($request->boolean('best_seller')) {
            $query->where('is_best_seller', true);
        }

        if ($request->boolean('hot')) {
            $query->where('is_hot', true);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->max_price);
        }

        $sort = $request->get('sort', 'featured');
        match ($sort) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'name' => $query->orderBy('name'),
            default => $query->orderByDesc('is_featured')->orderBy('featured_sort')->latest(),
        };

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::orderBy('name')->get();
        $maxPrice = (int) Product::active()->max('price');

        return view('products.index', compact('products', 'activeCategory', 'categories', 'maxPrice'));
    }

    public function show($slug)
    {
        $product = Product::with(['category', 'images', 'comboProducts.category'])->active()->where('slug', $slug)->firstOrFail();
        $relatedProducts = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        $selectableProducts = $product->is_pick_any_combo
            ? app(\App\Services\PickAnyComboService::class)->selectableProducts()
            : collect();

        return view('products.show', compact('product', 'relatedProducts', 'selectableProducts'));
    }
}
