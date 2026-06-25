@php
    $meta = $paymentMeta ?? [];
    $statusBadge = fn (bool $live, bool $configured = true) => $live
        ? '<span class="text-xs font-semibold text-green-700 bg-green-50 px-2 py-0.5 rounded-full">Live at checkout</span>'
        : ($configured
            ? '<span class="text-xs font-semibold text-amber-800 bg-amber-50 px-2 py-0.5 rounded-full">Configured — disabled</span>'
            : '<span class="text-xs font-semibold text-gray-600 bg-gray-100 px-2 py-0.5 rounded-full">Not configured</span>');
@endphp

<form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
    @csrf @method('PUT')
    <input type="hidden" name="settings_tab" value="payments">

    <div class="card p-4 sm:p-6 space-y-3">
        <h3 class="font-display text-xl text-brand-chocolate">Payments</h3>
        <p class="text-sm text-gray-500">Enable or disable <strong>COD</strong>, <strong>Razorpay</strong>, and <strong>Cashfree</strong>. Customers only see methods that are enabled and correctly configured.</p>
        <div class="rounded-xl bg-brand-green-soft/60 border border-green-100 p-4 text-sm text-brand-chocolate space-y-2">
            <p class="font-semibold">How checkout works</p>
            <ol class="list-decimal list-inside space-y-1 text-gray-700">
                <li>Customer chooses a payment method on the checkout page.</li>
                <li><strong>COD</strong> — order is placed immediately; pay on delivery.</li>
                <li><strong>Razorpay</strong> — Razorpay popup → payment verified → order confirmed.</li>
                <li><strong>Cashfree</strong> — Cashfree hosted page → return URL + webhook confirm payment → order confirmed.</li>
            </ol>
        </div>
    </div>

    {{-- COD --}}
    <div class="card p-4 sm:p-6 space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h4 class="font-bold text-brand-chocolate">Cash on Delivery (COD)</h4>
                <p class="text-xs text-gray-500 mt-0.5">No API keys needed.</p>
            </div>
            {!! $statusBadge($meta['cod_live'] ?? false, true) !!}
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Status</label>
            <select name="cod_enabled" class="input-field max-w-xs">
                <option value="1" @selected(($settings['cod_enabled'] ?? '1') == '1')>Enabled</option>
                <option value="0" @selected(($settings['cod_enabled'] ?? '1') == '0')>Disabled</option>
            </select>
        </div>
    </div>

    {{-- Razorpay --}}
    <div class="card p-4 sm:p-6 space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h4 class="font-bold text-brand-chocolate">Razorpay</h4>
                <p class="text-xs text-gray-500 mt-0.5">UPI, cards, netbanking, wallets via Razorpay.</p>
            </div>
            {!! $statusBadge($meta['razorpay_live'] ?? false, $meta['razorpay_configured'] ?? false) !!}
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Status</label>
            <select name="razorpay_enabled" class="input-field max-w-xs">
                <option value="1" @selected(($settings['razorpay_enabled'] ?? '1') == '1')>Enabled</option>
                <option value="0" @selected(($settings['razorpay_enabled'] ?? '1') == '0')>Disabled</option>
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Key ID</label>
                <input type="text" name="razorpay_key_id" value="{{ old('razorpay_key_id', $settings['razorpay_key_id'] ?? '') }}" class="input-field" placeholder="rzp_live_... or rzp_test_...">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Key Secret</label>
                <input type="password" name="razorpay_key_secret" value="" placeholder="Leave blank to keep current" class="input-field" autocomplete="new-password">
            </div>
        </div>

        <details class="rounded-xl border border-gray-200 bg-gray-50/80 p-4 text-sm">
            <summary class="font-semibold text-brand-chocolate cursor-pointer">Razorpay setup instructions</summary>
            <div class="mt-3 space-y-3 text-gray-700">
                <p><strong>1. Create / log in to Razorpay</strong><br>
                    Go to <a href="https://dashboard.razorpay.com" target="_blank" rel="noopener" class="text-brand-orange hover:underline">dashboard.razorpay.com</a> and complete KYC for live payments.</p>
                <p><strong>2. Get API keys</strong><br>
                    Dashboard → <strong>Account &amp; Settings</strong> → <strong>API Keys</strong> → Generate Key.<br>
                    Copy <strong>Key ID</strong> and <strong>Key Secret</strong> into the fields above.</p>
                <p><strong>3. Test vs Live</strong><br>
                    Use <code class="bg-white px-1 rounded">rzp_test_...</code> keys with Test Mode ON for testing.<br>
                    Switch to <code class="bg-white px-1 rounded">rzp_live_...</code> keys for real payments.</p>
                <p><strong>4. Where keys go</strong><br>
                    Paste them here in <strong>Admin → Store Settings → Payments</strong> and set status to <strong>Enabled</strong>.</p>
                <p><strong>5. No webhook required</strong><br>
                    This store verifies Razorpay payments on your server when the customer completes checkout (signature verification).</p>
            </div>
        </details>
    </div>

    {{-- Cashfree --}}
    <div class="card p-4 sm:p-6 space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h4 class="font-bold text-brand-chocolate">Cashfree Payment Gateway</h4>
                <p class="text-xs text-gray-500 mt-0.5">UPI, cards, netbanking via Cashfree PG.</p>
            </div>
            {!! $statusBadge($meta['cashfree_live'] ?? false, $meta['cashfree_configured'] ?? false) !!}
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Status</label>
            <select name="cashfree_enabled" class="input-field max-w-xs">
                <option value="1" @selected(($settings['cashfree_enabled'] ?? '0') == '1')>Enabled</option>
                <option value="0" @selected(($settings['cashfree_enabled'] ?? '0') == '0')>Disabled</option>
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">App ID (Client ID)</label>
                <input type="text" name="cashfree_app_id" value="{{ old('cashfree_app_id', $settings['cashfree_app_id'] ?? '') }}" class="input-field" placeholder="TEST... or CF...">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Secret Key</label>
                <input type="password" name="cashfree_secret_key" value="" placeholder="Leave blank to keep current" class="input-field" autocomplete="new-password">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Environment</label>
                <select name="cashfree_environment" class="input-field">
                    <option value="sandbox" @selected(($settings['cashfree_environment'] ?? 'sandbox') === 'sandbox')>Sandbox (Test)</option>
                    <option value="production" @selected(($settings['cashfree_environment'] ?? 'sandbox') === 'production')>Production (Live)</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">Must match the keys you paste (sandbox keys + Sandbox, live keys + Production).</p>
            </div>
        </div>

        <div class="rounded-xl border border-amber-100 bg-amber-50/50 p-4 space-y-2 text-sm">
            <p class="font-semibold text-brand-chocolate">URLs to configure in Cashfree dashboard</p>
            <p class="text-gray-600">Set these in Cashfree → <strong>Developers</strong> → <strong>Webhooks</strong> (and ensure your site URL is <strong>{{ $meta['site_url'] ?? config('app.url') }}</strong>).</p>
            <div>
                <span class="text-xs font-medium text-gray-500">Webhook URL (notify when payment succeeds)</span>
                <code class="block mt-1 p-2 bg-white rounded-lg border text-xs break-all">{{ $meta['cashfree_webhook_url'] ?? route('webhooks.cashfree') }}</code>
            </div>
            <div>
                <span class="text-xs font-medium text-gray-500">Return URL (auto-set per order — example)</span>
                <code class="block mt-1 p-2 bg-white rounded-lg border text-xs break-all">{{ $meta['cashfree_return_example'] ?? url('/payment/cashfree/return/1') }}</code>
            </div>
        </div>

        <details class="rounded-xl border border-gray-200 bg-gray-50/80 p-4 text-sm">
            <summary class="font-semibold text-brand-chocolate cursor-pointer">Cashfree setup instructions</summary>
            <div class="mt-3 space-y-3 text-gray-700">
                <p><strong>1. Create / log in to Cashfree</strong><br>
                    Go to <a href="https://merchant.cashfree.com" target="_blank" rel="noopener" class="text-brand-orange hover:underline">merchant.cashfree.com</a> → sign up for Payment Gateway.</p>
                <p><strong>2. Get API credentials</strong><br>
                    <strong>Developers</strong> → <strong>API Keys</strong> (or Payment Gateway → Credentials).<br>
                    Copy <strong>App ID</strong> (Client ID) and <strong>Secret Key</strong>.</p>
                <p><strong>3. Sandbox first</strong><br>
                    Use <strong>Sandbox</strong> environment with test keys. Place a test order on your store with Cashfree selected.<br>
                    Cashfree test cards/UPI: see <a href="https://www.cashfree.com/docs/payments/online/resources/sandbox" target="_blank" rel="noopener" class="text-brand-orange hover:underline">Cashfree sandbox docs</a>.</p>
                <p><strong>4. Paste keys here</strong><br>
                    Admin → Store Settings → Payments → Cashfree section → set <strong>Enabled</strong> → Save.</p>
                <p><strong>5. Add webhook in Cashfree</strong><br>
                    Cashfree Dashboard → Developers → Webhooks → Add URL:<br>
                    <code class="bg-white px-1 rounded break-all">{{ $meta['cashfree_webhook_url'] ?? route('webhooks.cashfree') }}</code><br>
                    Enable events for <strong>Payment Success</strong> / order paid (PG order events).</p>
                <p><strong>6. Go live</strong><br>
                    Complete Cashfree KYC → switch to <strong>Production</strong> keys → set Environment to <strong>Production (Live)</strong> here → Save.</p>
                <p><strong>7. How it works on your store</strong><br>
                    Customer picks Cashfree → order created → Cashfree checkout opens → after payment, customer returns to your site and the webhook marks the order as <strong>paid</strong>.</p>
            </div>
        </details>
    </div>

    <button type="submit" class="btn-primary">Save Payment Settings</button>
</form>
