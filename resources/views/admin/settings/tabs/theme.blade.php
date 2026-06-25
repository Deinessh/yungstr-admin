<form method="POST" action="{{ route('admin.settings.update') }}" class="card p-4 sm:p-6 space-y-6">
    @csrf @method('PUT')
    <input type="hidden" name="settings_tab" value="theme">

    <div>
        <h3 class="font-display text-xl text-brand-chocolate mb-1">Website Theme Colors</h3>
        <p class="text-sm text-gray-500">These colors apply across the storefront (buttons, headings, backgrounds). Changes appear immediately after save.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach([
            'theme_primary' => ['label' => 'Primary (Green)', 'default' => '#004D26'],
            'theme_accent' => ['label' => 'Accent (Orange)', 'default' => '#F26A2E'],
            'theme_background' => ['label' => 'Page Background', 'default' => '#FFFFFF'],
            'theme_text' => ['label' => 'Body Text', 'default' => '#1A3324'],
            'theme_soft' => ['label' => 'Soft Highlight', 'default' => '#E8F5EE'],
        ] as $key => $meta)
        <div>
            <label class="block text-sm font-medium mb-1">{{ $meta['label'] }}</label>
            <div class="flex items-center gap-3">
                <input type="color" name="{{ $key }}" value="{{ old($key, $settings[$key] ?? $meta['default']) }}" class="w-12 h-10 rounded border border-gray-200 cursor-pointer">
                <input type="text" value="{{ old($key, $settings[$key] ?? $meta['default']) }}" class="input-field font-mono text-sm" readonly tabindex="-1" data-color-preview="{{ $key }}">
            </div>
        </div>
        @endforeach
    </div>

    <div class="rounded-xl border border-amber-100 p-4 flex flex-wrap gap-3 items-center">
        <span class="text-sm text-gray-600">Preview:</span>
        <span class="px-4 py-2 rounded-full text-white text-sm font-bold" style="background: {{ $settings['theme_primary'] ?? '#004D26' }}">Primary</span>
        <span class="px-4 py-2 rounded-full text-white text-sm font-bold" style="background: {{ $settings['theme_accent'] ?? '#F26A2E' }}">Accent</span>
        <span class="px-4 py-2 rounded-full text-sm font-bold border" style="background: {{ $settings['theme_soft'] ?? '#E8F5EE' }}; color: {{ $settings['theme_text'] ?? '#1A3324' }}">Soft</span>
    </div>

    <button type="submit" class="btn-primary">Save Theme Colors</button>
</form>

<script>
document.querySelectorAll('input[type="color"]').forEach(function (picker) {
    picker.addEventListener('input', function () {
        const preview = document.querySelector('[data-color-preview="' + picker.name + '"]');
        if (preview) preview.value = picker.value;
    });
});
</script>
