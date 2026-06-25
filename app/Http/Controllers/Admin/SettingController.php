<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CashfreeService;
use App\Services\ImageUploadService;
use App\Services\QrCodeService;
use App\Services\RazorpayService;
use App\Services\SettingService;
use App\Services\VisitPageService;
use App\Services\VelocityShippingService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    private array $tabs = [
        'general' => 'General',
        'payments' => 'Payments',
        'theme' => 'Theme',
        'marketing' => 'Marketing QR',
        'branding' => 'Branding',
        'home' => 'Home page',
        'about' => 'About page',
        'contact' => 'Contact',
        'shipping' => 'Shipping',
        'seo' => 'SEO',
    ];

    public function __construct(
        private SettingService $settings,
        private ImageUploadService $images,
        private VelocityShippingService $velocity,
        private QrCodeService $qrCodes,
        private VisitPageService $visitPage,
        private RazorpayService $razorpay,
        private CashfreeService $cashfree,
    ) {}

    public function edit(Request $request)
    {
        $tab = $request->query('tab', 'general');
        if (! array_key_exists($tab, $this->tabs)) {
            $tab = 'general';
        }

        $this->qrCodes->ensureExists();

        return view('admin.settings.edit', [
            'tab' => $tab,
            'tabs' => $this->tabs,
            'settings' => $this->allSettings(),
            'seoOverrides' => $this->settings->pageSeoOverrides(),
            'journeyBullets' => $this->settings->jsonSetting('about_journey_bullets', []),
            'velocityTest' => session('velocity_test'),
            'qrImageUrl' => is_file($this->qrCodes->publicFilePath()) ? $this->qrCodes->publicUrl() : null,
            'qrStableFileUrl' => $this->qrCodes->stableFileUrl(),
            'visitPageLinks' => $this->visitPageStoredLinks(),
            'visitIconPresets' => $this->visitPage->iconPresets(),
            'paymentMeta' => $this->paymentMeta(),
        ]);
    }

    public function update(Request $request)
    {
        $tab = $request->input('settings_tab', 'general');

        try {
            return match ($tab) {
                'general' => $this->updateGeneral($request),
                'payments' => $this->updatePayments($request),
                'theme' => $this->updateTheme($request),
                'marketing' => $this->updateMarketing($request),
                'branding' => $this->updateBranding($request),
                'home' => $this->updateHome($request),
                'about' => $this->updateAbout($request),
                'contact' => $this->updateContact($request),
                'shipping' => $this->updateShipping($request),
                'seo' => $this->updateSeo($request),
                default => back()->with('error', 'Unknown settings tab.'),
            };
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', 'Could not save settings: '.$e->getMessage());
        }
    }

    public function testVelocity()
    {
        $result = $this->velocity->testConnection();

        return back()->with($result['ok'] ? 'success' : 'error', $result['message'])
            ->with('velocity_test', $result);
    }

    private function updateGeneral(Request $request)
    {
        $data = $request->validate([
            'store_name' => 'required|string|max:255',
            'store_tagline' => 'nullable|string|max:255',
            'store_email' => 'required|email|max:255',
            'store_phone' => 'required|string|max:30',
            'currency' => 'required|string|max:10',
            'delivery_date_enabled' => 'required|in:0,1',
            'delivery_date_required' => 'required|in:0,1',
            'delivery_lead_days' => 'required|integer|min:0|max:30',
            'mail_mailer' => 'nullable|string|max:50',
            'mail_host' => 'nullable|string|max:255',
            'mail_port' => 'nullable|integer|min:1|max:65535',
            'mail_username' => 'nullable|string|max:255',
            'mail_password' => 'nullable|string|max:255',
            'mail_encryption' => 'nullable|string|max:20',
            'mail_from_address' => 'nullable|email|max:255',
            'mail_from_name' => 'nullable|string|max:255',
        ]);

        if (! $request->filled('mail_password')) {
            unset($data['mail_password']);
        }

        $this->settings->setMany($data);

        return $this->savedRedirect('general');
    }

    private function updatePayments(Request $request)
    {
        $data = $request->validate([
            'cod_enabled' => 'required|in:0,1',
            'razorpay_enabled' => 'required|in:0,1',
            'razorpay_key_id' => 'nullable|string|max:255',
            'razorpay_key_secret' => 'nullable|string|max:255',
            'cashfree_enabled' => 'required|in:0,1',
            'cashfree_app_id' => 'nullable|string|max:255',
            'cashfree_secret_key' => 'nullable|string|max:255',
            'cashfree_environment' => 'required|in:sandbox,production',
        ]);

        if (! $request->filled('razorpay_key_secret')) {
            unset($data['razorpay_key_secret']);
        }

        if (! $request->filled('cashfree_secret_key')) {
            unset($data['cashfree_secret_key']);
        }

        if ($data['razorpay_enabled'] === '1' && empty($data['razorpay_key_id']) && ! $this->settings->razorpayKeyId()) {
            return back()->withInput()->with('error', 'Razorpay is enabled but Key ID is missing. Add API keys or set to Disabled.');
        }

        if ($data['razorpay_enabled'] === '1' && ! $request->filled('razorpay_key_secret') && ! $this->settings->razorpayKeySecret()) {
            return back()->withInput()->with('error', 'Razorpay is enabled but Key Secret is missing. Add API keys or set to Disabled.');
        }

        if ($data['cashfree_enabled'] === '1' && empty($data['cashfree_app_id']) && ! $this->settings->cashfreeAppId()) {
            return back()->withInput()->with('error', 'Cashfree is enabled but App ID is missing. Add API keys or set to Disabled.');
        }

        if ($data['cashfree_enabled'] === '1' && ! $request->filled('cashfree_secret_key') && ! $this->settings->cashfreeSecretKey()) {
            return back()->withInput()->with('error', 'Cashfree is enabled but Secret Key is missing. Add API keys or set to Disabled.');
        }

        $this->settings->setMany($data);

        return $this->savedRedirect('payments');
    }

    private function updateTheme(Request $request)
    {
        $data = $request->validate([
            'theme_primary' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'theme_accent' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'theme_background' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'theme_text' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'theme_soft' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        $this->settings->setMany($data);

        return $this->savedRedirect('theme');
    }

    private function updateMarketing(Request $request)
    {
        $data = $request->validate([
            'qr_scan_base_url' => 'required|url|max:255',
            'visit_page_title' => 'nullable|string|max:120',
            'visit_page_subtitle' => 'nullable|string|max:255',
            'visit_links' => 'nullable|array',
            'visit_links.*.title' => 'nullable|string|max:80',
            'visit_links.*.subtitle' => 'nullable|string|max:120',
            'visit_links.*.url' => 'nullable|string|max:500',
            'visit_links.*.icon' => 'nullable|string|max:80',
            'visit_links.*.color_from' => 'nullable|string|max:7',
            'visit_links.*.color_to' => 'nullable|string|max:7',
            'visit_links.*.enabled' => 'nullable|in:0,1',
            'regenerate_qr' => 'nullable|in:1',
        ]);

        $previousScanBase = $this->settings->qrScanBaseUrl();
        $visitLinks = $this->visitPage->parseSubmittedLinks($data['visit_links'] ?? []);

        $this->settings->setMany([
            'qr_scan_base_url' => rtrim($data['qr_scan_base_url'], '/'),
            'visit_page_title' => $data['visit_page_title'] ?? '',
            'visit_page_subtitle' => $data['visit_page_subtitle'] ?? '',
        ]);
        $this->settings->set('visit_page_links', json_encode($visitLinks));

        if ($request->boolean('regenerate_qr') || $previousScanBase !== rtrim($data['qr_scan_base_url'], '/')) {
            $this->qrCodes->generateAndStore();
        } else {
            $this->qrCodes->ensureExists();
        }

        return $this->savedRedirect('marketing');
    }

    /** @return list<array<string, string>> */
    private function visitPageStoredLinks(): array
    {
        $stored = $this->settings->jsonSetting('visit_page_links', []);

        if ($stored !== []) {
            return $this->visitPage->parseSubmittedLinks($stored);
        }

        return array_map(fn (array $link) => [
            'title' => $link['title'],
            'subtitle' => $link['subtitle'],
            'url' => $link['url'],
            'icon' => $link['icon'],
            'color_from' => $link['color_from'],
            'color_to' => $link['color_to'],
            'enabled' => '1',
        ], $this->visitPage->defaultLinks());
    }

    private function updateBranding(Request $request)
    {
        $data = $request->validate([
            'brand_name' => 'required|string|max:255',
            'website_name' => 'required|string|max:255',
            'invoice_legal_company_name' => 'nullable|string|max:255',
            'invoice_prefix' => 'required|string|max:20',
            'invoice_gstin' => 'nullable|string|max:20',
            'invoice_address' => 'nullable|string|max:2000',
            'logo_alt' => 'nullable|string|max:255',
            'footer_tagline' => 'nullable|string|max:255',
            'footer_copyright' => 'nullable|string|max:255',
            'footer_fssai' => 'nullable|string|max:255',
            'footer_vegetarian' => 'nullable|string|max:100',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'invoice_logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,webp,ico|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            try {
                $this->settings->set('logo_path', $this->images->storePublicImage($request->file('logo'), 'images', 'logo'));
            } catch (\Throwable $e) {
                return back()->withInput()->with('error', 'Logo upload failed: '.$e->getMessage());
            }
        }

        if ($request->hasFile('invoice_logo')) {
            try {
                $this->settings->set('invoice_logo_path', $this->images->storePublicImage($request->file('invoice_logo'), 'images', 'invoice-logo'));
            } catch (\Throwable $e) {
                return back()->withInput()->with('error', 'Invoice logo upload failed: '.$e->getMessage());
            }
        }

        if ($request->hasFile('favicon')) {
            try {
                $this->settings->set('favicon_path', $this->images->storePublicImage($request->file('favicon'), 'images', 'favicon'));
            } catch (\Throwable $e) {
                return back()->withInput()->with('error', 'Favicon upload failed: '.$e->getMessage());
            }
        }

        unset($data['logo'], $data['favicon'], $data['invoice_logo']);
        $this->settings->setMany($data);

        return $this->savedRedirect('branding');
    }

    private function updateHome(Request $request)
    {
        $data = $request->validate([
            'announcement_1' => 'nullable|string|max:255',
            'announcement_2' => 'nullable|string|max:255',
            'announcement_3' => 'nullable|string|max:255',
            'newsletter_heading' => 'nullable|string|max:255',
            'home_category_subtitle' => 'nullable|string|max:255',
            'home_why_millet_title' => 'nullable|string|max:255',
            'home_hero_badge' => 'nullable|string|max:100',
            'home_hero_title' => 'nullable|string|max:255',
            'home_hero_subtitle' => 'nullable|string|max:255',
            'home_hero_button_text' => 'nullable|string|max:100',
            'founder_badge_title' => 'nullable|string|max:255',
            'founder_ribbon' => 'nullable|string|max:255',
            'founder_heading_script' => 'nullable|string|max:255',
            'founder_heading_bold' => 'nullable|string|max:255',
            'founder_body' => 'nullable|string|max:5000',
            'founder_feature_1' => 'nullable|string|max:255',
            'founder_feature_2' => 'nullable|string|max:255',
            'founder_feature_3' => 'nullable|string|max:255',
            'founder_cta_text' => 'nullable|string|max:100',
            'founder_cta_url' => 'nullable|string|max:255',
            'founder_signature_label' => 'nullable|string|max:100',
            'founder_signature_brand' => 'nullable|string|max:255',
            'founder_quote_note' => 'nullable|string|max:1000',
            'founder_photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'founder_illustration' => 'nullable|file|max:5120',
        ]);

        if ($request->hasFile('founder_photo')) {
            try {
                $old = $this->settings->get('founder_photo_path');
                $this->settings->set('founder_photo_path', $this->images->storeFounderImage($request->file('founder_photo'), $old));
            } catch (\Throwable $e) {
                return back()->withInput()->with('error', 'Founder photo upload failed: '.$e->getMessage());
            }
        }

        if ($request->hasFile('founder_illustration')) {
            try {
                $old = $this->settings->get('founder_illustration_path');
                $this->settings->set('founder_illustration_path', $this->images->storeFounderIllustration($request->file('founder_illustration'), $old));
            } catch (\Throwable $e) {
                return back()->withInput()->with('error', 'Illustration upload failed: '.$e->getMessage());
            }
        }

        unset($data['founder_photo'], $data['founder_illustration']);
        $this->settings->setMany($data);

        return $this->savedRedirect('home');
    }

    private function updateAbout(Request $request)
    {
        $data = $request->validate([
            'about_hero_title' => 'required|string|max:255',
            'about_hero_subtitle' => 'nullable|string|max:5000',
            'about_journey_title' => 'nullable|string|max:255',
            'about_journey_p1' => 'nullable|string|max:5000',
            'about_journey_p2' => 'nullable|string|max:5000',
            'about_journey_bullets_text' => 'nullable|string|max:2000',
        ]);

        $bullets = array_values(array_filter(array_map('trim', explode("\n", $data['about_journey_bullets_text'] ?? ''))));
        unset($data['about_journey_bullets_text']);

        $this->settings->setMany($data);
        $this->settings->set('about_journey_bullets', json_encode($bullets));

        return $this->savedRedirect('about');
    }

    private function updateContact(Request $request)
    {
        $data = $request->validate([
            'contact_heading' => 'nullable|string|max:255',
            'contact_subtitle' => 'nullable|string|max:2000',
            'contact_address' => 'nullable|string|max:2000',
            'contact_hours' => 'nullable|string|max:255',
            'contact_whatsapp' => 'nullable|string|max:20',
            'social_instagram' => 'nullable|string|max:255',
            'social_facebook' => 'nullable|string|max:255',
            'social_youtube' => 'nullable|string|max:255',
        ]);

        $this->settings->setMany($data);

        return $this->savedRedirect('contact');
    }

    private function updateShipping(Request $request)
    {
        $data = $request->validate([
            'show_global_free_shipping_banner' => 'required|in:0,1',
            'velocity_enabled' => 'required|in:0,1',
            'velocity_auto_ship' => 'required|in:0,1',
            'velocity_username' => 'nullable|string|max:30',
            'velocity_password' => 'nullable|string|max:255',
            'velocity_warehouse_id' => 'nullable|string|max:50',
            'velocity_pickup_location' => 'nullable|string|max:100',
            'velocity_warehouse_pincode' => 'nullable|string|max:10',
            'velocity_warehouse_city' => 'nullable|string|max:100',
            'velocity_warehouse_state' => 'nullable|string|max:100',
            'velocity_warehouse_address' => 'nullable|string|max:2000',
            'velocity_default_carrier_id' => 'nullable|string|max:50',
            'velocity_package_length' => 'nullable|numeric|min:1',
            'velocity_package_breadth' => 'nullable|numeric|min:1',
            'velocity_package_height' => 'nullable|numeric|min:1',
            'velocity_package_weight' => 'nullable|numeric|min:0.1',
        ]);

        if (! $request->filled('velocity_password')) {
            unset($data['velocity_password']);
        }

        if ($data['velocity_enabled'] === '1') {
            $hasPassword = $request->filled('velocity_password') || $this->settings->get('velocity_password');
            if (! $hasPassword || empty($data['velocity_username']) || empty($data['velocity_warehouse_id'])) {
                return back()->withInput()->with('error', 'To enable Velocity, save API Username, API Password, and Warehouse ID.');
            }
        }

        $this->settings->setMany($data);

        return $this->savedRedirect('shipping');
    }

    private function updateSeo(Request $request)
    {
        $overridesInput = collect($request->input('seo_overrides', []))
            ->filter(fn ($row) => filled($row['page'] ?? null))
            ->map(function ($row) {
                if (empty($row['canonical'])) {
                    $row['canonical'] = null;
                }

                return $row;
            })
            ->values()
            ->all();

        $request->merge(['seo_overrides' => $overridesInput]);

        $data = $request->validate([
            'seo_default_title' => 'required|string|max:255',
            'seo_default_description' => 'nullable|string|max:500',
            'seo_default_keywords' => 'nullable|string|max:500',
            'seo_overrides' => 'nullable|array',
            'seo_overrides.*.page' => 'required|string|max:50',
            'seo_overrides.*.title' => 'nullable|string|max:255',
            'seo_overrides.*.description' => 'nullable|string|max:500',
            'seo_overrides.*.canonical' => 'nullable|string|max:255',
            'seo_overrides.*.sitemap' => 'nullable|in:0,1',
        ]);

        $overrides = collect($data['seo_overrides'] ?? [])->values()->all();

        $this->settings->setMany([
            'seo_default_title' => $data['seo_default_title'],
            'seo_default_description' => $data['seo_default_description'] ?? '',
            'seo_default_keywords' => $data['seo_default_keywords'] ?? '',
            'page_seo_overrides' => json_encode($overrides),
        ]);

        return $this->savedRedirect('seo');
    }

    private function allSettings(): array
    {
        $keys = [
            'store_name', 'store_tagline', 'store_email', 'store_phone', 'currency',
            'brand_name', 'website_name', 'logo_path', 'favicon_path', 'logo_alt',
            'invoice_prefix', 'invoice_gstin', 'invoice_address', 'invoice_legal_company_name', 'invoice_logo_path',
            'footer_tagline', 'footer_copyright', 'footer_fssai', 'footer_vegetarian',
            'announcement_1', 'announcement_2', 'announcement_3', 'newsletter_heading',
            'home_category_subtitle', 'home_why_millet_title',
            'home_hero_badge', 'home_hero_title', 'home_hero_subtitle', 'home_hero_button_text',
            'founder_badge_title', 'founder_ribbon', 'founder_heading_script', 'founder_heading_bold', 'founder_body',
            'founder_feature_1', 'founder_feature_2', 'founder_feature_3', 'founder_cta_text', 'founder_cta_url',
            'founder_signature_label', 'founder_signature_brand', 'founder_quote_note',
            'founder_photo_path', 'founder_illustration_path',
            'about_hero_title', 'about_hero_subtitle', 'about_journey_title', 'about_journey_p1', 'about_journey_p2',
            'contact_heading', 'contact_subtitle', 'contact_address', 'contact_hours', 'contact_whatsapp',
            'social_instagram', 'social_facebook', 'social_youtube',
            'show_global_free_shipping_banner',
            'velocity_enabled', 'velocity_auto_ship', 'velocity_username', 'velocity_password',
            'velocity_warehouse_id', 'velocity_pickup_location', 'velocity_warehouse_pincode',
            'velocity_warehouse_city', 'velocity_warehouse_state', 'velocity_warehouse_address',
            'velocity_default_carrier_id', 'velocity_package_length', 'velocity_package_breadth',
            'velocity_package_height', 'velocity_package_weight',
            'seo_default_title', 'seo_default_description', 'seo_default_keywords',
            'razorpay_key_id', 'razorpay_key_secret', 'razorpay_enabled',
            'cashfree_enabled', 'cashfree_app_id', 'cashfree_secret_key', 'cashfree_environment',
            'cod_enabled',
            'theme_primary', 'theme_accent', 'theme_background', 'theme_text', 'theme_soft',
            'qr_scan_base_url', 'qr_redirect_url', 'qr_generated_at',
            'visit_page_title', 'visit_page_subtitle', 'visit_page_links',
            'delivery_date_enabled', 'delivery_date_required', 'delivery_lead_days',
            'mail_mailer', 'mail_host', 'mail_port', 'mail_username', 'mail_password',
            'mail_encryption', 'mail_from_address', 'mail_from_name',
        ];

        $settings = [];
        foreach ($keys as $key) {
            $settings[$key] = $this->settings->get($key, '');
        }

        $settings['cod_enabled'] = $this->settings->codEnabled() ? '1' : '0';
        $settings['delivery_date_enabled'] = $this->settings->deliveryDateEnabled() ? '1' : '0';
        $settings['delivery_date_required'] = $this->settings->deliveryDateRequired() ? '1' : '0';

        return $settings;
    }

    /** @return array<string, mixed> */
    private function paymentMeta(): array
    {
        return [
            'site_url' => rtrim((string) config('app.url'), '/'),
            'cashfree_webhook_url' => route('webhooks.cashfree'),
            'cashfree_return_example' => route('payment.cashfree.return', ['order' => 1]),
            'cod_live' => $this->settings->codEnabled(),
            'razorpay_configured' => (bool) $this->settings->razorpayKeyId() && (bool) $this->settings->razorpayKeySecret(),
            'razorpay_live' => $this->razorpay->isEnabled(),
            'cashfree_configured' => (bool) $this->settings->cashfreeAppId() && (bool) $this->settings->cashfreeSecretKey(),
            'cashfree_live' => $this->cashfree->isEnabled(),
        ];
    }

    private function savedRedirect(string $tab)
    {
        return redirect()->route('admin.settings.edit', ['tab' => $tab])
            ->with('success', 'Settings saved successfully.');
    }
}
