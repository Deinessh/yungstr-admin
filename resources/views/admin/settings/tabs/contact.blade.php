<form method="POST" action="{{ route('admin.settings.update') }}" class="card p-4 sm:p-6 space-y-6">
    @csrf @method('PUT')
    <input type="hidden" name="settings_tab" value="contact">

    <div>
        <h3 class="font-display text-xl text-brand-chocolate mb-1">Contact</h3>
        <p class="text-sm text-gray-500">Contact page, footer, and floating WhatsApp button.</p>
    </div>

    <div class="grid grid-cols-1 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Page Heading</label>
            <input type="text" name="contact_heading" value="{{ old('contact_heading', $settings['contact_heading']) }}" class="input-field">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Page Subtitle</label>
            <textarea name="contact_subtitle" rows="2" class="input-field !rounded-2xl">{{ old('contact_subtitle', $settings['contact_subtitle']) }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Business Address</label>
            <textarea name="contact_address" rows="2" class="input-field !rounded-2xl">{{ old('contact_address', $settings['contact_address']) }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Business Hours</label>
            <input type="text" name="contact_hours" value="{{ old('contact_hours', $settings['contact_hours']) }}" class="input-field" placeholder="Mon-Sat: 9AM - 6PM">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">WhatsApp Number (digits only, with country code)</label>
            <input type="text" name="contact_whatsapp" value="{{ old('contact_whatsapp', $settings['contact_whatsapp']) }}" class="input-field" placeholder="918978605003">
        </div>
    </div>

    <div>
        <h4 class="font-bold mb-3">Social Links</h4>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div><label class="block text-sm font-medium mb-1">Instagram</label><input type="url" name="social_instagram" value="{{ old('social_instagram', $settings['social_instagram']) }}" class="input-field"></div>
            <div><label class="block text-sm font-medium mb-1">Facebook</label><input type="url" name="social_facebook" value="{{ old('social_facebook', $settings['social_facebook']) }}" class="input-field"></div>
            <div><label class="block text-sm font-medium mb-1">YouTube</label><input type="url" name="social_youtube" value="{{ old('social_youtube', $settings['social_youtube']) }}" class="input-field"></div>
        </div>
    </div>

    <button type="submit" class="btn-primary">Save Contact Settings</button>
</form>
