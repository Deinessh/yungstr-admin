<div class="fixed bottom-6 right-6 z-50">
    <a
        href="https://wa.me/{{ $storeSettings['contact_whatsapp'] ?? '15551234567' }}?text=Hi%20{{ urlencode($storeSettings['brand_name'] ?? 'Yungstr Club') }}%2C%20I%20would%20like%20to%20know%20more."
        target="_blank"
        rel="noopener noreferrer"
        class="w-12 h-12 rounded-full bg-whatsapp text-white shadow-lg flex items-center justify-center hover:scale-105 transition"
        title="WhatsApp us"
        aria-label="WhatsApp us"
    ><i class="fab fa-whatsapp text-xl"></i></a>
</div>
