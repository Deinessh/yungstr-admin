<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use App\Services\CouponService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CouponController extends Controller
{
    public function __construct(
        private CartService $cart,
        private CouponService $coupons,
    ) {}

    public function apply(Request $request)
    {
        $request->validate(['coupon_code' => 'required|string|max:50']);

        $validation = $this->coupons->validate(
            $request->coupon_code,
            $this->cart->subtotal(),
            Auth::user()
        );

        if (! $validation['valid']) {
            return back()
                ->withInput()
                ->with('error', $validation['message']);
        }

        $form = session('checkout_form', []);
        $form['coupon_code'] = strtoupper(trim($request->coupon_code));
        session()->put('checkout_form', $form);

        return back()->with('success', $validation['message']);
    }

    public function remove()
    {
        $form = session('checkout_form', []);
        unset($form['coupon_code']);
        session()->put('checkout_form', $form);

        return back()->with('success', 'Coupon removed.');
    }
}
