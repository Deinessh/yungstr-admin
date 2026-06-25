<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class StoreContentSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->websiteContent() as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }

    public static function websiteContent(): array
    {
        return [
            // General
            'store_name' => 'Yungstr Club',
            'store_tagline' => 'Men\'s Streetwear. Built Different.',
            'store_email' => 'support@yungstrclub.com',
            'store_phone' => '+1 (555) 123-4567',
            'currency' => 'INR',

            // Branding
            'brand_name' => 'Yungstr Club',
            'website_name' => 'Yungstr Club Online Store',
            'logo_alt' => 'Yungstr Club - Men\'s Streetwear',
            'invoice_prefix' => 'YC',
            'invoice_gstin' => '',
            'invoice_address' => "Yungstr Club HQ\n123 Streetwear Ave, Los Angeles, CA 90001",

            // Home — announcement & newsletter
            'announcement_1' => 'Street Approved Styles',
            'announcement_2' => 'Premium Quality Heavyweight Blends',
            'announcement_3' => 'Free Shipping on Orders Above ₹100',
            'newsletter_heading' => 'Join the Club - Get Notified on New Drops!',

            // Home — USP strip & how it works
            'home_hero_badge' => 'NEW DROP',
            'home_hero_title' => 'Built for the Yungstr',
            'home_hero_subtitle' => 'Premium streetwear for those who lead, not follow.',
            'home_hero_button_text' => 'Shop Collection',
            'home_usp_strip' => json_encode([
                ['icon' => 'fas fa-shield-halved', 'label' => 'Premium Quality'],
                ['icon' => 'fas fa-fire', 'label' => 'Street Approved'],
                ['icon' => 'fas fa-truck', 'label' => 'Fast Shipping'],
                ['icon' => 'fas fa-arrow-rotate-left', 'label' => 'Easy Returns'],
            ]),
            'home_trust_bar' => json_encode([
                ['icon' => 'fas fa-gem', 'label' => 'Premium Heavyweight Fabrics'],
                ['icon' => 'fas fa-vest-patches', 'label' => 'Custom Oversized Fits'],
                ['icon' => 'fas fa-paint-brush', 'label' => 'Embroidered Logos'],
                ['icon' => 'fas fa-thumbs-up', 'label' => 'Designed for Real Ones'],
                ['icon' => 'fas fa-users', 'label' => 'Trusted by 10,000+ Creators'],
            ]),
            'home_how_it_works' => json_encode([
                ['icon' => 'fas fa-shirt', 'title' => 'Select Gear', 'desc' => 'Choose your drop gear.'],
                ['icon' => 'fas fa-credit-card', 'title' => 'Quick Checkout', 'desc' => 'Fast and secure checkout.'],
                ['icon' => 'fas fa-street-view', 'title' => 'Rep the Club', 'desc' => 'Show your style on the streets.'],
            ]),

            // Home — sections
            'home_category_subtitle' => 'Limited streetwear drops designed for comfort and style.',
            'home_why_millet_title' => 'Why Yungstr Club?',
            'home_value_props' => json_encode([
                ['icon' => 'fas fa-gem', 'title' => 'Premium Fabric', 'desc' => 'Wholesome comfort and durability', 'color' => 'text-brand-green-logo'],
                ['icon' => 'fas fa-fire', 'title' => 'Street Approved', 'desc' => 'Authentic designs inspired by youth culture', 'color' => 'text-brand-orange-logo'],
                ['icon' => 'fas fa-leaf', 'title' => 'Ethical Sourcing', 'desc' => 'Supporting local ethical suppliers', 'color' => 'text-brand-green-logo'],
                ['icon' => 'fas fa-crosshairs', 'title' => 'Built Different', 'desc' => 'Original drops, no fast-fashion copycatting', 'color' => 'text-brand-green-logo'],
            ]),
            'home_why_millet_benefits' => json_encode([
                ['icon' => 'fas fa-tshirt', 'title' => 'Heavyweight Comfort', 'desc' => 'Naturally breathable, sturdy seams, and structured styling.'],
                ['icon' => 'fas fa-bolt', 'title' => 'Bold Graphics', 'desc' => 'Vibrant screen prints and embroidery that holds through washes.'],
                ['icon' => 'fas fa-crown', 'title' => 'Exclusive Drops', 'desc' => 'Limited quantities per run ensuring your look stays unique.'],
                ['icon' => 'fas fa-hand-holding-heart', 'title' => 'Made for Creators', 'desc' => 'Supporting street culture, local artists, and youth styling hubs.'],
            ]),

            // Founder story (homepage + about)
            'founder_badge_title' => "Lead\nNot Follow",
            'founder_ribbon' => 'Driven by Culture, Inspired by the Streets',
            'founder_heading_script' => 'From Creative Vision',
            'founder_heading_bold' => 'TO THE STREET SYSTEM',
            'founder_body' => 'Yungstr Club was born from a simple belief — streetwear should be authentic, premium, and built for self-expression. I wanted better, heavier, and more structured styling choices that do not compromise on fabric weight or fit. That’s why we craft our hoodies, tees, and cargos to stand out.',
            'founder_feature_1' => 'Heavyweight Custom Fabric',
            'founder_feature_2' => 'Original Hand-drawn Graphics',
            'founder_feature_3' => 'Built Different Street Styling',
            'founder_cta_text' => 'Our Story',
            'founder_cta_url' => '/about#founder-story',
            'founder_signature_label' => '— Founder',
            'founder_signature_brand' => 'Yungstr Club',
            'founder_illustration_path' => 'images/founder-placeholder.svg',
            'founder_photo_path' => '',
            'founder_quote_note' => '“I started Yungstr Club with one dream: to help youth showcase their voice through uncompromising premium streetwear.”',

            // About page
            'about_hero_title' => 'About Yungstr Club',
            'about_hero_subtitle' => 'We are passionate about bringing premium, heavy-blend streetwear back to the frontlines of youth fashion. Our mission is to provide you with limited-run collections that elevate your style and represent authentic street culture.',
            'about_journey_title' => 'Our Story',
            'about_journey_p1' => 'Yungstr Club started with a simple idea: clothing is self-expression. Observing the decline of fabric quality in fast fashion, we set out to construct heavyweight products that hold their structure and last years.',
            'about_journey_p2' => 'We partner directly with high-quality mills to source our custom cotton blends. By crafting our pieces in limited-run drops, we prevent inventory waste, maintain style exclusivity, and guarantee premium stitching.',
            'about_journey_bullets' => json_encode(['Custom Heavyweight Blends', 'Limited Run Exclusivity', 'Ethical Manufacturing Sourcing']),
            'about_core_values' => json_encode([
                ['emoji' => '🧵', 'title' => 'Quality First', 'desc' => 'We select only heavyweight fabrics and custom blends that hold shape and durability.'],
                ['emoji' => '🔥', 'title' => 'Street Culture', 'desc' => 'Inspired directly by underground music, skateboarding, and local youth style hubs.'],
                ['emoji' => '⚔️', 'title' => 'Built Different', 'desc' => 'We don\'t follow generic trend cycles. We design drop-by-drop and release what we feel is next.'],
            ]),

            // Contact
            'contact_heading' => 'Get In Touch',
            'contact_subtitle' => "Have questions about sizing, drops, or your order? Reach out to our support crew below.",
            'contact_address' => '123 Streetwear Ave, Los Angeles, CA 90001',
            'contact_hours' => 'Mon-Fri: 9AM - 6PM',
            'contact_whatsapp' => '15551234567',
            'social_instagram' => 'https://instagram.com/yungstrclub',
            'social_facebook' => 'https://facebook.com/yungstrclub',
            'social_youtube' => 'https://youtube.com/yungstrclub',

            // Footer
            'footer_tagline' => 'Premium streetwear for those who lead, not follow.',
            'footer_copyright' => 'Yungstr Club | All rights reserved',
            'footer_fssai' => 'Drop 01 Active',
            'footer_vegetarian' => '100% Original',

            // SEO
            'seo_default_title' => 'Yungstr Club — Men\'s Streetwear | Built Different',
            'seo_default_description' => 'Shop premium hoodies, tees, cargoes, and caps from Yungstr Club. Built different.',
            'seo_default_keywords' => 'streetwear, yungstr, hoodies, cargo pants, t-shirts, fashion',
            'page_seo_overrides' => json_encode([
                ['page' => 'home', 'title' => 'Yungstr Club — Men\'s Streetwear | Built Different', 'description' => 'Premium hoodies, tees, and cargos. Designed for real ones.', 'canonical' => '', 'sitemap' => '1'],
                ['page' => 'catalogue', 'title' => 'Shop Streetwear | Yungstr Club', 'description' => 'Browse custom hoodies, graphic tees, cargos, and caps.', 'canonical' => '', 'sitemap' => '1'],
                ['page' => 'about', 'title' => 'About Us | Yungstr Club', 'description' => 'Our journey bringing authentic street culture to fashion.', 'canonical' => '', 'sitemap' => '1'],
                ['page' => 'contact', 'title' => 'Contact Crew | Yungstr Club', 'description' => 'Reach our streetwear team for orders, sizing, and press.', 'canonical' => '', 'sitemap' => '1'],
            ]),

            // Shipping defaults
            'free_shipping_threshold' => '100',
            'shipping_fee' => '10',
            'velocity_enabled' => '0',
            'velocity_auto_ship' => '1',
            'velocity_package_length' => '30',
            'velocity_package_breadth' => '20',
            'velocity_package_height' => '10',
            'velocity_package_weight' => '0.8',
        ];
    }
}
