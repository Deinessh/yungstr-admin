<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromoBanner;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;

class PromoBannerController extends Controller
{
    public function __construct(private ImageUploadService $uploads) {}

    public function index()
    {
        $banners = PromoBanner::ordered()->get();

        return view('admin.promo-banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.promo-banners.form', ['banner' => new PromoBanner()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $data['image'] = $this->uploads->storePromoBannerImage($request->file('image'));

        PromoBanner::create($data);

        return redirect()->route('admin.promo-banners.index')->with('success', 'Promo banner created.');
    }

    public function edit(PromoBanner $promoBanner)
    {
        return view('admin.promo-banners.form', ['banner' => $promoBanner]);
    }

    public function update(Request $request, PromoBanner $promoBanner)
    {
        $data = $this->validated($request, $promoBanner);

        if ($request->hasFile('image')) {
            $data['image'] = $this->uploads->storePromoBannerImage($request->file('image'), $promoBanner->image);
        } else {
            $data['image'] = $promoBanner->image;
        }

        $promoBanner->update($data);

        return redirect()->route('admin.promo-banners.index')->with('success', 'Promo banner updated.');
    }

    public function destroy(PromoBanner $promoBanner)
    {
        $this->uploads->delete($promoBanner->image);
        $promoBanner->delete();

        return back()->with('success', 'Promo banner deleted.');
    }

    protected function validated(Request $request, ?PromoBanner $banner = null): array
    {
        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'link_url' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'image' => ($banner && $banner->image ? 'nullable' : 'required').'|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        unset($data['image']);

        return $data;
    }
}
