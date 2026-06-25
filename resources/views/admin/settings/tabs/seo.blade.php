<form method="POST" action="{{ route('admin.settings.update') }}" class="card p-4 sm:p-6 space-y-6" x-data="{ rows: @js(old('seo_overrides', $seoOverrides ?: [['page' => '', 'title' => '', 'description' => '', 'canonical' => '', 'sitemap' => '1']])) }">
    @csrf @method('PUT')
    <input type="hidden" name="settings_tab" value="seo">

    <div>
        <h3 class="font-display text-xl text-brand-chocolate mb-1">Per-page SEO overrides</h3>
        <p class="text-sm text-gray-500">Override meta title, description, canonical, and sitemap settings on a per-page basis. Use page identifiers like <code>home</code>, <code>catalogue</code>, <code>about</code>, <code>contact</code>.</p>
    </div>

    <div class="grid grid-cols-1 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Default Meta Title</label>
            <input type="text" name="seo_default_title" value="{{ old('seo_default_title', $settings['seo_default_title']) }}" required class="input-field">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Default Meta Description</label>
            <textarea name="seo_default_description" rows="2" class="input-field !rounded-2xl">{{ old('seo_default_description', $settings['seo_default_description']) }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Default Keywords</label>
            <input type="text" name="seo_default_keywords" value="{{ old('seo_default_keywords', $settings['seo_default_keywords']) }}" class="input-field">
        </div>
    </div>

    <div class="space-y-4">
        <template x-for="(row, index) in rows" :key="index">
            <div class="rounded-2xl border border-orange-100 p-4 grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium mb-1">Page ID</label>
                    <input type="text" :name="'seo_overrides['+index+'][page]'" x-model="row.page" class="input-field" placeholder="catalogue">
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1">Meta Title</label>
                    <input type="text" :name="'seo_overrides['+index+'][title]'" x-model="row.title" class="input-field">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium mb-1">Meta Description</label>
                    <textarea :name="'seo_overrides['+index+'][description]'" x-model="row.description" rows="2" class="input-field !rounded-2xl"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1">Canonical URL</label>
                    <input type="text" :name="'seo_overrides['+index+'][canonical]'" x-model="row.canonical" class="input-field" placeholder="https://...">
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1">Include in Sitemap</label>
                    <select :name="'seo_overrides['+index+'][sitemap]'" x-model="row.sitemap" class="input-field">
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <button type="button" @click="rows.splice(index, 1)" class="text-xs text-red-600 font-medium">Remove override</button>
                </div>
            </div>
        </template>
    </div>

    <button type="button" @click="rows.push({page:'', title:'', description:'', canonical:'', sitemap:'1'})" class="inline-flex items-center gap-2 border border-gray-300 rounded-xl px-4 py-2 text-sm font-medium hover:bg-gray-50">
        <span>+</span> Add page override
    </button>

    <div>
        <button type="submit" class="btn-primary">Save SEO Settings</button>
    </div>
</form>
