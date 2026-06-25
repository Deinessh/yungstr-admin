<form method="POST" action="{{ route('admin.settings.update') }}" class="card p-4 sm:p-6 space-y-6">
    @csrf @method('PUT')
    <input type="hidden" name="settings_tab" value="general">

    <div>
        <h3 class="font-display text-xl text-brand-chocolate mb-1">General</h3>
        <p class="text-sm text-gray-500">Store identity, delivery scheduling, and email.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Store Name</label>
            <input type="text" name="store_name" value="{{ old('store_name', $settings['store_name']) }}" required class="input-field">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Tagline</label>
            <input type="text" name="store_tagline" value="{{ old('store_tagline', $settings['store_tagline']) }}" class="input-field">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Store Email</label>
            <input type="email" name="store_email" value="{{ old('store_email', $settings['store_email']) }}" required class="input-field">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Store Phone</label>
            <input type="text" name="store_phone" value="{{ old('store_phone', $settings['store_phone']) }}" required class="input-field">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Currency</label>
            <input type="text" name="currency" value="{{ old('currency', $settings['currency'] ?: 'INR') }}" required class="input-field">
        </div>
    </div>

    <p class="text-sm text-gray-500">Payment gateways (Razorpay, Cashfree, COD) are configured under the <strong>Payments</strong> tab.</p>

    <div>
        <h4 class="font-bold mb-3">Delivery Scheduling</h4>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Delivery Date Field</label>
                <select name="delivery_date_enabled" class="input-field">
                    <option value="1" @selected($settings['delivery_date_enabled'] == '1')>Enabled</option>
                    <option value="0" @selected($settings['delivery_date_enabled'] == '0')>Disabled</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Required at Checkout</label>
                <select name="delivery_date_required" class="input-field">
                    <option value="1" @selected($settings['delivery_date_required'] == '1')>Required</option>
                    <option value="0" @selected($settings['delivery_date_required'] == '0')>Optional</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Lead Days</label>
                <input type="number" name="delivery_lead_days" value="{{ old('delivery_lead_days', $settings['delivery_lead_days']) }}" class="input-field">
            </div>
        </div>
    </div>

    <div>
        <h4 class="font-bold mb-3">SMTP Email</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium mb-1">Host</label><input type="text" name="mail_host" value="{{ old('mail_host', $settings['mail_host']) }}" class="input-field"></div>
            <div><label class="block text-sm font-medium mb-1">Port</label><input type="number" name="mail_port" value="{{ old('mail_port', $settings['mail_port']) }}" class="input-field"></div>
            <div><label class="block text-sm font-medium mb-1">Username</label><input type="text" name="mail_username" value="{{ old('mail_username', $settings['mail_username']) }}" class="input-field"></div>
            <div><label class="block text-sm font-medium mb-1">Password</label><input type="password" name="mail_password" placeholder="Leave blank to keep current" class="input-field"></div>
            <div><label class="block text-sm font-medium mb-1">From Address</label><input type="email" name="mail_from_address" value="{{ old('mail_from_address', $settings['mail_from_address']) }}" class="input-field"></div>
            <div><label class="block text-sm font-medium mb-1">From Name</label><input type="text" name="mail_from_name" value="{{ old('mail_from_name', $settings['mail_from_name']) }}" class="input-field"></div>
            <input type="hidden" name="mail_mailer" value="smtp">
            <input type="hidden" name="mail_encryption" value="{{ $settings['mail_encryption'] ?: 'tls' }}">
        </div>
    </div>

    <button type="submit" class="btn-primary">Save General Settings</button>
</form>
