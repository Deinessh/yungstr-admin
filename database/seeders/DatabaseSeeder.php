<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Coupon;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@yungstrclub.com'],
            [
                'name' => 'Yungstr Admin',
                'password' => bcrypt('Admin@12345'),
                'is_admin' => true,
            ]
        );

        if (User::where('email', 'test@example.com')->doesntExist()) {
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        $defaults = [
            'cod_enabled' => '1',
            'show_global_free_shipping_banner' => '1',
            'invoice_legal_company_name' => 'Yungstr Club Co.',
            'invoice_logo_path' => 'images/yungstr-logo.svg',
            'referral_reward_type' => 'fixed',
            'referral_reward_discount' => '100',
            'referral_reward_expiry_days' => '14',
            'referrals_required_to_unlock' => '3',
            'referral_code_counter' => '0',
            'delivery_date_enabled' => '0',
            'delivery_date_required' => '0',
            'delivery_lead_days' => '2',
            'razorpay_key_id' => '',
            'razorpay_key_secret' => '',
            'mail_mailer' => 'smtp',
            'mail_host' => '',
            'mail_port' => '587',
            'mail_username' => '',
            'mail_password' => '',
            'mail_encryption' => 'tls',
            'mail_from_address' => 'hello@yungstrclub.com',
            'mail_from_name' => 'Yungstr Club',
            'store_name' => 'Yungstr Club',
            'store_tagline' => 'Men\'s Streetwear. Built Different.',
            'store_email' => 'support@yungstrclub.com',
            'store_phone' => '+1 (555) 123-4567',
            'currency' => 'INR',
            'brand_name' => 'Yungstr Club',
            'website_name' => 'Yungstr Club Online Store',
            'invoice_prefix' => 'YC',
            'invoice_counter' => '0',
            'announcement_1' => 'Street Approved Styles',
            'announcement_2' => 'Premium Quality Heavyweight Blends',
            'announcement_3' => 'Free Shipping on Orders Above ₹100',
            'newsletter_heading' => 'Stay Updated with New Drops & Offers!',
            'about_heading' => 'About Yungstr Club',
            'contact_whatsapp' => '15551234567',
            'velocity_enabled' => '0',
            'velocity_auto_ship' => '1',
            'velocity_package_length' => '30',
            'velocity_package_breadth' => '20',
            'velocity_package_height' => '10',
            'velocity_package_weight' => '0.8',
            'seo_default_title' => 'Yungstr Club — Men\'s Streetwear | Built Different',
            'seo_default_description' => 'Shop premium hoodies, tees, cargoes, and caps from Yungstr Club. Built different.',
            'page_seo_overrides' => '[]',
            'theme_primary' => '#000000',
            'theme_accent' => '#b5ff00',
            'theme_background' => '#ffffff',
            'theme_text' => '#0a0a0a',
            'theme_soft' => '#f3f4f6',
        ];

        foreach ($defaults as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        $this->call(StoreContentSeeder::class);
        $this->call(ProductPlanSeeder::class);

        $coupons = [
            [
                'code' => 'WELCOME50',
                'name' => 'Welcome Discount',
                'type' => 'fixed',
                'value' => 50,
                'min_order_amount' => 199,
                'max_uses' => 100,
                'is_active' => true,
                'is_system' => true,
            ],
            [
                'code' => 'STREET10',
                'name' => 'Street Approved 10% Off',
                'type' => 'percent',
                'value' => 10,
                'min_order_amount' => 299,
                'max_uses' => 200,
                'is_active' => true,
                'is_system' => true,
            ],
            [
                'code' => 'FLAT100',
                'name' => 'Flat ₹100 Off',
                'type' => 'fixed',
                'value' => 100,
                'min_order_amount' => 499,
                'max_uses' => 50,
                'is_active' => true,
                'is_system' => true,
            ],
        ];

        foreach ($coupons as $couponData) {
            Coupon::firstOrCreate(
                ['code' => $couponData['code']],
                $couponData
            );
        }

        $testimonials = [
            ['name' => 'Priya Sharma', 'role' => 'Verified Buyer · Bangalore', 'quote' => "The Yungstr hoodie is amazing! Warm, premium weight, and the neon print is very sharp.", 'rating' => 5, 'sort_order' => 1],
            ['name' => 'Rohit Verma', 'role' => 'Verified Buyer · Pune', 'quote' => 'Ordered the cargo pants, and the fit is perfect. Multi-pockets look dope. Fast shipping!', 'rating' => 5, 'sort_order' => 2],
            ['name' => 'Anjali Mehta', 'role' => 'Verified Buyer · Mumbai', 'quote' => "Bought the Cap and white tee. High quality fabric, doesn't fade. Trusted brand!", 'rating' => 5, 'sort_order' => 3],
            ['name' => 'Lakshmi Reddy', 'role' => 'Verified Buyer · Hyderabad', 'quote' => "Street approved. Love the oversize fit of the sweatshirt. Got it in 3 days.", 'rating' => 5, 'sort_order' => 4],
            ['name' => 'Karthik Iyer', 'role' => 'Verified Buyer · Chennai', 'quote' => 'Hands down the best streetwear jacket I own. Got lots of compliments. 10/10.', 'rating' => 5, 'sort_order' => 5],
            ['name' => 'Deepa Nair', 'role' => 'Verified Buyer · Kochi', 'quote' => 'Fast delivery to Kochi. The styling and print detail is spot on.', 'rating' => 5, 'sort_order' => 6],
        ];

        foreach ($testimonials as $data) {
            \App\Models\Testimonial::updateOrCreate(
                ['name' => $data['name'], 'quote' => $data['quote']],
                array_merge($data, ['is_active' => true])
            );
        }
    }
}
