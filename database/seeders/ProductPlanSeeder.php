<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\HeroSlide;
use App\Models\Product;
use App\Models\PromoBanner;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductPlanSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Hoodies & Sweatshirts',
                'slug' => 'hoodies',
                'description' => 'Premium heavyweight hoodies and crewneck sweatshirts.',
                'image' => 'images/category-trial.png',
            ],
            [
                'name' => 'T-Shirts & Tanks',
                'slug' => 't-shirts',
                'description' => 'Premium cotton graphic tees, tank tops, and longsleeve tees.',
                'image' => 'images/category-trial.png',
            ],
            [
                'name' => 'Streetwear Pants & Shorts',
                'slug' => 'pants',
                'description' => 'Loose cargo pants, tech trousers, and mesh shorts.',
                'image' => 'images/category-trial.png',
            ],
            [
                'name' => 'Accessories & Outerwear',
                'slug' => 'accessories',
                'description' => 'Embroidered caps, windbreakers, and drop gear.',
                'image' => 'images/category-trial.png',
            ],
        ];

        $categoryIds = [];
        foreach ($categories as $data) {
            $category = Category::updateOrCreate(['slug' => $data['slug']], $data);
            $categoryIds[$data['slug']] = $category->id;
        }

        $products = [
            [
                'category' => 'hoodies',
                'name' => 'Yungstr Club Hoodie',
                'benefit_tag' => 'Best Seller',
                'description' => 'Premium heavyweight black hoodie with bold Yungstr Club neon green branding. Built for comfort. Made to stand out.',
                'combo_includes' => null,
                'mrp' => 799,
                'price' => 699,
                'weight' => '600g',
                'weight_kg' => 0.6,
                'image' => 'images/products/yungstr-club-hoodie.png',
                'is_featured' => true,
                'featured_sort' => 1,
                'is_best_seller' => true,
                'is_hot' => true,
                'key_benefits' => ['Premium heavyweight cotton', 'Bold neon green screenprint', 'Soft fleece lining'],
            ],
            [
                'category' => 't-shirts',
                'name' => 'Yungstr Club Tee',
                'benefit_tag' => 'Essential',
                'description' => 'Premium white streetwear t-shirt with neon green Yungstr Club text logo printed on the chest. Designed with comfort.',
                'combo_includes' => null,
                'mrp' => 399,
                'price' => 349,
                'weight' => '200g',
                'weight_kg' => 0.2,
                'image' => 'images/products/yungstr-club-tee.png',
                'is_featured' => true,
                'featured_sort' => 2,
                'is_best_seller' => true,
                'key_benefits' => ['100% fine cotton jersey', 'Vibrant front chest print', 'Oversized comfort fit'],
            ],
            [
                'category' => 'pants',
                'name' => 'Yungstr Club Cargo',
                'benefit_tag' => 'Street Approved',
                'description' => 'Black loose cargo pants for men, multi-pocket streetwear style. Built to last.',
                'combo_includes' => null,
                'mrp' => 899,
                'price' => 749,
                'weight' => '500g',
                'weight_kg' => 0.5,
                'image' => 'images/products/yungstr-club-cargo.png',
                'is_featured' => true,
                'featured_sort' => 3,
                'is_best_seller' => true,
                'key_benefits' => ['Multi-pocket utility pockets', 'Sturdy tactical hardware', 'Loose breathable fit'],
            ],
            [
                'category' => 'hoodies',
                'name' => 'Yungstr Club Sweatshirt',
                'benefit_tag' => 'Classic Style',
                'description' => 'Heavy fleece crewneck sweatshirt with classic logo styling. Cozy and thick.',
                'combo_includes' => null,
                'mrp' => 699,
                'price' => 599,
                'weight' => '500g',
                'weight_kg' => 0.5,
                'image' => 'images/products/yungstr-club-hoodie.png',
                'is_featured' => true,
                'featured_sort' => 4,
                'key_benefits' => ['Fleece-lined interior', 'Ribbed cuffs and waistband', 'High density branding'],
            ],
            [
                'category' => 't-shirts',
                'name' => 'Yungstr Club Tank',
                'benefit_tag' => 'Summer Gear',
                'description' => 'Minimalist cotton rib tank top. Perfect for street layering and hot drops.',
                'combo_includes' => null,
                'mrp' => 299,
                'price' => 259,
                'weight' => '150g',
                'weight_kg' => 0.15,
                'image' => 'images/products/yungstr-club-tee.png',
                'is_featured' => false,
                'featured_sort' => 0,
                'key_benefits' => ['Ribbed construction', 'Breathable organic cotton', 'Discreet tags'],
            ],
            [
                'category' => 'pants',
                'name' => 'Yungstr Club Shorts',
                'benefit_tag' => 'Sporty Vibe',
                'description' => 'Comfortable heavy mesh shorts with cargo utility pockets. Elastic waist for daily rep.',
                'combo_includes' => null,
                'mrp' => 499,
                'price' => 449,
                'weight' => '300g',
                'weight_kg' => 0.3,
                'image' => 'images/products/yungstr-club-cargo.png',
                'is_featured' => false,
                'featured_sort' => 0,
                'key_benefits' => ['Dual side slash pockets', 'Embroidered brand label', 'Breathable athletic mesh'],
            ],
            [
                'category' => 'accessories',
                'name' => 'Yungstr Club Jacket',
                'benefit_tag' => 'Outerwear Drop',
                'description' => 'Technical streetwear bomber jacket with windproof lining. Built for cold streets.',
                'combo_includes' => null,
                'mrp' => 999,
                'price' => 899,
                'weight' => '700g',
                'weight_kg' => 0.7,
                'image' => 'images/products/yungstr-club-hoodie.png',
                'is_featured' => true,
                'featured_sort' => 5,
                'key_benefits' => ['Water-resistant techwear shell', 'Multi-pocket zip detailing', 'Quilted inner warming layer'],
            ],
            [
                'category' => 't-shirts',
                'name' => 'Yungstr Club Longsleeve',
                'benefit_tag' => 'Clean Fit',
                'description' => 'Classic mockneck long sleeve shirt in heavyweight jersey. Vintage look.',
                'combo_includes' => null,
                'mrp' => 549,
                'price' => 499,
                'weight' => '300g',
                'weight_kg' => 0.3,
                'image' => 'images/products/yungstr-club-tee.png',
                'is_featured' => false,
                'featured_sort' => 0,
                'key_benefits' => ['Heavyweight cotton jersey', 'Mockneck collar border', 'Printed sleeves artwork'],
            ],
            [
                'category' => 'accessories',
                'name' => 'Yungstr Club Cap',
                'benefit_tag' => 'Essential Cap',
                'description' => 'Six-panel cotton cap with premium embroidery and strapback adjuster. Dope style.',
                'combo_includes' => null,
                'mrp' => 299,
                'price' => 249,
                'weight' => '100g',
                'weight_kg' => 0.1,
                'image' => 'images/products/yungstr-club-cap.png',
                'is_featured' => true,
                'featured_sort' => 6,
                'is_best_seller' => true,
                'key_benefits' => ['100% brushed cotton twill', 'Premium embroidered logo', 'Adjustable strapback buckle'],
            ],
        ];

        $validSlugs = [];
        foreach ($products as $data) {
            $slug = Str::slug($data['name']);
            $validSlugs[] = $slug;

            Product::updateOrCreate(
                ['slug' => $slug],
                [
                    'category_id' => $categoryIds[$data['category']],
                    'name' => $data['name'],
                    'benefit_tag' => $data['benefit_tag'],
                    'description' => $data['description'],
                    'combo_includes' => $data['combo_includes'],
                    'mrp' => $data['mrp'],
                    'price' => $data['price'],
                    'stock' => 100,
                    'weight' => $data['weight'],
                    'weight_kg' => $data['weight_kg'],
                    'image' => $data['image'],
                    'is_featured' => $data['is_featured'],
                    'is_best_seller' => $data['is_best_seller'] ?? false,
                    'is_hot' => $data['is_hot'] ?? false,
                    'is_pick_any_combo' => false,
                    'featured_sort' => $data['featured_sort'],
                    'key_benefits' => $data['key_benefits'] ?? ['Premium custom cotton fabric', 'Strapback details', 'Built different streetwear styling'],
                    'is_active' => true,
                ]
            );
        }

        Product::whereNotIn('slug', $validSlugs)->delete();
        Category::whereNotIn('slug', collect($categories)->pluck('slug'))->delete();

        $this->seedHeroSlides();
        $this->seedPromoBanners();
    }

    protected function seedHeroSlides(): void
    {
        HeroSlide::query()->delete();

        $slides = [
            [
                'button_url' => '/products',
                'image' => 'images/hero/banner-1.png',
                'sort_order' => 0,
            ],
            [
                'button_url' => '/products?category=hoodies',
                'image' => 'images/hero/banner-2.png',
                'sort_order' => 1,
            ],
            [
                'button_url' => '/products?category=accessories',
                'image' => 'images/hero/banner-3.png',
                'sort_order' => 2,
            ],
        ];

        foreach ($slides as $slide) {
            HeroSlide::create(array_merge($slide, ['is_active' => true]));
        }
    }

    protected function seedPromoBanners(): void
    {
        PromoBanner::query()->delete();

        $banners = [
            [
                'title' => 'Drop 01 Active — Elevate Your Style',
                'link_url' => '/products',
                'image' => 'images/hero/banner-1.png',
                'sort_order' => 0,
            ],
            [
                'title' => 'Premium Heavyweight Hoodies',
                'link_url' => '/products?category=hoodies',
                'image' => 'images/hero/banner-2.png',
                'sort_order' => 1,
            ],
            [
                'title' => 'Tokyo Shibuya Night-Style Banners',
                'link_url' => '/products?category=accessories',
                'image' => 'images/hero/banner-3.png',
                'sort_order' => 2,
            ],
        ];

        foreach ($banners as $banner) {
            PromoBanner::create(array_merge($banner, ['is_active' => true]));
        }
    }
}
