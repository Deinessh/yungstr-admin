<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingService
{
    public function get(string $key, mixed $default = null): mixed
    {
        try {
            return Cache::remember("setting.{$key}", 3600, function () use ($key, $default) {
                return $this->fetchFromDatabase($key, $default);
            });
        } catch (\Throwable) {
            return $this->fetchFromDatabase($key, $default);
        }
    }

    private function fetchFromDatabase(string $key, mixed $default = null): mixed
    {
        $setting = Setting::where('key', $key)->first();

        return $setting?->value ?? $default;
    }

    public function set(string $key, mixed $value): void
    {
        Setting::updateOrCreate(['key' => $key], ['value' => is_scalar($value) || $value === null ? (string) $value : json_encode($value)]);

        try {
            Cache::forget("setting.{$key}");
        } catch (\Throwable) {
            // Cache may be unavailable if storage permissions are misconfigured.
        }
    }

    public function setMany(array $values): void
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value);
        }
    }

    public function codEnabled(): bool
    {
        return filter_var($this->get('cod_enabled', '1'), FILTER_VALIDATE_BOOLEAN);
    }

    public function razorpayEnabled(): bool
    {
        return filter_var($this->get('razorpay_enabled', '1'), FILTER_VALIDATE_BOOLEAN)
            && $this->razorpayKeyId()
            && $this->razorpayKeySecret();
    }

    public function cashfreeEnabled(): bool
    {
        return filter_var($this->get('cashfree_enabled', '0'), FILTER_VALIDATE_BOOLEAN);
    }

    public function cashfreeAppId(): ?string
    {
        $value = $this->get('cashfree_app_id');

        return $value ?: null;
    }

    public function cashfreeSecretKey(): ?string
    {
        $value = $this->get('cashfree_secret_key');

        return $value ?: null;
    }

    public function cashfreeEnvironment(): string
    {
        $env = $this->get('cashfree_environment', 'sandbox');

        return $env === 'production' ? 'production' : 'sandbox';
    }

    public function qrRedirectUrl(): string
    {
        return (string) $this->get('qr_redirect_url', config('app.url'));
    }

    public function qrScanBaseUrl(): string
    {
        return rtrim((string) $this->get('qr_scan_base_url', config('app.url')), '/');
    }

    /**
     * @return array<string, string>
     */
    public function themeColors(): array
    {
        return [
            'primary' => (string) $this->get('theme_primary', '#004D26'),
            'accent' => (string) $this->get('theme_accent', '#F26A2E'),
            'background' => (string) $this->get('theme_background', '#FFFFFF'),
            'text' => (string) $this->get('theme_text', '#1A3324'),
            'soft' => (string) $this->get('theme_soft', '#E8F5EE'),
        ];
    }

    public function freeShippingThreshold(): float
    {
        return (float) $this->get('free_shipping_threshold', 399);
    }

    public function shippingFee(): float
    {
        return (float) $this->get('shipping_fee', 29);
    }

    public function razorpayKeyId(): ?string
    {
        $value = $this->get('razorpay_key_id');

        return $value ?: null;
    }

    public function razorpayKeySecret(): ?string
    {
        $value = $this->get('razorpay_key_secret');

        return $value ?: null;
    }

    public function referralRewardDiscount(): float
    {
        return (float) $this->get('referral_reward_discount', 100);
    }

    public function referralRewardType(): string
    {
        $type = $this->get('referral_reward_type', 'fixed');

        return in_array($type, ['fixed', 'percent'], true) ? $type : 'fixed';
    }

    public function referralRewardExpiryDays(): int
    {
        return max(1, (int) $this->get('referral_reward_expiry_days', 7));
    }

    public function referralsRequiredToUnlock(): int
    {
        return (int) $this->get('referrals_required_to_unlock', 3);
    }

    public function deliveryDateEnabled(): bool
    {
        return filter_var($this->get('delivery_date_enabled', '1'), FILTER_VALIDATE_BOOLEAN);
    }

    public function deliveryDateRequired(): bool
    {
        return filter_var($this->get('delivery_date_required', '1'), FILTER_VALIDATE_BOOLEAN);
    }

    public function deliveryLeadDays(): int
    {
        return max(0, (int) $this->get('delivery_lead_days', 1));
    }

    public function minDeliveryDate(): string
    {
        return now()->addDays($this->deliveryLeadDays())->format('Y-m-d');
    }

    public function nextReferralCodeCounter(): int
    {
        $current = (int) $this->get('referral_code_counter', 0);
        $next = $current + 1;
        $this->set('referral_code_counter', (string) $next);

        return $next;
    }

    public function brandName(): string
    {
        return (string) $this->get('brand_name', 'S7 MilletCo');
    }

    public function websiteName(): string
    {
        return (string) $this->get('website_name', $this->brandName());
    }

    public function storeName(): string
    {
        return (string) $this->get('store_name', $this->brandName());
    }

    public function storeEmail(): string
    {
        return (string) $this->get('store_email', 'hello@s7milletco.com');
    }

    public function storePhone(): string
    {
        return (string) $this->get('store_phone', '+91 89786 05003');
    }

    public function pageSeoOverrides(): array
    {
        $raw = $this->get('page_seo_overrides', '[]');

        return json_decode($raw, true) ?: [];
    }

    public function seoForPage(string $page): array
    {
        foreach ($this->pageSeoOverrides() as $override) {
            if (($override['page'] ?? '') === $page) {
                return $override;
            }
        }

        return [];
    }

    public function allPublic(): array
    {
        return [
            'store_name' => $this->storeName(),
            'brand_name' => $this->brandName(),
            'website_name' => $this->websiteName(),
            'store_tagline' => (string) $this->get('store_tagline', 'Smart Nutrition Everyday'),
            'store_email' => $this->storeEmail(),
            'store_phone' => $this->storePhone(),
            'logo_alt' => (string) $this->get('logo_alt', 'S7 MilletCo - Smart Nutrition Everyday'),
            'logo_path' => (string) $this->get('logo_path', ''),
            'favicon_path' => (string) $this->get('favicon_path', ''),
            'contact_address' => $this->get('contact_address', 'Bengaluru, India'),
            'contact_hours' => (string) $this->get('contact_hours', 'Mon-Sat: 9AM - 6PM'),
            'contact_whatsapp' => $this->get('contact_whatsapp', '918978605003'),
            'contact_heading' => (string) $this->get('contact_heading', 'Contact Us'),
            'contact_subtitle' => (string) $this->get('contact_subtitle', ''),
            'social_instagram' => $this->get('social_instagram', '#'),
            'social_facebook' => $this->get('social_facebook', '#'),
            'social_youtube' => $this->get('social_youtube', '#'),
            'announcement_1' => $this->get('announcement_1', 'FREE Delivery on Combo Packs'),
            'announcement_2' => $this->get('announcement_2', 'Just Add Water & Make Instant Dosa'),
            'announcement_3' => $this->get('announcement_3', 'Healthy Millet Breakfasts in Minutes'),
            'newsletter_heading' => $this->get('newsletter_heading', 'Stay Updated with Healthy Recipes & Offers!'),
            'home_category_subtitle' => (string) $this->get('home_category_subtitle', ''),
            'home_why_millet_title' => (string) $this->get('home_why_millet_title', 'Why Choose Millet?'),
            'home_hero_badge' => (string) $this->get('home_hero_badge', "INDIA'S FIRST"),
            'home_hero_title' => (string) $this->get('home_hero_title', 'Smart Instant Millet Breakfast'),
            'home_hero_subtitle' => (string) $this->get('home_hero_subtitle', 'Just Add Water • No Fermentation • No Refrigeration'),
            'home_hero_button_text' => (string) $this->get('home_hero_button_text', 'Shop Instant Breakfasts'),
            'home_usp_strip' => $this->jsonSetting('home_usp_strip', []),
            'home_trust_bar' => $this->jsonSetting('home_trust_bar', []),
            'home_how_it_works' => $this->jsonSetting('home_how_it_works', []),
            'home_value_props' => $this->jsonSetting('home_value_props', []),
            'home_why_millet_benefits' => $this->jsonSetting('home_why_millet_benefits', []),
            'founder_badge_title' => (string) $this->get('founder_badge_title', "Founder\n& Mother"),
            'founder_ribbon' => (string) $this->get('founder_ribbon', ''),
            'founder_heading_script' => (string) $this->get('founder_heading_script', ''),
            'founder_heading_bold' => (string) $this->get('founder_heading_bold', ''),
            'founder_body' => (string) $this->get('founder_body', ''),
            'founder_feature_1' => (string) $this->get('founder_feature_1', ''),
            'founder_feature_2' => (string) $this->get('founder_feature_2', ''),
            'founder_feature_3' => (string) $this->get('founder_feature_3', ''),
            'founder_cta_text' => (string) $this->get('founder_cta_text', 'Read Our Story'),
            'founder_cta_url' => (string) $this->get('founder_cta_url', '/about#founder-story'),
            'founder_signature_label' => (string) $this->get('founder_signature_label', '— Founder'),
            'founder_signature_brand' => (string) $this->get('founder_signature_brand', $this->brandName()),
            'founder_quote_note' => (string) $this->get('founder_quote_note', ''),
            'founder_photo_path' => (string) $this->get('founder_photo_path', ''),
            'founder_illustration_path' => (string) $this->get('founder_illustration_path', 'images/founder-kitchen-illustration.svg'),
            'about_hero_title' => (string) $this->get('about_hero_title', 'About S7 MilletCo'),
            'about_hero_subtitle' => (string) $this->get('about_hero_subtitle', ''),
            'about_journey_title' => (string) $this->get('about_journey_title', 'Our Journey'),
            'about_journey_p1' => (string) $this->get('about_journey_p1', ''),
            'about_journey_p2' => (string) $this->get('about_journey_p2', ''),
            'about_journey_bullets' => $this->jsonSetting('about_journey_bullets', []),
            'about_core_values' => $this->jsonSetting('about_core_values', []),
            'footer_tagline' => (string) $this->get('footer_tagline', ''),
            'footer_copyright' => (string) $this->get('footer_copyright', 'S7 MilletCo'),
            'footer_fssai' => (string) $this->get('footer_fssai', ''),
            'footer_vegetarian' => (string) $this->get('footer_vegetarian', '100% Vegetarian'),
            'seo_default_title' => $this->get('seo_default_title', 'S7 MilletCo — Healthy Millets Online'),
            'seo_default_description' => $this->get('seo_default_description', ''),
            'free_shipping_threshold' => $this->freeShippingThreshold(),
            'show_global_free_shipping_banner' => filter_var($this->get('show_global_free_shipping_banner', '0'), FILTER_VALIDATE_BOOLEAN),
            'theme_primary' => $this->themeColors()['primary'],
            'theme_accent' => $this->themeColors()['accent'],
            'theme_background' => $this->themeColors()['background'],
            'theme_text' => $this->themeColors()['text'],
            'theme_soft' => $this->themeColors()['soft'],
        ];
    }

    public function jsonSetting(string $key, array $default = []): array
    {
        $raw = $this->get($key);

        if (! $raw) {
            return $default;
        }

        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : $default;
    }

    public function invoiceLegalCompanyName(): string
    {
        $legal = trim((string) $this->get('invoice_legal_company_name', ''));

        return $legal !== '' ? $legal : $this->brandName();
    }

    public function invoiceLogoPath(): ?string
    {
        $path = trim((string) $this->get('invoice_logo_path', ''));

        return $path !== '' ? $path : null;
    }

    public function invoiceLogoUrl(): ?string
    {
        $path = $this->invoiceLogoPath();

        return $path ? asset($path) : null;
    }

    public function logoUrl(): string
    {
        $path = $this->get('logo_path');

        return $path ? asset($path) : asset('images/logo.png');
    }
}

