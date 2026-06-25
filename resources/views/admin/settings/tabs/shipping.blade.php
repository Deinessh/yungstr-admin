<form method="POST" action="{{ route('admin.settings.update') }}" class="card p-4 sm:p-6 space-y-6">
    @csrf @method('PUT')
    <input type="hidden" name="settings_tab" value="shipping">

    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div>
            <h3 class="font-display text-xl text-brand-chocolate mb-1">Shipping</h3>
            <p class="text-sm text-gray-500">Zone-wise checkout rates, Velocity fulfilment, and delivery messaging.</p>
        </div>
        <a href="{{ route('admin.shipping-zones.index') }}" class="btn-outline text-sm whitespace-nowrap">Manage Shipping Zones</a>
    </div>

    <div class="rounded-2xl border border-cream-dark bg-cream-bar/40 p-4 text-sm space-y-3">
        <h4 class="font-bold text-brand-chocolate">Zone-wise delivery (checkout)</h4>
        <p class="text-gray-700">All delivery fees and free-shipping thresholds are set in <strong>Shipping Zones</strong> (including the default “Rest of India” zone). Customers enter their PIN at checkout to see location-specific charges.</p>
        <p class="text-xs text-gray-600"><strong>Example setup:</strong> Hyderabad free above ₹399 · Telangana free above ₹599 · Rest of India free above ₹799.</p>
    </div>

    <div>
        <h4 class="font-bold mb-3">Global Free-Shipping Banner</h4>
        <label class="flex items-start gap-3 text-sm text-gray-700">
            <input type="hidden" name="show_global_free_shipping_banner" value="0">
            <input type="checkbox" name="show_global_free_shipping_banner" value="1" @checked(old('show_global_free_shipping_banner', $settings['show_global_free_shipping_banner'] ?? '0') == '1')>
            <span>Show a site-wide free-shipping message in the top announcement bar.<br><span class="text-xs text-gray-500">Leave unchecked — customers should only see free-shipping eligibility after PIN code detection.</span></span>
        </label>
    </div>

    <div class="rounded-2xl border border-cream-dark bg-cream-bar/40 p-4 text-sm space-y-3">
        <h4 class="font-bold text-brand-chocolate">How automatic shipping works</h4>
        <ol class="list-decimal list-inside space-y-2 text-gray-700">
            <li>Customer places an order (COD or prepaid). When payment is confirmed, the order status becomes <strong>confirmed</strong>.</li>
            <li>The system automatically calls Velocity <code class="text-xs bg-white px-1 rounded">/forward-order-orchestration</code> to create the shipment, assign a courier, and generate an AWB + shipping label.</li>
            <li>An invoice is generated automatically with your <strong>legal company name</strong>, <strong>invoice logo</strong>, and <strong>brand name</strong> from Branding settings.</li>
            <li>Tracking is synced every hour via AWB. Order status updates to <strong>shipped</strong> / <strong>delivered</strong> automatically.</li>
        </ol>
        <p class="text-xs text-gray-600"><strong>Setup steps:</strong> Create a Velocity account → create a warehouse in their portal → copy Warehouse ID here → enter API username (+91…) and password → enable Velocity + Auto-ship → Test Connection.</p>
    </div>

    <div>
        <h4 class="font-bold mb-3">Velocity Shipping API</h4>
        @php
            $velocityPasswordSaved = ! empty($settings['velocity_password']);
            $velocityReady = $velocityPasswordSaved && ! empty($settings['velocity_username']) && ! empty($settings['velocity_warehouse_id']);
        @endphp
        <div class="mb-4 rounded-xl border p-3 text-sm {{ $velocityReady ? 'border-green-200 bg-green-50 text-green-900' : 'border-amber-200 bg-amber-50 text-amber-950' }}">
            @if($velocityReady)
                <p><strong>Credentials saved.</strong> Username and warehouse are set; password is on file. Use <em>Test Velocity Connection</em> below to verify login.</p>
            @else
                <p><strong>Setup incomplete.</strong>
                    @unless($velocityPasswordSaved) API Password is not saved yet — you must enter it and click <em>Save Shipping Settings</em> before testing. @endunless
                    @if(empty($settings['velocity_username'])) Username missing. @endif
                    @if(empty($settings['velocity_warehouse_id'])) Warehouse ID missing. @endif
                </p>
            @endif
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Enable Velocity</label>
                <select name="velocity_enabled" class="input-field">
                    <option value="1" @selected($settings['velocity_enabled'] == '1')>Enabled</option>
                    <option value="0" @selected($settings['velocity_enabled'] != '1')>Disabled</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Auto-create shipment on order confirm</label>
                <select name="velocity_auto_ship" class="input-field">
                    <option value="1" @selected($settings['velocity_auto_ship'] != '0')>Yes — fully automatic</option>
                    <option value="0" @selected($settings['velocity_auto_ship'] == '0')>No — manual only</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">API Username (+91…)</label>
                <input type="text" name="velocity_username" value="{{ old('velocity_username', $settings['velocity_username']) }}" class="input-field" placeholder="+919866340090">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">API Password</label>
                <input type="password" name="velocity_password" placeholder="{{ $velocityPasswordSaved ? 'Leave blank to keep current' : 'Required — enter Velocity password' }}" class="input-field" autocomplete="new-password">
                @unless($velocityPasswordSaved)
                    <p class="text-xs text-amber-700 mt-1">Password not saved yet. Enter it here and click Save before testing connection.</p>
                @endunless
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Warehouse ID</label>
                <input type="text" name="velocity_warehouse_id" value="{{ old('velocity_warehouse_id', $settings['velocity_warehouse_id']) }}" class="input-field" placeholder="WH66DU">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Pickup Location Name</label>
                <input type="text" name="velocity_pickup_location" value="{{ old('velocity_pickup_location', $settings['velocity_pickup_location']) }}" class="input-field">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Warehouse Pincode</label>
                <input type="text" name="velocity_warehouse_pincode" value="{{ old('velocity_warehouse_pincode', $settings['velocity_warehouse_pincode']) }}" class="input-field">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Warehouse City</label>
                <input type="text" name="velocity_warehouse_city" value="{{ old('velocity_warehouse_city', $settings['velocity_warehouse_city']) }}" class="input-field">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Warehouse State</label>
                <input type="text" name="velocity_warehouse_state" value="{{ old('velocity_warehouse_state', $settings['velocity_warehouse_state']) }}" class="input-field">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium mb-1">Warehouse Address</label>
                <textarea name="velocity_warehouse_address" rows="2" class="input-field !rounded-2xl">{{ old('velocity_warehouse_address', $settings['velocity_warehouse_address']) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Default Carrier ID (optional)</label>
                <input type="text" name="velocity_default_carrier_id" value="{{ old('velocity_default_carrier_id', $settings['velocity_default_carrier_id']) }}" class="input-field" placeholder="Leave blank for auto-assign">
            </div>
        </div>
    </div>

    <div>
        <h4 class="font-bold mb-3">Default Package Dimensions</h4>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div><label class="block text-sm font-medium mb-1">Length (cm)</label><input type="number" step="0.1" name="velocity_package_length" value="{{ old('velocity_package_length', $settings['velocity_package_length'] ?: 20) }}" class="input-field"></div>
            <div><label class="block text-sm font-medium mb-1">Breadth (cm)</label><input type="number" step="0.1" name="velocity_package_breadth" value="{{ old('velocity_package_breadth', $settings['velocity_package_breadth'] ?: 15) }}" class="input-field"></div>
            <div><label class="block text-sm font-medium mb-1">Height (cm)</label><input type="number" step="0.1" name="velocity_package_height" value="{{ old('velocity_package_height', $settings['velocity_package_height'] ?: 10) }}" class="input-field"></div>
            <div><label class="block text-sm font-medium mb-1">Weight (kg)</label><input type="number" step="0.01" name="velocity_package_weight" value="{{ old('velocity_package_weight', $settings['velocity_package_weight'] ?: 0.5) }}" class="input-field"></div>
        </div>
        <p class="text-xs text-gray-500 mt-2">Per-product weight (kg) in product edit overrides this when calculating total shipment weight.</p>
    </div>

    <div class="flex flex-wrap gap-3">
        <button type="submit" class="btn-primary">Save Shipping Settings</button>
    </div>
</form>

<form method="POST" action="{{ route('admin.settings.velocity-test') }}" class="mt-4 flex flex-wrap items-center gap-3">
    @csrf
    <button type="submit" class="btn-outline text-sm">Test Velocity Connection</button>
    <span class="text-xs text-gray-500">Save settings first, then test. Velocity must be enabled only after a successful test.</span>
</form>
