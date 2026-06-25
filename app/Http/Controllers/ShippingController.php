<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use App\Services\PincodeLookupService;
use App\Services\ShippingZoneService;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    public function __construct(
        private CartService $cart,
        private ShippingZoneService $zones,
        private PincodeLookupService $pincodeLookup,
    ) {}

    public function quote(Request $request)
    {
        $data = $request->validate([
            'pincode' => 'required|string|regex:/^\d{6}$/',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
        ]);

        $location = $this->pincodeLookup->lookup($data['pincode']);
        $applied = $this->pincodeLookup->applyToAddress($data['pincode'], [
            'city' => $data['city'] ?? null,
            'state' => $data['state'] ?? null,
        ]);

        $data['state'] = $applied['state'];
        $data['city'] = $applied['city'];

        $address = [
            'pincode' => $data['pincode'],
            'city' => $applied['city'],
            'state' => $applied['state'],
            'district' => $location['district'] ?? null,
        ];

        if ($location) {
            $location['state'] = $applied['state'];
            $location['city'] = $applied['city'];
        }

        $this->cart->setShippingLocation($data);

        $quote = $this->cart->shippingQuote(null, $data);
        $totals = $this->cart->totals(null, 0, $data);

        return response()->json([
            'location' => $location,
            'address' => $address,
            'quote' => $quote,
            'totals' => [
                'subtotal' => $totals['subtotal'],
                'shipping' => $quote['resolved'] ? $quote['shipping_fee'] : null,
                'total' => $quote['resolved'] ? $totals['total'] : $totals['subtotal'],
            ],
        ]);
    }
}
