@php
use App\Models\ContactSubmission;
use App\Models\Order;

$pendingOrdersCount = Order::visibleToCustomer()
    ->whereIn('status', ['pending', 'confirmed'])
    ->whereNotIn('payment_status', ['failed'])
    ->count();

$sections = [
    'Overview' => [
        ['route' => 'admin.dashboard', 'icon' => 'fa-chart-pie', 'label' => 'Dashboard'],
    ],
    'Sales' => [
        ['route' => 'admin.orders.index', 'icon' => 'fa-shopping-bag', 'label' => 'Orders', 'badge' => $pendingOrdersCount ?: null],
    ],
    'Catalogue' => [
        ['route' => 'admin.products.index', 'icon' => 'fa-box', 'label' => 'Products'],
        ['route' => 'admin.categories.index', 'icon' => 'fa-tags', 'label' => 'Categories'],
        ['route' => 'admin.hero-slides.index', 'icon' => 'fa-images', 'label' => 'Hero Slides'],
        ['route' => 'admin.promo-banners.index', 'icon' => 'fa-bullhorn', 'label' => 'Promo Banners'],
    ],
    'Customers' => [
        ['route' => 'admin.contacts.index', 'icon' => 'fa-envelope', 'label' => 'Contact Forms', 'badge' => ContactSubmission::where('is_read', false)->count() ?: null],
        ['route' => 'admin.testimonials.index', 'icon' => 'fa-star', 'label' => 'Reviews'],
    ],
    'Marketing' => [
        ['route' => 'admin.coupons.index', 'icon' => 'fa-ticket-alt', 'label' => 'Coupons'],
        ['route' => 'admin.shipping-zones.index', 'icon' => 'fa-truck', 'label' => 'Shipping Zones'],
        ['route' => 'admin.referrals.index', 'icon' => 'fa-user-friends', 'label' => 'Referrals'],
        ['route' => 'admin.referral-settings.edit', 'icon' => 'fa-gift', 'label' => 'Referral Settings'],
    ],
    'System' => [
        ['route' => 'admin.settings.edit', 'icon' => 'fa-cog', 'label' => 'Settings'],
    ],
];
@endphp

@foreach($sections as $section => $items)
<div class="mb-1">
    <div class="admin-nav-section">{{ $section }}</div>
    @foreach($items as $item)
    @php
        $routePattern = str_replace('.index', '.*', $item['route']);
        $active = request()->routeIs($routePattern) || request()->routeIs($item['route']) || request()->routeIs(str_replace('.edit', '.*', $item['route']));
    @endphp
    <a href="{{ route($item['route']) }}"
       @if(!empty($mobile))
       @click="mobileNav = false"
       @endif
       class="admin-nav-link {{ $active ? 'admin-nav-link-active' : '' }}">
        <i class="fas {{ $item['icon'] }} w-4 shrink-0 text-center"></i>
        <span class="flex-1">{{ $item['label'] }}</span>
        @if(!empty($item['badge']))
            <span class="min-w-[1.25rem] h-5 px-1.5 rounded-full bg-admin-orange text-white text-[10px] font-bold flex items-center justify-center">{{ $item['badge'] > 99 ? '99+' : $item['badge'] }}</span>
        @endif
    </a>
    @endforeach
</div>
@endforeach
