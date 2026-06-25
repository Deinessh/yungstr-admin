

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.data('pickAnyUniqueSelects', () => ({
    init() {
        this.$el.querySelectorAll('select').forEach((select) => {
            select.addEventListener('change', () => this.sync());
        });
        this.sync();
    },
    sync() {
        const selects = [...this.$el.querySelectorAll('select')];
        const seen = new Set();

        selects.forEach((select) => {
            if (select.value && seen.has(select.value)) {
                select.value = '';
            } else if (select.value) {
                seen.add(select.value);
            }
        });

        const selected = selects.map((select) => select.value).filter(Boolean);

        selects.forEach((select) => {
            const current = select.value;
            select.querySelectorAll('option').forEach((option) => {
                if (! option.value) {
                    return;
                }
                option.disabled = selected.includes(option.value) && option.value !== current;
            });
        });
    },
}));

Alpine.data('announcementTicker', () => ({
    active: 0,
    total: 1,
    timer: null,
    init() {
        this.total = parseInt(this.$el.dataset.itemCount || '1', 10);
        if (this.total > 1) {
            this.timer = setInterval(() => {
                this.active = (this.active + 1) % this.total;
            }, 4000);
        }
    },
    destroy() {
        clearInterval(this.timer);
    },
}));

Alpine.data('homeHeroSplit', () => ({
    active: 0,
    total: 1,
    links: ['/products'],
    timer: null,
    init() {
        this.total = parseInt(this.$el.dataset.slideCount || '1', 10);
        try {
            this.links = JSON.parse(this.$el.dataset.slideLinks || '["/products"]');
        } catch {
            this.links = ['/products'];
        }
        this.startAutoplay();
    },
    go(index) {
        this.active = index;
    },
    prev() {
        this.active = (this.active - 1 + this.total) % this.total;
    },
    next() {
        this.active = (this.active + 1 + this.total) % this.total;
    },
    startAutoplay() {
        if (this.total <= 1) {
            return;
        }
        this.timer = setInterval(() => this.next(), 7000);
    },
    stopAutoplay() {
        clearInterval(this.timer);
    },
}));

Alpine.data('shippingPincodeChecker', (config) => ({
    pincode: config.pincode || '',
    loading: false,
    resolved: config.initialQuote?.resolved || false,
    zoneName: config.initialQuote?.zone_name || '',
    message: config.initialQuote?.message || '',
    init() {
        if (this.resolved && this.message) {
            return;
        }
        if (/^\d{6}$/.test(this.pincode)) {
            this.check();
        }
    },
    async check() {
        if (!/^\d{6}$/.test(this.pincode)) {
            this.resolved = false;
            this.zoneName = '';
            this.message = 'Enter a valid 6-digit PIN code.';
            window.dispatchEvent(new CustomEvent('shipping-quote-updated', { detail: { quote: { resolved: false } } }));
            return;
        }

        this.loading = true;

        try {
            const response = await fetch(config.quoteUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': config.csrf,
                },
                body: JSON.stringify({ pincode: this.pincode }),
            });

            const data = await response.json();
            this.resolved = !!data.quote?.resolved;
            this.zoneName = data.quote?.zone_name || '';
            this.message = data.quote?.message || '';
            window.dispatchEvent(new CustomEvent('shipping-quote-updated', { detail: data }));
        } catch {
            this.resolved = false;
            this.message = 'Unable to check delivery right now. Please try again.';
        } finally {
            this.loading = false;
        }
    },
}));

Alpine.data('cartOrderSummary', (config) => ({
    subtotal: config.subtotal,
    shipping: config.initialShipping,
    total: config.initialTotal,
    resolved: config.quoteResolved,
    applyQuote(detail) {
        const quote = detail?.quote;

        if (!quote?.resolved) {
            this.resolved = false;
            this.shipping = null;
            this.total = this.subtotal;
            return;
        }

        this.resolved = true;
        this.shipping = quote.shipping_fee ?? 0;
        this.total = detail?.totals?.total ?? (this.subtotal + this.shipping);
    },
    shippingLabel() {
        if (!this.resolved) {
            return 'Enter PIN code';
        }

        return (this.shipping || 0) > 0
            ? `₹${Math.round(this.shipping).toLocaleString('en-IN')}`
            : 'FREE';
    },
    totalLabel() {
        return this.resolved
            ? `₹${Math.round(this.total).toLocaleString('en-IN')}`
            : `₹${Math.round(this.subtotal).toLocaleString('en-IN')}`;
    },
}));

