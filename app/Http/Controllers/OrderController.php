<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Services\CartService;
use App\Services\CheckoutAddressService;
use App\Services\CouponService;
use App\Services\InvoiceService;
use App\Services\OrderService;
use App\Services\PickAnyComboService;
use App\Services\CashfreeService;
use App\Services\RazorpayService;
use App\Services\SettingService;
use App\Services\PincodeLookupService;
use App\Services\ShippingZoneService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct(
        private CartService $cart,
        private CouponService $coupons,
        private OrderService $orders,
        private RazorpayService $razorpay,
        private CashfreeService $cashfree,
        private SettingService $settings,
        private InvoiceService $invoices,
        private PickAnyComboService $pickAny,
        private CheckoutAddressService $checkoutAddress,
        private ShippingZoneService $shippingZones,
        private PincodeLookupService $pincodeLookup,
    ) {}

    public function checkout()
    {
        $cart = $this->cart->getCart();

        if (count($cart) === 0) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        if ($this->cart->hasCheckoutBlockers($cart)) {
            return redirect()->route('cart.index')->with(
                'error',
                'Your cart contains products that cannot be checked out. Please remove coming soon or unavailable items.'
            );
        }

        $cart = $this->cart->enrichFromProducts($cart);

        $sessionForm = session('checkout_form', []);
        $saved = $sessionForm;
        if (auth()->check()) {
            $saved = $this->checkoutAddress->resolve(auth()->user(), $saved);
        }
        if (! empty($sessionForm['coupon_code'])) {
            $saved['coupon_code'] = $sessionForm['coupon_code'];
        }

        $sessionLocation = $this->cart->shippingLocation() ?? [];
        $location = [
            'pincode' => old('shipping_pincode', $saved['shipping_pincode'] ?? $sessionLocation['pincode'] ?? null),
            'city' => old('shipping_city', $saved['shipping_city'] ?? $sessionLocation['city'] ?? null),
            'state' => old('shipping_state', $saved['shipping_state'] ?? $sessionLocation['state'] ?? null),
        ];

        if (! empty($location['pincode'])) {
            $applied = $this->pincodeLookup->applyToAddress($location['pincode'], [
                'city' => $location['city'] ?? null,
                'state' => $location['state'] ?? null,
            ]);
            $location['state'] = $applied['state'];
            $location['city'] = $applied['city'];
            $saved['shipping_state'] = $applied['state'];
            $saved['shipping_city'] = $applied['city'];
            $this->cart->setShippingLocation($location);
        }

        $subtotal = $this->cart->subtotal($cart);
        $couponState = $this->resolveCheckoutCoupon(auth()->user(), $subtotal, $saved['coupon_code'] ?? null);
        $totals = $this->cart->totals(
            $cart,
            $couponState['discount'],
            ! empty($location['pincode']) ? $location : null
        );
        $selectableProducts = $this->pickAny->selectableProducts();

        return view('checkout.index', [
            'cart' => $cart,
            'totals' => $totals,
            'selectableProducts' => $selectableProducts,
            'codEnabled' => $this->settings->codEnabled(),
            'razorpayEnabled' => $this->razorpay->isEnabled(),
            'cashfreeEnabled' => $this->cashfree->isEnabled(),
            'saved' => $saved,
            'shippingLocation' => $location,
            'shippingQuote' => $totals['shipping_quote'],
            'appliedCouponCode' => $couponState['code'],
            'couponError' => $couponState['error'],
        ]);
    }

    public function placeOrder(Request $request)
    {
        $rules = [
            'shipping_name' => 'nullable|string|max:255',
            'shipping_address' => 'required|string|max:2000',
            'shipping_city' => 'required|string|max:100',
            'shipping_state' => 'required|string|max:100',
            'shipping_pincode' => 'required|string|regex:/^\d{6}$/',
            'contact_number' => 'required|string|max:15',
            'payment_method' => 'required|in:razorpay,cashfree,cod',
            'customer_notes' => 'nullable|string|max:2000',
        ];

        $request->validate($rules);

        $applied = $this->pincodeLookup->applyToAddress($request->shipping_pincode, [
            'city' => $request->shipping_city,
            'state' => $request->shipping_state,
        ]);

        $request->merge([
            'shipping_city' => $applied['city'] ?? $request->shipping_city,
            'shipping_state' => $applied['state'] ?? $request->shipping_state,
        ]);

        $cart = $this->cart->getCart();

        if (count($cart) === 0) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        if ($blockers = $this->cart->checkoutBlockers($cart)) {
            return redirect()->route('cart.index')->with(
                'error',
                reset($blockers) ?: 'Your cart contains products that cannot be checked out.'
            );
        }

        if ($error = $this->syncPickAnySelections($request, $cart)) {
            return back()->withInput()->with('error', $error);
        }

        if ($request->payment_method === 'cod' && ! $this->settings->codEnabled()) {
            return back()->with('error', 'Cash on Delivery is currently unavailable.');
        }

        if ($request->payment_method === 'razorpay' && ! $this->razorpay->isEnabled()) {
            return back()->with('error', 'Razorpay is not available right now.');
        }

        if ($request->payment_method === 'cashfree' && ! $this->cashfree->isEnabled()) {
            return back()->with('error', 'Cashfree is not available right now.');
        }

        $user = Auth::user();

        $checkoutForm = session('checkout_form', []);
        $couponCode = $request->input('coupon_code') ?: ($checkoutForm['coupon_code'] ?? null);

        try {
            $order = $this->orders->createFromCheckout($user, array_merge($request->only([
                'shipping_name', 'shipping_address', 'shipping_city', 'shipping_state', 'shipping_pincode',
                'contact_number', 'payment_method', 'customer_notes',
            ]), [
                'coupon_code' => $couponCode,
            ]));
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        $this->cart->saveDraft($user->id, array_merge($request->only([
            'shipping_name', 'shipping_address', 'shipping_city', 'shipping_state', 'shipping_pincode',
            'contact_number', 'payment_method', 'customer_notes',
        ]), [
            'coupon_code' => $couponCode,
        ]));

        session()->forget('checkout_form');

        if ($request->payment_method === 'cod') {
            return redirect()->route('orders.index')->with('success', 'Order placed successfully! Pay on delivery.');
        }

        if ($request->payment_method === 'cashfree') {
            try {
                $cashfreeOrder = $this->cashfree->createOrder($order, $user);
            } catch (\Throwable $e) {
                $this->orders->markPaymentFailed($order);

                return back()->with('error', 'Unable to initiate Cashfree payment. Please try again.');
            }

            $order->update([
                'cashfree_order_id' => $cashfreeOrder['order_id'],
            ]);

            return view('checkout.cashfree-payment', [
                'order' => $order,
                'paymentSessionId' => $cashfreeOrder['payment_session_id'],
                'cashfreeMode' => $this->cashfree->mode(),
            ]);
        }

        try {
            $razorpayOrder = $this->razorpay->createOrder(
                $order->total_amount,
                'order_'.$order->id,
                ['order_id' => $order->id, 'user_id' => $user->id]
            );
        } catch (\Throwable $e) {
            $this->orders->markPaymentFailed($order);

            return back()->with('error', 'Unable to initiate payment. Please try again.');
        }

        $order->update(['razorpay_order_id' => $razorpayOrder['id']]);

        return view('checkout.payment', [
            'order' => $order,
            'razorpayKey' => $this->settings->razorpayKeyId(),
            'razorpayOrderId' => $razorpayOrder['id'],
            'amount' => (int) round($order->total_amount * 100),
        ]);
    }

    public function verifyPayment(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer',
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id' => 'required|string',
            'razorpay_signature' => 'required|string',
        ]);

        $order = Order::where('id', $request->order_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if (! $this->razorpay->verifySignature(
            $request->razorpay_order_id,
            $request->razorpay_payment_id,
            $request->razorpay_signature
        )) {
            $this->orders->markPaymentFailed($order);

            return redirect()->route('checkout')->with('error', 'Payment verification failed.');
        }

        $order->update([
            'razorpay_payment_id' => $request->razorpay_payment_id,
            'payment_status' => 'paid',
        ]);

        $this->orders->finalizeOrder($order, $order->coupon);

        return redirect()->route('orders.index')->with('success', 'Payment successful! Your order has been placed.');
    }

    public function cashfreeReturn(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($order->payment_status === 'paid') {
            return redirect()->route('orders.index')->with('success', 'Payment successful! Your order has been placed.');
        }

        if (! $order->cashfree_order_id) {
            return redirect()->route('orders.index')->with('error', 'Payment could not be verified.');
        }

        $remote = $this->cashfree->fetchOrder($order->cashfree_order_id);

        if ($remote && $this->cashfree->isPaid($remote)) {
            $order->update([
                'cashfree_payment_id' => $remote['cf_payment_id'] ?? null,
                'payment_status' => 'paid',
            ]);
            $this->orders->finalizeOrder($order->fresh(), $order->coupon);

            return redirect()->route('orders.index')->with('success', 'Payment successful! Your order has been placed.');
        }

        return redirect()->route('orders.index')->with('error', 'Payment was not completed. You can resume payment from your orders.');
    }

    public function resumePayment(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($order->payment_status === 'paid' || $order->status === 'cancelled') {
            return redirect()->route('account.dashboard')->with('error', 'This order cannot be resumed.');
        }

        if ($order->payment_method === 'cashfree') {
            if (! $this->cashfree->isEnabled()) {
                return redirect()->route('orders.index')->with('error', 'Cashfree is not configured.');
            }

            try {
                if ($order->cashfree_order_id) {
                    $remote = $this->cashfree->fetchOrder($order->cashfree_order_id);
                    if ($remote && isset($remote['payment_session_id'])) {
                        return view('checkout.cashfree-payment', [
                            'order' => $order,
                            'paymentSessionId' => $remote['payment_session_id'],
                            'cashfreeMode' => $this->cashfree->mode(),
                        ]);
                    }
                }

                $cashfreeOrder = $this->cashfree->createOrder($order, Auth::user());
                $order->update(['cashfree_order_id' => $cashfreeOrder['order_id']]);

                return view('checkout.cashfree-payment', [
                    'order' => $order,
                    'paymentSessionId' => $cashfreeOrder['payment_session_id'],
                    'cashfreeMode' => $this->cashfree->mode(),
                ]);
            } catch (\Throwable) {
                return redirect()->route('orders.index')->with('error', 'Unable to resume Cashfree payment.');
            }
        }

        if ($order->payment_method !== 'razorpay') {
            return redirect()->route('account.dashboard')->with('error', 'This order cannot be resumed.');
        }

        if (! $this->razorpay->isEnabled()) {
            return redirect()->route('orders.index')->with('error', 'Razorpay is not configured.');
        }

        if (! $order->razorpay_order_id) {
            try {
                $razorpayOrder = $this->razorpay->createOrder(
                    $order->total_amount,
                    'order_'.$order->id,
                    ['order_id' => $order->id, 'user_id' => Auth::id()]
                );
                $order->update(['razorpay_order_id' => $razorpayOrder['id']]);
            } catch (\Throwable) {
                return redirect()->route('orders.index')->with('error', 'Unable to resume payment.');
            }
        }

        return view('checkout.payment', [
            'order' => $order,
            'razorpayKey' => $this->settings->razorpayKeyId(),
            'razorpayOrderId' => $order->razorpay_order_id,
            'amount' => (int) round($order->total_amount * 100),
        ]);
    }

    public function index()
    {
        $orders = Order::with('items.product')
            ->where('user_id', Auth::id())
            ->visibleToCustomer()
            ->latest()
            ->get();

        return view('orders.index', compact('orders'));
    }

    public function invoice(Order $order)
    {
        if ($order->user_id !== Auth::id() || ! $order->isPaid() || ! $order->canAccessInvoice()) {
            abort(403);
        }

        $order = $this->invoices->ensureInvoice($order->fresh(['items.product', 'user']));

        return $this->invoices->pdf($order)->download($order->invoice_number.'.pdf');
    }

    protected function syncPickAnySelections(Request $request, array &$cart): ?string
    {
        $pickAnyInput = $request->input('pick_any', []);

        foreach ($cart as $productId => &$item) {
            $product = Product::find($productId);

            if (! $product?->is_pick_any_combo) {
                continue;
            }

            $quantity = (int) ($item['quantity'] ?? 1);
            $rawSets = $this->pickAny->extractRawSetsFromRequest($pickAnyInput, (int) $productId, $quantity);

            if ($error = $this->pickAny->validateSets($rawSets, $quantity)) {
                return $error;
            }

            $item['pick_any_sets'] = $this->pickAny->buildSets($rawSets);
            $item['is_pick_any_combo'] = true;
        }
        unset($item);

        session()->put('cart', $cart);

        return null;
    }

    /**
     * @return array{code: ?string, discount: float, error: ?string}
     */
    protected function resolveCheckoutCoupon($user, float $subtotal, ?string $code): array
    {
        $code = $code ? strtoupper(trim($code)) : null;

        if (! $code || ! $user) {
            return ['code' => null, 'discount' => 0.0, 'error' => null];
        }

        $validation = $this->coupons->validate($code, $subtotal, $user);

        if (! $validation['valid']) {
            $form = session('checkout_form', []);
            unset($form['coupon_code']);
            session()->put('checkout_form', $form);

            return ['code' => null, 'discount' => 0.0, 'error' => $validation['message']];
        }

        return [
            'code' => $code,
            'discount' => (float) $validation['discount'],
            'error' => null,
        ];
    }
}
