<form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="card p-4 sm:p-6 space-y-6">
    @csrf @method('PUT')
    <input type="hidden" name="settings_tab" value="branding">

    <div>
        <h3 class="font-display text-xl text-brand-chocolate mb-1">Branding</h3>
        <p class="text-sm text-gray-500">Website name, brand name, logo, and invoice details shown on PDF invoices.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Brand Name</label>
            <input type="text" name="brand_name" value="{{ old('brand_name', $settings['brand_name'] ?: 'Yungstr Club') }}" required class="input-field">
            <p class="text-xs text-gray-500 mt-1">Marketing name shown on the website.</p>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Legal Company Name (for invoices)</label>
            <input type="text" name="invoice_legal_company_name" value="{{ old('invoice_legal_company_name', $settings['invoice_legal_company_name'] ?? '') }}" class="input-field" placeholder="Yungstr Club Co.">
            <p class="text-xs text-gray-500 mt-1">Registered business name printed on tax invoices.</p>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Website Name</label>
            <input type="text" name="website_name" value="{{ old('website_name', $settings['website_name'] ?: 'Yungstr Club Online Store') }}" required class="input-field">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Invoice Prefix</label>
            <input type="text" name="invoice_prefix" value="{{ old('invoice_prefix', $settings['invoice_prefix'] ?: 'YC') }}" required class="input-field">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">GSTIN (optional)</label>
            <input type="text" name="invoice_gstin" value="{{ old('invoice_gstin', $settings['invoice_gstin']) }}" class="input-field">
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Invoice Business Address</label>
        <textarea name="invoice_address" rows="3" class="input-field !rounded-2xl">{{ old('invoice_address', $settings['invoice_address']) }}</textarea>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Logo Alt Text</label>
        <input type="text" name="logo_alt" value="{{ old('logo_alt', $settings['logo_alt']) }}" class="input-field">
    </div>

    <div>
        <h4 class="font-bold mb-3">Footer</h4>
        <div class="grid grid-cols-1 gap-4">
            <div><label class="block text-sm font-medium mb-1">Footer Tagline</label><input type="text" name="footer_tagline" value="{{ old('footer_tagline', $settings['footer_tagline']) }}" class="input-field"></div>
            <div><label class="block text-sm font-medium mb-1">Copyright Line</label><input type="text" name="footer_copyright" value="{{ old('footer_copyright', $settings['footer_copyright']) }}" class="input-field"></div>
            <div><label class="block text-sm font-medium mb-1">FSSAI License Text</label><input type="text" name="footer_fssai" value="{{ old('footer_fssai', $settings['footer_fssai']) }}" class="input-field"></div>
            <div><label class="block text-sm font-medium mb-1">Vegetarian Badge Text</label><input type="text" name="footer_vegetarian" value="{{ old('footer_vegetarian', $settings['footer_vegetarian']) }}" class="input-field"></div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium mb-2">Store Logo</label>
            @if($settings['logo_path'])
                <img src="{{ asset($settings['logo_path']) }}" alt="Logo" class="h-16 mb-3 object-contain">
            @else
                <img src="{{ asset('images/yungstr-logo.svg') }}" alt="Logo" class="h-16 mb-3 object-contain">
            @endif
            <input type="file" name="logo" accept="image/*" class="block w-full text-sm">
            <p class="text-xs text-gray-500 mt-1">Used on the website header.</p>
        </div>
        <div>
            <label class="block text-sm font-medium mb-2">Invoice Logo</label>
            @if(!empty($settings['invoice_logo_path']))
                <img src="{{ asset($settings['invoice_logo_path']) }}" alt="Invoice logo" class="h-16 mb-3 object-contain">
            @endif
            <input type="file" name="invoice_logo" accept="image/*" class="block w-full text-sm">
            <p class="text-xs text-gray-500 mt-1">Printed on PDF and print invoices. Falls back to store logo if empty.</p>
        </div>
        <div>
            <label class="block text-sm font-medium mb-2">Favicon</label>
            @if($settings['favicon_path'])
                <img src="{{ asset($settings['favicon_path']) }}" alt="Favicon" class="h-10 w-10 mb-3 object-contain">
            @endif
            <input type="file" name="favicon" accept="image/*" class="block w-full text-sm">
            <p class="text-xs text-gray-500 mt-1">Recommended: 64×64 px or 32×32 px PNG. Max 2 MB.</p>
        </div>
    </div>

    <button type="submit" class="btn-primary">Save Branding</button>
</form>