Alpine.data('checkoutShipping', (config) => ({
    subtotal: config.subtotal,
    discount: config.discount,
    quote: config.quote || { resolved: false, shipping_fee: null, message: '' },
    shippingDisplay: config.initialShippingLabel || 'Enter PIN code',
    totalDisplay: `₹${Math.round(config.initialTotal || config.subtotal).toLocaleString('en-IN')}`,
    loading: false,
    lookupLoading: false,
    lookupRequestId: 0,
    init() {
        this.syncTotalsFromQuote();

        const pincode = document.getElementById('shipping_pincode')?.value || '';

        if (/^\d{6}$/.test(pincode) && !this.quote.resolved) {
            this.refreshQuote();
        }
    },
    syncTotalsFromQuote() {
        if (!this.quote?.resolved) {
            return;
        }

        const fee = Number(this.quote.shipping_fee) || 0;
        this.shippingDisplay = fee > 0
            ? `₹${Math.round(fee).toLocaleString('en-IN')}`
            : 'FREE';
        this.totalDisplay = `₹${Math.max(this.subtotal + fee - this.discount, 0).toLocaleString('en-IN')}`;
    },
    applyLocation(location) {
        if (!location) {
            return;
        }

        const cityEl = document.getElementById('shipping_city');
        const stateEl = document.getElementById('shipping_state');

        if (stateEl && location.state) {
            stateEl.value = location.state;
            stateEl.setAttribute('readonly', 'readonly');
        }

        if (cityEl && location.city) {
            cityEl.value = location.city;
            cityEl.setAttribute('readonly', 'readonly');
        }
    },
    async refreshQuote() {
        const pincode = document.getElementById('shipping_pincode')?.value?.trim() || '';

        if (!/^\d{6}$/.test(pincode)) {
            this.quote = {
                resolved: false,
                shipping_fee: null,
                message: 'Enter your 6-digit PIN — city and state will be filled automatically.',
            };
            this.shippingDisplay = 'Enter PIN code';
            this.totalDisplay = `₹${Math.max(this.subtotal - this.discount, 0).toLocaleString('en-IN')}`;

            return;
        }

        this.loading = true;
        this.lookupLoading = true;
        const requestId = ++this.lookupRequestId;

        try {
            const response = await fetch(config.quoteUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': config.csrf,
                },
                body: JSON.stringify({ pincode }),
            });
            const data = await response.json();

            if (requestId !== this.lookupRequestId) {
                return;
            }

            this.quote = data.quote || this.quote;
            this.syncTotalsFromQuote();
            this.applyLocation(data.location || data.address);

            if (!data.address?.state && !data.location?.state) {
                this.quote.message = 'Could not detect city/state for this PIN. Please enter them manually.';
            }
        } catch {
            this.quote.message = 'Unable to refresh delivery charges.';
        } finally {
            this.loading = false;
            this.lookupLoading = false;
        }
    },
}));

Alpine.data('productGallery', (config = {}) => ({
    active: 0,
    images: config.images || [],
    init() {
        this.$nextTick(() => {
            const track = this.$refs.mobileTrack;

            if (!track) {
                return;
            }

            track.addEventListener('scroll', () => this.syncFromScroll(), { passive: true });
        });
    },
    syncFromScroll() {
        const track = this.$refs.mobileTrack;

        if (!track || track.offsetWidth <= 0) {
            return;
        }

        const index = Math.round(track.scrollLeft / track.offsetWidth);
        this.active = Math.max(0, Math.min(index, this.images.length - 1));
    },
    goTo(index) {
        if (index < 0 || index >= this.images.length) {
            return;
        }

        this.active = index;

        const track = this.$refs.mobileTrack;

        if (track) {
            track.scrollTo({ left: index * track.offsetWidth, behavior: 'smooth' });
        }
    },
}));

Alpine.start();
