<form method="POST" action="{{ route('admin.settings.update') }}" class="card p-4 sm:p-6 space-y-6">
    @csrf @method('PUT')
    <input type="hidden" name="settings_tab" value="about">

    <div>
        <h3 class="font-display text-xl text-brand-chocolate mb-1">About page</h3>
        <p class="text-sm text-gray-500">Content shown on the public About page. Founder banner is edited under Home page.</p>
    </div>

    <div class="grid grid-cols-1 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Hero Title</label>
            <input type="text" name="about_hero_title" value="{{ old('about_hero_title', $settings['about_hero_title']) }}" required class="input-field">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Hero Subtitle</label>
            <textarea name="about_hero_subtitle" rows="3" class="input-field !rounded-2xl">{{ old('about_hero_subtitle', $settings['about_hero_subtitle']) }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Journey Section Title</label>
            <input type="text" name="about_journey_title" value="{{ old('about_journey_title', $settings['about_journey_title']) }}" class="input-field">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Journey Paragraph 1</label>
            <textarea name="about_journey_p1" rows="3" class="input-field !rounded-2xl">{{ old('about_journey_p1', $settings['about_journey_p1']) }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Journey Paragraph 2</label>
            <textarea name="about_journey_p2" rows="3" class="input-field !rounded-2xl">{{ old('about_journey_p2', $settings['about_journey_p2']) }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Journey Bullet Points (one per line)</label>
            <textarea name="about_journey_bullets_text" rows="4" class="input-field !rounded-2xl" placeholder="100% Organic Sourcing">{{ old('about_journey_bullets_text', is_array($journeyBullets ?? null) ? implode("\n", $journeyBullets) : '') }}</textarea>
        </div>
    </div>

    <button type="submit" class="btn-primary">Save About Page</button>
</form>
