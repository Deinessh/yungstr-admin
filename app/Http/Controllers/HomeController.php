<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $categories = \App\Models\Category::all();
        $featuredProducts = Product::with('category')
            ->active()
            ->featured()
            ->take(6)
            ->get();
        $heroSlides = \App\Models\HeroSlide::active()->ordered()->get();
        $promoBanners = \App\Models\PromoBanner::active()->ordered()->get();
        $testimonials = \App\Models\Testimonial::active()->take(6)->get();

        return view('home', compact('categories', 'featuredProducts', 'heroSlides', 'promoBanners', 'testimonials'));
    }
}
