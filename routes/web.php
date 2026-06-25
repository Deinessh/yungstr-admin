<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\ReferralController as AdminReferralController;
use App\Http\Controllers\Admin\ReferralSettingController as AdminReferralSettingController;
use App\Http\Controllers\Admin\TestimonialController as AdminTestimonialController;
use App\Http\Controllers\Admin\ContactController as AdminContactController;
use App\Http\Controllers\Admin\Auth\LoginController as AdminLoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::view('/about', 'about')->name('about');
Route::view('/faqs', 'pages.faqs')->name('pages.faqs');
Route::view('/shipping-policy', 'pages.shipping-policy')->name('pages.shipping-policy');
Route::view('/returns-refunds', 'pages.returns-refunds')->name('pages.returns-refunds');
Route::view('/terms-and-conditions', 'pages.terms')->name('pages.terms');
Route::view('/privacy-policy', 'pages.privacy-policy')->name('pages.privacy-policy');
Route::get('/contact', fn () => view('contact'))->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::redirect('/catalogue', '/products')->name('catalogue');
Route::redirect('/shop', '/products');
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/visit', \App\Http\Controllers\VisitController::class)->name('visit');
Route::post('/webhooks/cashfree', \App\Http\Controllers\CashfreeWebhookController::class)->name('webhooks.cashfree');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/buy-now', [CartController::class, 'buyNow'])->name('cart.buy-now');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');

Route::post('/shipping/quote', [\App\Http\Controllers\ShippingController::class, 'quote'])->name('shipping.quote');

Route::middleware('auth')->group(function () {
    Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
    Route::post('/order/place', [OrderController::class, 'placeOrder'])->name('order.place');
    Route::post('/order/verify-payment', [OrderController::class, 'verifyPayment'])->name('order.verify-payment');
    Route::post('/checkout/apply-coupon', [CouponController::class, 'apply'])->name('checkout.apply-coupon');
    Route::post('/checkout/remove-coupon', [CouponController::class, 'remove'])->name('checkout.remove-coupon');
    Route::post('/checkout/save-draft', [AccountController::class, 'saveCheckoutDraft'])->name('checkout.save-draft');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
    Route::get('/orders/{order}/resume-payment', [OrderController::class, 'resumePayment'])->name('orders.resume-payment');
    Route::get('/payment/cashfree/return/{order}', [OrderController::class, 'cashfreeReturn'])->name('payment.cashfree.return');
    Route::get('/account', [AccountController::class, 'dashboard'])->name('account.dashboard');
    Route::get('/account/resume-checkout', [AccountController::class, 'resumeCheckout'])->name('account.resume-checkout');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [AdminLoginController::class, 'create'])->name('login');
        Route::post('login', [AdminLoginController::class, 'store']);
    });

    Route::post('logout', [AdminLoginController::class, 'destroy'])->middleware('auth:admin')->name('logout');

    Route::middleware(['auth:admin', 'admin'])->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::resource('products', AdminProductController::class)->except(['show']);
        Route::delete('products/{product}/images/{image}', [AdminProductController::class, 'destroyImage'])->name('products.images.destroy');
        Route::resource('hero-slides', \App\Http\Controllers\Admin\HeroSlideController::class)->except(['show']);
        Route::resource('promo-banners', \App\Http\Controllers\Admin\PromoBannerController::class)->except(['show']);
        Route::resource('categories', AdminCategoryController::class)->except(['show']);
        Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
        Route::get('orders/{order}/invoice', [AdminOrderController::class, 'invoice'])->name('orders.invoice');
        Route::get('orders/{order}/invoice/print', [AdminOrderController::class, 'printInvoice'])->name('orders.invoice.print');
        Route::post('orders/{order}/retry-shipment', [AdminOrderController::class, 'retryShipment'])->name('orders.retry-shipment');
        Route::post('orders/{order}/sync-tracking', [AdminOrderController::class, 'syncTracking'])->name('orders.sync-tracking');
        Route::get('settings', [AdminSettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [AdminSettingController::class, 'update'])->name('settings.update');
        Route::post('settings/velocity-test', [AdminSettingController::class, 'testVelocity'])->name('settings.velocity-test');
        Route::resource('shipping-zones', \App\Http\Controllers\Admin\ShippingZoneController::class)->except(['show']);
        Route::resource('coupons', AdminCouponController::class)->except(['show']);
        Route::get('referrals', [AdminReferralController::class, 'index'])->name('referrals.index');
        Route::get('referral-settings', [AdminReferralSettingController::class, 'edit'])->name('referral-settings.edit');
        Route::put('referral-settings', [AdminReferralSettingController::class, 'update'])->name('referral-settings.update');
        Route::resource('testimonials', AdminTestimonialController::class)->except(['show']);
        Route::get('contacts', [AdminContactController::class, 'index'])->name('contacts.index');
        Route::get('contacts/{contact}', [AdminContactController::class, 'show'])->name('contacts.show');
        Route::delete('contacts/{contact}', [AdminContactController::class, 'destroy'])->name('contacts.destroy');
    });
});

require __DIR__.'/auth.php';
