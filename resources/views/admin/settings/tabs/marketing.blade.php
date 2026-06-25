@php
    $initialLinks = old('visit_links', $visitPageLinks ?? []);
    if (! is_array($initialLinks)) {
        $initialLinks = [];
    }
@endphp

<form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6"
      x-data="visitLinksEditor(@js($initialLinks), @js($visitIconPresets ?? []))">
    @csrf @method('PUT')
    <input type="hidden" name="settings_tab" value="marketing">

    <div class="card p-4 sm:p-6 space-y-4">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h3 class="font-display text-xl text-brand-chocolate mb-1">Marketing QR Code</h3>
                <p class="text-sm text-gray-500 max-w-2xl">QR always points to <code class="text-xs bg-gray-100 px-1 rounded">/visit</code> — a link hub page you control below. Update links, icons, and colours anytime without reprinting.</p>
            </div>
            <a href="{{ route('visit') }}" target="_blank" rel="noopener" class="btn-outline text-sm shrink-0">Preview /visit page</a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium mb-1">QR scan link base URL</label>
                <input type="url" name="qr_scan_base_url" value="{{ old('qr_scan_base_url', $settings['qr_scan_base_url'] ?? config('app.url')) }}" class="input-field" placeholder="https://yungstrclub.com">
                <p class="text-xs text-gray-500 mt-1">Encoded in QR: <strong>{{ rtrim(old('qr_scan_base_url', $settings['qr_scan_base_url'] ?? config('app.url')), '/') }}/visit</strong></p>
                <label class="inline-flex items-center gap-2 cursor-pointer mt-3">
                    <input type="checkbox" name="regenerate_qr" value="1" class="rounded border-gray-300 text-brand-orange focus:ring-brand-orange">
                    <span class="text-sm">Regenerate QR image (only if scan domain changed)</span>
                </label>
            </div>
            <div class="rounded-2xl border border-amber-100 bg-white p-6 text-center">
                @if(!empty($qrImageUrl))
                    <img src="{{ $qrImageUrl }}" alt="Website marketing QR code" class="mx-auto max-w-[220px] w-full h-auto rounded-xl shadow-sm border border-gray-100">
                    <p class="text-xs text-gray-500 mt-4 break-all">Stable file: <code>{{ $qrStableFileUrl }}</code></p>
                    <a href="{{ $qrImageUrl }}" download="yungstrclub-website-qr.png" class="btn-outline text-sm mt-3 inline-flex">Download PNG</a>
                @else
                    <p class="text-sm text-gray-500">Save settings to generate your QR code.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="card p-4 sm:p-6 space-y-5">
        <div>
            <h3 class="font-display text-xl text-brand-chocolate mb-1">QR landing page (/visit)</h3>
            <p class="text-sm text-gray-500">Shown when someone scans your QR — social links, website, WhatsApp, and any buttons you add.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Page title</label>
                <input type="text" name="visit_page_title" value="{{ old('visit_page_title', $settings['visit_page_title'] ?? '') }}" class="input-field" placeholder="{{ $settings['brand_name'] ?? 'Yungstr Club' }}">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Page subtitle</label>
                <input type="text" name="visit_page_subtitle" value="{{ old('visit_page_subtitle', $settings['visit_page_subtitle'] ?? '') }}" class="input-field" placeholder="Thanks for visiting — pick what you'd like to do">
            </div>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3">
            <h4 class="font-bold text-brand-chocolate">Link buttons</h4>
            <button type="button" @click="addLink()" class="btn-outline text-sm">
                <i class="fas fa-plus mr-1"></i> Add link
            </button>
        </div>

        <template x-if="links.length === 0">
            <p class="text-sm text-gray-500 rounded-xl border border-dashed border-gray-200 p-6 text-center">No links yet. Click “Add link” or save once to use suggested defaults from Contact settings.</p>
        </template>

        <div class="space-y-4">
            <template x-for="(link, index) in links" :key="index">
                <div class="rounded-2xl border border-amber-100 bg-amber-50/30 p-4 sm:p-5 space-y-4">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <span class="text-sm font-semibold text-brand-chocolate" x-text="'Link ' + (index + 1)"></span>
                        <div class="flex items-center gap-2">
                            <label class="inline-flex items-center gap-2 text-sm cursor-pointer">
                                <input type="hidden" :name="'visit_links[' + index + '][enabled]'" value="0">
                                <input type="checkbox" :name="'visit_links[' + index + '][enabled]'" value="1" x-model="link.enabled" class="rounded border-gray-300 text-brand-orange focus:ring-brand-orange">
                                <span>Enabled</span>
                            </label>
                            <button type="button" @click="moveUp(index)" :disabled="index === 0" class="w-8 h-8 rounded-lg border border-gray-200 text-gray-600 hover:bg-white disabled:opacity-30" title="Move up"><i class="fas fa-chevron-up text-xs"></i></button>
                            <button type="button" @click="moveDown(index)" :disabled="index === links.length - 1" class="w-8 h-8 rounded-lg border border-gray-200 text-gray-600 hover:bg-white disabled:opacity-30" title="Move down"><i class="fas fa-chevron-down text-xs"></i></button>
                            <button type="button" @click="removeLink(index)" class="w-8 h-8 rounded-lg border border-red-200 text-red-600 hover:bg-red-50" title="Remove"><i class="fas fa-trash text-xs"></i></button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium mb-1">Button title</label>
                            <input type="text" :name="'visit_links[' + index + '][title]'" x-model="link.title" class="input-field" placeholder="Instagram" required>
                        </div>
                        <div>
                            <label class="block text-xs font-medium mb-1">Subtitle</label>
                            <input type="text" :name="'visit_links[' + index + '][subtitle]'" x-model="link.subtitle" class="input-field" placeholder="Follow us on Instagram">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-medium mb-1">URL</label>
                            <input type="url" :name="'visit_links[' + index + '][url]'" x-model="link.url" class="input-field" placeholder="https://instagram.com/...">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-end">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-medium mb-1">Icon (Font Awesome class)</label>
                            <select :name="'visit_links[' + index + '][icon]'" x-model="link.icon" class="input-field mb-2">
                                <template x-for="(label, value) in iconPresets" :key="value">
                                    <option :value="value" x-text="label"></option>
                                </template>
                            </select>
                            <input type="text" x-model="link.icon" class="input-field font-mono text-sm" placeholder="fab fa-instagram">
                        </div>
                        <div class="sm:col-span-1">
                            <label class="block text-xs font-medium mb-1">Gradient start</label>
                            <div class="flex gap-2 items-center">
                                <input type="color" x-model="link.color_from" class="h-10 w-14 rounded-lg border border-gray-200 cursor-pointer shrink-0">
                                <input type="text" :name="'visit_links[' + index + '][color_from]'" x-model="link.color_from" class="input-field font-mono text-sm" pattern="#[0-9A-Fa-f]{6}">
                            </div>
                        </div>
                        <div class="sm:col-span-1">
                            <label class="block text-xs font-medium mb-1">Gradient end</label>
                            <div class="flex gap-2 items-center">
                                <input type="color" x-model="link.color_to" class="h-10 w-14 rounded-lg border border-gray-200 cursor-pointer shrink-0">
                                <input type="text" :name="'visit_links[' + index + '][color_to]'" x-model="link.color_to" class="input-field font-mono text-sm" pattern="#[0-9A-Fa-f]{6}">
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 rounded-xl px-4 py-3 text-white text-sm shadow-sm"
                         :style="'background: linear-gradient(135deg, ' + link.color_from + ' 0%, ' + link.color_to + ' 100%)'">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white/25">
                            <i :class="link.icon" class="text-lg"></i>
                        </span>
                        <span class="flex-1 min-w-0">
                            <span class="block font-bold" x-text="link.title || 'Title'"></span>
                            <span class="block text-white/90 truncate" x-text="link.subtitle || 'Subtitle'"></span>
                        </span>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <button type="submit" class="btn-primary">Save Marketing &amp; QR Settings</button>
</form>

@push('scripts')
<script>
function visitLinksEditor(initialLinks, iconPresets) {
    const normalize = (link) => ({
        title: link.title ?? '',
        subtitle: link.subtitle ?? '',
        url: link.url ?? '',
        icon: link.icon ?? 'fas fa-link',
        color_from: link.color_from ?? '#355e3b',
        color_to: link.color_to ?? '#4a7c4e',
        enabled: link.enabled === '1' || link.enabled === true || link.enabled === 1,
    });

    return {
        links: (Array.isArray(initialLinks) ? initialLinks : []).map(normalize),
        iconPresets,
        addLink() {
            this.links.push({
                title: '',
                subtitle: '',
                url: '',
                icon: 'fas fa-link',
                color_from: '#355e3b',
                color_to: '#4a7c4e',
                enabled: true,
            });
        },
        removeLink(index) {
            this.links.splice(index, 1);
        },
        moveUp(index) {
            if (index > 0) {
                const item = this.links.splice(index, 1)[0];
                this.links.splice(index - 1, 0, item);
            }
        },
        moveDown(index) {
            if (index < this.links.length - 1) {
                const item = this.links.splice(index, 1)[0];
                this.links.splice(index + 1, 0, item);
            }
        },
    };
}
</script>
@endpush
