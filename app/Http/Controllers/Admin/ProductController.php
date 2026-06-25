<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function __construct(private ImageUploadService $images) {}

    public function index()
    {
        $products = Product::with('category')->latest()->paginate(20);

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.form', [
            'product' => new Product(),
            'categories' => Category::all(),
            'comboSelectableProducts' => $this->comboSelectableProducts(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        if ($request->hasFile('image')) {
            $data['image'] = $this->images->storeProductImage($request->file('image'));
        }

        if ($request->hasFile('video')) {
            $data['video'] = $this->images->storeProductVideo($request->file('video'));
        }

        $data['slug'] = Str::slug($data['name']);

        $product = Product::create($data);
        $this->syncGalleryImages($product, $request);
        $this->syncComboProducts($product, $request);

        return redirect()->route('admin.products.index')->with('success', 'Product created.');
    }

    public function edit(Product $product)
    {
        $product->load(['images', 'comboProducts']);

        return view('admin.products.form', [
            'product' => $product,
            'categories' => Category::all(),
            'comboSelectableProducts' => $this->comboSelectableProducts($product->id),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validated($request);
        $data['slug'] = Str::slug($data['name']);

        if ($request->hasFile('image')) {
            $data['image'] = $this->images->storeProductImage($request->file('image'), $product->image);
        } else {
            $data['image'] = $product->image;
        }

        if ($request->hasFile('video')) {
            $data['video'] = $this->images->storeProductVideo($request->file('video'), $product->video);
        } else {
            $data['video'] = $product->video;
        }

        $product->update($data);
        $this->syncGalleryImages($product, $request);
        $this->syncComboProducts($product, $request);

        return redirect()->route('admin.products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        $this->images->delete($product->image);
        $this->images->delete($product->video);

        foreach ($product->images as $image) {
            $this->images->delete($image->path);
        }

        $product->delete();

        return back()->with('success', 'Product deleted.');
    }

    public function destroyImage(Product $product, ProductImage $image)
    {
        if ($image->product_id !== $product->id) {
            abort(404);
        }

        $this->images->delete($image->path);
        $image->delete();

        return back()->with('success', 'Image removed.');
    }

    protected function syncComboProducts(Product $product, Request $request): void
    {
        if ($product->is_pick_any_combo) {
            $product->comboProducts()->sync([]);

            return;
        }

        if (! $request->has('combo_product_ids')) {
            return;
        }

        $allowedIds = $this->comboSelectableProducts($product->id)->pluck('id')->all();

        $ids = collect($request->input('combo_product_ids', []))
            ->filter(fn ($id) => filled($id) && (int) $id !== $product->id)
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => in_array($id, $allowedIds, true))
            ->unique()
            ->values();

        $sync = [];
        foreach ($ids as $index => $id) {
            $sync[$id] = ['sort_order' => $index + 1];
        }

        $product->comboProducts()->sync($sync);
    }

    protected function comboSelectableProducts(?int $excludeId = null)
    {
        return Product::query()
            ->pickAnySelectable()
            ->when($excludeId, fn ($query) => $query->where('id', '!=', $excludeId))
            ->orderBy('name')
            ->get(['id', 'name', 'weight']);
    }

    protected function syncGalleryImages(Product $product, Request $request): void
    {
        if (! $request->hasFile('gallery')) {
            return;
        }

        $sort = (int) $product->images()->max('sort_order');

        foreach ($request->file('gallery') as $file) {
            $sort++;
            ProductImage::create([
                'product_id' => $product->id,
                'path' => $this->images->storeProductImage($file),
                'sort_order' => $sort,
            ]);
        }
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'benefit_tag' => 'nullable|string|max:100',
            'combo_includes' => 'nullable|string|max:2000',
            'mrp' => 'nullable|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'gallery' => 'nullable|array',
            'gallery.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
            'video' => 'nullable|file|mimes:mp4,webm,mov|max:51200',
            'weight' => 'nullable|string|max:50',
            'weight_kg' => 'nullable|numeric|min:0.01|max:100',
            'is_featured' => 'nullable|boolean',
            'is_best_seller' => 'nullable|boolean',
            'is_hot' => 'nullable|boolean',
            'featured_sort' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'is_coming_soon' => 'nullable|boolean',
            'key_benefits' => 'nullable|string',
            'nutrition_info' => 'nullable|string',
            'combo_product_ids' => 'nullable|array',
            'combo_product_ids.*' => 'integer|exists:products,id',
        ]);

        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_best_seller'] = $request->boolean('is_best_seller');
        $data['is_hot'] = $request->boolean('is_hot');
        $category = ! empty($data['category_id'])
            ? Category::find($data['category_id'])
            : null;
        $data['is_pick_any_combo'] = $category?->isPickAnyCategory() ?? false;
        $data['is_active'] = $request->boolean('is_active', true);
        $data['is_coming_soon'] = $request->boolean('is_coming_soon');
        $data['featured_sort'] = $data['featured_sort'] ?? 0;
        $data['stock'] = $request->filled('stock') ? (int) $request->stock : null;

        if (! empty($data['key_benefits'])) {
            $data['key_benefits'] = array_values(array_filter(array_map('trim', explode("\n", $data['key_benefits']))));
        } else {
            $data['key_benefits'] = null;
        }

        if (! empty($data['nutrition_info'])) {
            $lines = array_filter(array_map('trim', explode("\n", $data['nutrition_info'])));
            $nutrition = [];
            foreach ($lines as $line) {
                if (str_contains($line, ':')) {
                    [$label, $value] = array_map('trim', explode(':', $line, 2));
                    $nutrition[] = ['label' => $label, 'value' => $value];
                }
            }
            $data['nutrition_info'] = $nutrition;
        } else {
            $data['nutrition_info'] = null;
        }

        unset($data['image'], $data['gallery'], $data['video']);

        return $data;
    }
}
