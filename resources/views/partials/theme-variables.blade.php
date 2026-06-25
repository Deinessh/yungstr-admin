@php
    $theme = $storeSettings ?? [];
    $primary = $theme['theme_primary'] ?? '#004D26';
    $accent = $theme['theme_accent'] ?? '#F26A2E';
    $background = $theme['theme_background'] ?? '#FFFFFF';
    $text = $theme['theme_text'] ?? '#1A3324';
    $soft = $theme['theme_soft'] ?? '#E8F5EE';
@endphp
<style>
:root {
    --theme-primary: {{ $primary }};
    --theme-accent: {{ $accent }};
    --theme-background: {{ $background }};
    --theme-text: {{ $text }};
    --theme-soft: {{ $soft }};
    --theme-primary-soft: color-mix(in srgb, {{ $primary }} 12%, white);
    --theme-primary-border: color-mix(in srgb, {{ $primary }} 18%, #E8E8E8);
}
.bg-cream,
.bg-cream-section { background-color: var(--theme-background) !important; }
.bg-cream-bar { background-color: var(--theme-soft) !important; }
.bg-brand-green { background-color: var(--theme-primary) !important; }
.bg-brand-green-soft { background-color: var(--theme-soft) !important; }
.bg-brand-orange { background-color: var(--theme-accent) !important; }
.text-brand-green,
.text-brand-green-logo { color: var(--theme-primary) !important; }
.text-brand-orange,
.text-brand-orange-logo { color: var(--theme-accent) !important; }
.text-brand-dark,
.text-brand-body,
.text-brand-chocolate,
.text-brand-brown { color: var(--theme-text) !important; }
.border-brand-green { border-color: var(--theme-primary) !important; }
.border-brand-orange { border-color: var(--theme-accent) !important; }
.btn-primary { background-color: var(--theme-accent) !important; }
.btn-primary:hover { filter: brightness(0.92); }
.hover\:text-brand-green:hover { color: var(--theme-primary) !important; }
.hover\:text-brand-orange:hover { color: var(--theme-accent) !important; }
.hover\:border-brand-green:hover { border-color: var(--theme-primary) !important; }
.focus\:ring-brand-green\/20:focus { --tw-ring-color: color-mix(in srgb, var(--theme-primary) 20%, transparent) !important; }
.focus\:ring-brand-green\/30:focus { --tw-ring-color: color-mix(in srgb, var(--theme-primary) 30%, transparent) !important; }
.focus\:border-brand-green\/40:focus { border-color: color-mix(in srgb, var(--theme-primary) 40%, transparent) !important; }
.store-header { background-color: var(--theme-background) !important; }
.announcement-bar { background-color: var(--theme-primary) !important; color: #fff !important; }
.announcement-bar i { color: rgba(255, 255, 255, 0.92) !important; }
.home-hero-split { background-color: var(--theme-background) !important; }
.home-hero-split__panel,
.home-hero-split__content,
.home-hero-split__media-col,
.home-hero-split__trust { background-color: var(--theme-background) !important; }
.home-hero-split__badge {
    background-color: var(--theme-primary-soft) !important;
    color: var(--theme-primary) !important;
}
.home-hero-split__usp-icon {
    color: var(--theme-primary) !important;
    border-color: var(--theme-primary-border) !important;
}
.home-hero-split__usp-label { color: var(--theme-text) !important; }
.home-hero-split__trust-icon {
    background-color: var(--theme-primary-soft) !important;
    border-color: var(--theme-primary-border) !important;
    color: var(--theme-primary) !important;
}
.home-usp-strip__icon {
    color: var(--theme-primary) !important;
    border-color: var(--theme-primary-border) !important;
}
.section-label { color: var(--theme-primary) !important; }
</style>
