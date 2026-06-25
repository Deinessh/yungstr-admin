<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\CartService;
use App\Services\ReferralService;
use App\Services\SettingService;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function __construct(
        private CartService $cart,
        private ReferralService $referrals,
        private SettingService $settings,
    ) {}

    public function dashboard()
    {
        $user = Auth::user()->load(['referralsGiven.referredUser']);
        $orders = Order::with('items.product')
            ->where('user_id', $user->id)
            ->visibleToCustomer()
            ->latest()
            ->get();

        $pendingPayments = Order::where('user_id', $user->id)
            ->where('payment_method', 'razorpay')
            ->where('payment_status', 'pending')
            ->where('status', '!=', 'cancelled')
            ->latest()
            ->get();

        $draft = $user->checkoutDraft;
        $referralsRequired = $this->settings->referralsRequiredToUnlock();
        $personalCoupon = $this->referrals->personalCoupon($user);
        $referrals = $user->referralsGiven()->with('referredUser')->latest()->get();

        return view('account.dashboard', compact(
            'user', 'orders', 'pendingPayments', 'draft', 'referralsRequired', 'personalCoupon', 'referrals'
        ));
    }

    public function resumeCheckout()
    {
        $user = Auth::user();
        $draft = $user->checkoutDraft;

        if (! $draft || empty($draft->cart_data)) {
            return redirect()->route('cart.index')->with('error', 'No saved checkout found.');
        }

        session()->put('cart', $draft->cart_data);
        session()->put('checkout_form', [
            'shipping_name' => $draft->shipping_name,
            'shipping_address' => $draft->shipping_address,
            'shipping_city' => $draft->shipping_city,
            'shipping_state' => $draft->shipping_state,
            'shipping_pincode' => $draft->shipping_pincode,
            'contact_number' => $draft->contact_number,
            'delivery_date' => $draft->delivery_date?->format('Y-m-d'),
            'customer_notes' => $draft->customer_notes,
            'coupon_code' => $draft->coupon_code,
            'referral_code' => $draft->referral_code,
            'payment_method' => $draft->payment_method,
        ]);

        if ($draft->shipping_pincode) {
            $this->cart->setShippingLocation([
                'pincode' => $draft->shipping_pincode,
                'city' => $draft->shipping_city,
                'state' => $draft->shipping_state,
            ]);
        }

        if ($this->cart->hasCheckoutBlockers(session('cart', []))) {
            return redirect()->route('cart.index')->with(
                'error',
                'Your saved cart includes coming soon or unavailable products. Please remove them before checkout.'
            );
        }

        return redirect()->route('checkout');
    }

    public function saveCheckoutDraft(\Illuminate\Http\Request $request)
    {
        $this->cart->saveDraft(Auth::id(), $request->only([
            'shipping_name', 'shipping_address', 'shipping_city', 'shipping_state', 'shipping_pincode',
            'contact_number', 'delivery_date', 'customer_notes', 'coupon_code', 'referral_code', 'payment_method',
        ]));

        if ($request->filled('shipping_pincode')) {
            $this->cart->setShippingLocation($request->only('pincode', 'city', 'state') + [
                'pincode' => $request->shipping_pincode,
                'city' => $request->shipping_city,
                'state' => $request->shipping_state,
            ]);
        }

        return back()->with('success', 'Checkout saved. You can resume anytime from your account.');
    }
}
