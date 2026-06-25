<?php

namespace App\Services;

class VisitPageService
{
    public function __construct(private SettingService $settings) {}

    /**
     * @return array{title: string, subtitle: string, logo_url: string, links: list<array{title: string, subtitle: string, url: string, icon: string, color_from: string, color_to: string}>}
     */
    public function pageData(): array
    {
        $links = $this->links();

        return [
            'title' => trim((string) $this->settings->get('visit_page_title', '')) ?: $this->settings->brandName(),
            'subtitle' => trim((string) $this->settings->get('visit_page_subtitle', ''))
                ?: 'Thanks for visiting — pick what you\'d like to do',
            'logo_url' => $this->logoUrl(),
            'links' => $links,
        ];
    }

    /**
     * @return list<array{title: string, subtitle: string, url: string, icon: string, color_from: string, color_to: string, enabled: bool}>
     */
    public function links(): array
    {
        $stored = $this->settings->jsonSetting('visit_page_links', []);

        if ($stored === []) {
            return $this->defaultLinks();
        }

        return $this->normalizeLinks($stored);
    }

    /**
     * @return list<array{title: string, subtitle: string, url: string, icon: string, color_from: string, color_to: string, enabled: bool}>
     */
    public function defaultLinks(): array
    {
        $site = rtrim((string) config('app.url'), '/');
        $whatsapp = preg_replace('/\D+/', '', (string) $this->settings->get('contact_whatsapp', ''));
        $whatsappUrl = $whatsapp !== ''
            ? 'https://wa.me/'.$whatsapp
            : $site;

        $instagram = $this->validUrl((string) $this->settings->get('social_instagram', ''));
        $facebook = $this->validUrl((string) $this->settings->get('social_facebook', ''));

        $links = [];

        if ($instagram) {
            $links[] = $this->linkPreset('Instagram', 'Follow us on Instagram', $instagram, 'fab fa-instagram', '#e1306c', '#f77737');
        }

        if ($facebook) {
            $links[] = $this->linkPreset('Facebook', 'Like us on Facebook', $facebook, 'fab fa-facebook-f', '#1877f2', '#0d5dbf');
        }

        $links[] = $this->linkPreset('WhatsApp', 'Chat with us on WhatsApp', $whatsappUrl, 'fab fa-whatsapp', '#25d366', '#128c7e');
        $links[] = $this->linkPreset('Visit Us', 'Visit our website', $site, 'fas fa-globe', '#0d9488', '#2563eb');

        return $links;
    }

    /**
     * @param  list<array<string, mixed>>  $raw
     * @return list<array{title: string, subtitle: string, url: string, icon: string, color_from: string, color_to: string, enabled: bool}>
     */
    public function normalizeLinks(array $raw): array
    {
        $links = [];

        foreach ($raw as $item) {
            if (! is_array($item)) {
                continue;
            }

            $enabled = ! isset($item['enabled']) || filter_var($item['enabled'], FILTER_VALIDATE_BOOLEAN);
            if (! $enabled) {
                continue;
            }

            $url = trim((string) ($item['url'] ?? ''));
            if ($url === '') {
                continue;
            }

            $title = trim((string) ($item['title'] ?? ''));
            if ($title === '') {
                continue;
            }

            $links[] = [
                'title' => $title,
                'subtitle' => trim((string) ($item['subtitle'] ?? '')),
                'url' => $url,
                'icon' => $this->sanitizeIcon((string) ($item['icon'] ?? 'fas fa-link')),
                'color_from' => $this->sanitizeColor((string) ($item['color_from'] ?? '#355e3b'), '#355e3b'),
                'color_to' => $this->sanitizeColor((string) ($item['color_to'] ?? '#4a7c4e'), '#4a7c4e'),
                'enabled' => true,
            ];
        }

        return $links;
    }

    /**
     * @param  list<array<string, mixed>>|null  $submitted
     * @return list<array{title: string, subtitle: string, url: string, icon: string, color_from: string, color_to: string, enabled: string}>
     */
    public function parseSubmittedLinks(?array $submitted): array
    {
        if (! is_array($submitted)) {
            return [];
        }

        $parsed = [];

        foreach ($submitted as $item) {
            if (! is_array($item)) {
                continue;
            }

            $parsed[] = [
                'title' => trim((string) ($item['title'] ?? '')),
                'subtitle' => trim((string) ($item['subtitle'] ?? '')),
                'url' => trim((string) ($item['url'] ?? '')),
                'icon' => $this->sanitizeIcon((string) ($item['icon'] ?? 'fas fa-link')),
                'color_from' => $this->sanitizeColor((string) ($item['color_from'] ?? '#355e3b'), '#355e3b'),
                'color_to' => $this->sanitizeColor((string) ($item['color_to'] ?? '#4a7c4e'), '#4a7c4e'),
                'enabled' => isset($item['enabled']) && (string) $item['enabled'] === '1' ? '1' : '0',
            ];
        }

        return $parsed;
    }

    /** @return array<string, string> */
    public function iconPresets(): array
    {
        return [
            'fab fa-instagram' => 'Instagram',
            'fab fa-facebook-f' => 'Facebook',
            'fab fa-whatsapp' => 'WhatsApp',
            'fab fa-youtube' => 'YouTube',
            'fab fa-x-twitter' => 'X (Twitter)',
            'fab fa-linkedin-in' => 'LinkedIn',
            'fab fa-pinterest-p' => 'Pinterest',
            'fab fa-telegram' => 'Telegram',
            'fab fa-google' => 'Google',
            'fas fa-globe' => 'Website',
            'fas fa-shopping-bag' => 'Shop',
            'fas fa-envelope' => 'Email',
            'fas fa-phone' => 'Phone',
            'fas fa-user-plus' => 'Save contact',
            'fas fa-map-marker-alt' => 'Location',
            'fas fa-link' => 'Generic link',
        ];
    }

    private function logoUrl(): string
    {
        $path = trim((string) $this->settings->get('visit_page_logo_path', ''));
        if ($path === '') {
            $path = trim((string) $this->settings->get('logo_path', ''));
        }

        if ($path !== '' && is_file(public_path(ltrim($path, '/')))) {
            return asset($path);
        }

        return asset('images/logo.png');
    }

    private function linkPreset(string $title, string $subtitle, string $url, string $icon, string $from, string $to): array
    {
        return [
            'title' => $title,
            'subtitle' => $subtitle,
            'url' => $url,
            'icon' => $icon,
            'color_from' => $from,
            'color_to' => $to,
            'enabled' => true,
        ];
    }

    private function validUrl(string $url): ?string
    {
        $url = trim($url);

        if ($url === '' || $url === '#') {
            return null;
        }

        if (! str_starts_with($url, 'http://') && ! str_starts_with($url, 'https://')) {
            $url = 'https://'.$url;
        }

        return filter_var($url, FILTER_VALIDATE_URL) ? $url : null;
    }

    private function sanitizeIcon(string $icon): string
    {
        $icon = preg_replace('/[^a-z0-9\s\-]/i', '', trim($icon)) ?? 'fas fa-link';

        return $icon !== '' ? $icon : 'fas fa-link';
    }

    private function sanitizeColor(string $color, string $fallback): string
    {
        $color = trim($color);

        return preg_match('/^#[0-9A-Fa-f]{6}$/', $color) ? $color : $fallback;
    }
}
