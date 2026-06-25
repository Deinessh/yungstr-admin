<form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="card p-4 sm:p-6 space-y-6">
    @csrf @method('PUT')
    <input type="hidden" name="settings_tab" value="home">

    <div>
        <h3 class="font-display text-xl text-brand-chocolate mb-1">Home page</h3>
        <p class="text-sm text-gray-500">Announcement bar, homepage sections, and the founder story banner shown on the homepage and About page.</p>
    </div>

    <div>
        <h4 class="font-bold mb-3">Announcement Bar</h4>
        <div class="grid grid-cols-1 gap-4">
            <div><label class="block text-sm font-medium mb-1">Line 1</label><input type="text" name="announcement_1" value="{{ old('announcement_1', $settings['announcement_1']) }}" class="input-field"></div>
            <div><label class="block text-sm font-medium mb-1">Line 2</label><input type="text" name="announcement_2" value="{{ old('announcement_2', $settings['announcement_2']) }}" class="input-field"></div>
            <div><label class="block text-sm font-medium mb-1">Line 3</label><input type="text" name="announcement_3" value="{{ old('announcement_3', $settings['announcement_3']) }}" class="input-field"></div>
        </div>
    </div>

    <div>
        <h4 class="font-bold mb-3">Hero Section</h4>
        <p class="text-xs text-gray-500 mb-3">Shared text shown on every homepage hero slide. Upload slide images under Hero Slides.</p>
        <div class="grid grid-cols-1 gap-4">
            <div><label class="block text-sm font-medium mb-1">Badge</label><input type="text" name="home_hero_badge" value="{{ old('home_hero_badge', $settings['home_hero_badge'] ?? '') }}" class="input-field" placeholder="INDIA'S FIRST"></div>
            <div><label class="block text-sm font-medium mb-1">Title</label><input type="text" name="home_hero_title" value="{{ old('home_hero_title', $settings['home_hero_title'] ?? '') }}" class="input-field"></div>
            <div><label class="block text-sm font-medium mb-1">Subtitle</label><input type="text" name="home_hero_subtitle" value="{{ old('home_hero_subtitle', $settings['home_hero_subtitle'] ?? '') }}" class="input-field"></div>
            <div><label class="block text-sm font-medium mb-1">Button Text</label><input type="text" name="home_hero_button_text" value="{{ old('home_hero_button_text', $settings['home_hero_button_text'] ?? '') }}" class="input-field" placeholder="Shop Instant Breakfasts"></div>
        </div>
    </div>

    <div>
        <h4 class="font-bold mb-3">Homepage Sections</h4>
        <div class="grid grid-cols-1 gap-4">
            <div><label class="block text-sm font-medium mb-1">Shop by Category Subtitle</label><input type="text" name="home_category_subtitle" value="{{ old('home_category_subtitle', $settings['home_category_subtitle']) }}" class="input-field"></div>
            <div><label class="block text-sm font-medium mb-1">Why Choose Millet Title</label><input type="text" name="home_why_millet_title" value="{{ old('home_why_millet_title', $settings['home_why_millet_title']) }}" class="input-field"></div>
            <div><label class="block text-sm font-medium mb-1">Newsletter Heading</label><input type="text" name="newsletter_heading" value="{{ old('newsletter_heading', $settings['newsletter_heading']) }}" class="input-field"></div>
        </div>
    </div>

    <div class="rounded-2xl border border-orange-100 bg-cream-bar/30 p-4 sm:p-6 space-y-5">
        <div>
            <h4 class="font-bold text-brand-chocolate">Founder Story Banner</h4>
            <p class="text-xs text-gray-500 mt-1">Matches the “From a Mother’s Vision” section on the homepage and About page.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium mb-2">Founder Photo</label>
                @php
                    $founderPreview = $settings['founder_photo_path']
                        ? asset($settings['founder_photo_path'])
                        : (file_exists(public_path('images/founder.jpg')) ? asset('images/founder.jpg') : asset('images/founder-photo-placeholder.svg'));
                @endphp
                <img src="{{ $founderPreview }}" alt="Founder preview" class="w-32 h-40 object-cover object-top rounded-2xl border border-cream-dark mb-3">
                <input type="file" name="founder_photo" accept="image/jpeg,image/png,image/jpg,image/webp" class="block w-full text-sm">
                <p class="text-xs text-gray-500 mt-1">Recommended: <strong>800×1000 px</strong> portrait (4:5), JPG/PNG/WebP, max 5 MB.</p>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Badge Text</label>
                <textarea name="founder_badge_title" rows="2" class="input-field !rounded-2xl" placeholder="Founder&#10;&amp; Mother">{{ old('founder_badge_title', $settings['founder_badge_title']) }}</textarea>
                <p class="text-xs text-gray-500 mt-1">Use a new line for the second line (e.g. “&amp; Mother”).</p>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Ribbon Text (below photo)</label>
                <input type="text" name="founder_ribbon" value="{{ old('founder_ribbon', $settings['founder_ribbon']) }}" class="input-field">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Heading Line 1 (script style)</label>
                <input type="text" name="founder_heading_script" value="{{ old('founder_heading_script', $settings['founder_heading_script']) }}" class="input-field">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Heading Line 2 (bold caps)</label>
                <input type="text" name="founder_heading_bold" value="{{ old('founder_heading_bold', $settings['founder_heading_bold']) }}" class="input-field">
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium mb-1">Story Body</label>
                <textarea name="founder_body" rows="4" class="input-field !rounded-2xl">{{ old('founder_body', $settings['founder_body']) }}</textarea>
            </div>

            <div><label class="block text-sm font-medium mb-1">Feature 1</label><input type="text" name="founder_feature_1" value="{{ old('founder_feature_1', $settings['founder_feature_1']) }}" class="input-field"></div>
            <div><label class="block text-sm font-medium mb-1">Feature 2</label><input type="text" name="founder_feature_2" value="{{ old('founder_feature_2', $settings['founder_feature_2']) }}" class="input-field"></div>
            <div><label class="block text-sm font-medium mb-1">Feature 3</label><input type="text" name="founder_feature_3" value="{{ old('founder_feature_3', $settings['founder_feature_3']) }}" class="input-field"></div>

            <div>
                <label class="block text-sm font-medium mb-1">CTA Button Text</label>
                <input type="text" name="founder_cta_text" value="{{ old('founder_cta_text', $settings['founder_cta_text']) }}" class="input-field">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">CTA Button Link</label>
                <input type="text" name="founder_cta_url" value="{{ old('founder_cta_url', $settings['founder_cta_url']) }}" class="input-field" placeholder="/about#founder-story">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Signature Label</label>
                <input type="text" name="founder_signature_label" value="{{ old('founder_signature_label', $settings['founder_signature_label']) }}" class="input-field" placeholder="— Founder">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Signature Brand Name</label>
                <input type="text" name="founder_signature_brand" value="{{ old('founder_signature_brand', $settings['founder_signature_brand']) }}" class="input-field">
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium mb-2">Right Illustration (optional)</label>
                @php
                    $illustrationPath = $settings['founder_illustration_path'] ?: 'images/founder-kitchen-illustration.svg';
                @endphp
                <img src="{{ asset($illustrationPath) }}" alt="Illustration preview" class="h-28 object-contain mb-3">
                <input type="file" name="founder_illustration" accept="image/svg+xml,image/png,image/jpeg,image/webp" class="block w-full text-sm">
                <p class="text-xs text-gray-500 mt-1">SVG or PNG recommended, ~400×500 px. Leave empty to keep current illustration.</p>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium mb-1">Founder Quote (shown on About page below banner)</label>
                <textarea name="founder_quote_note" rows="2" class="input-field !rounded-2xl">{{ old('founder_quote_note', $settings['founder_quote_note']) }}</textarea>
            </div>
        </div>
    </div>

    <button type="submit" class="btn-primary">Save Home Settings</button>
</form>
