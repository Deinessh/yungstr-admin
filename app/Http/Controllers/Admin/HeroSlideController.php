<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;

class HeroSlideController extends Controller
{
    public function __construct(private ImageUploadService $uploads) {}

    public function index()
    {
        $slides = HeroSlide::ordered()->get();

        return view('admin.hero-slides.index', compact('slides'));
    }

    public function create()
    {
        return view('admin.hero-slides.form', ['slide' => new HeroSlide()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request, creating: true);

        $data['image'] = $this->uploads->storeHeroImage($request->file('image'));
        $data['sort_order'] = (HeroSlide::max('sort_order') ?? -1) + 1;
        $data['is_active'] = true;

        HeroSlide::create($data);

        return redirect()->route('admin.hero-slides.index')->with('success', 'Slide created.');
    }

    public function edit(HeroSlide $heroSlide)
    {
        return view('admin.hero-slides.form', ['slide' => $heroSlide]);
    }

    public function update(Request $request, HeroSlide $heroSlide)
    {
        $data = $this->validated($request);

        if ($request->hasFile('image')) {
            $data['image'] = $this->uploads->storeHeroImage($request->file('image'), $heroSlide->image);
        } else {
            $data['image'] = $heroSlide->image;
        }

        $heroSlide->update($data);

        return redirect()->route('admin.hero-slides.index')->with('success', 'Slide updated.');
    }

    public function destroy(HeroSlide $heroSlide)
    {
        $this->uploads->delete($heroSlide->image);
        $this->uploads->delete($heroSlide->video);
        $heroSlide->delete();

        return back()->with('success', 'Slide deleted.');
    }

    protected function validated(Request $request, bool $creating = false): array
    {
        $data = $request->validate([
            'button_url' => 'nullable|string|max:255',
            'image' => ($creating ? 'required' : 'nullable').'|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        return [
            'button_url' => $data['button_url'] ?? null,
        ];
    }
}
