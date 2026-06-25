<div
    x-data="shippingPincodeChecker({
        pincode: @js(old('pincode', $pincode ?? $shippingLocation['pincode'] ?? '')),
        subtotal: {{ $subtotal ?? 0 }},
        initialQuote: @js($shippingQuote ?? null),
        quoteUrl: @js(route('shipping.quote')),
        csrf: @js(csrf_token()),
    })"
    class="rounded-2xl border border-amber-100 bg-amber-50/40 p-4 sm:p-5 space-y-3"
>
    <div class="flex items-start gap-3">
        <div class="w-9 h-9 rounded-xl bg-white border border-amber-100 flex items-center justify-center shrink-0">
            <i class="fas fa-location-dot text-brand-orange"></i>
        </div>
        <div class="flex-1 min-w-0">
            <h3 class="font-bold text-brand-dark text-sm sm:text-base">Check delivery to your PIN code</h3>
            <p class="text-xs text-gray-600 mt-1">Shipping charges and free-delivery eligibility depend on your location.</p>
        </div>
    </div>

    <div class="flex gap-2">
        <input
            type="text"
            x-model="pincode"
            maxlength="6"
            inputmode="numeric"
            pattern="\d{6}"
            placeholder="520001"
            class="input-field flex-1 min-w-0"
            @keydown.enter.prevent="check()"
        >
        <button type="button" class="btn-primary shrink-0 px-5" @click="check()" :disabled="loading">
            <span x-show="!loading">Check</span>
            <span x-show="loading">…</span>
        </button>
    </div>

    <template x-if="message">
        <div class="rounded-xl border px-4 py-3 text-sm" :class="resolved ? 'border-brand-green/30 bg-brand-green-soft/40 text-brand-dark' : 'border-amber-200 bg-white text-gray-700'">
            <p x-text="message"></p>
            <template x-if="resolved && zoneName">
                <p class="text-xs text-gray-600 mt-1">Zone: <span x-text="zoneName"></span></p>
            </template>
        </div>
    </template>
</div>
