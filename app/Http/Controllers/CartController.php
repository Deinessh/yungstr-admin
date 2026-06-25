<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use App\Services\PickAnyComboService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        private CartService $cart,
        private PickAnyComboService $pickAny,
    ) {}

    public function index()
    {
        $cart = $this->cart->getCart();
        $cart = $this->cart->enrichFromProducts($cart);
        session()->put('cart', $cart);

        $checkoutBlockers = $this->cart->checkoutBlockers($cart);

        $shippingLocation = $this->cart->shippingLocation();
        $totals = $this->cart->totals($cart, 0, $shippingLocation);
        $shippingQuote = $totals['shipping_quote'];

        if (! auth()->check() && count($cart) > 0) {
            session()->put('checkout.intended', route('checkout'));
        }

        return view('cart.index', [
            'cart' => $cart,
            'total' => $totals['subtotal'],
            'totals' => $totals,
            'shippingQuote' => $shippingQuote,
            'shippingLocation' => $shippingLocation ?? [],
            'checkoutBlockers' => $checkoutBlockers,
            'canCheckout' => count($cart) > 0 && count($checkoutBlockers) === 0,
        ]);
    }

    public function add(Request $request)
    {
        $product = Product::active()->findOrFail($request->product_id);
        $cart = session()->get('cart', []);
        $quantity = max(1, (int) ($request->quantity ?? 1));

        if ($error = $this->validateQuantity($product, $cart, $quantity)) {
            return redirect()->back()->with('error', $error);
        }

        $pickAnySets = null;
        if ($product->is_pick_any_combo) {
            $rawSets = $this->pickAny->extractRawSetsFromRequest($request->input('pick_any', []), $product->id, $quantity);

            if ($error = $this->pickAny->validateSets($rawSets, $quantity)) {
                return redirect()->back()->withInput()->with('error', $error);
            }

            $pickAnySets = $this->pickAny->buildSets($rawSets);
        }

        $existingQty = $cart[$product->id]['quantity'] ?? 0;
        $newQty = $existingQty + $quantity;

        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] = $newQty;
            $cart[$product->id]['stock'] = $product->stock;

            if ($pickAnySets !== null) {
                $existingSets = $cart[$product->id]['pick_any_sets'] ?? [];
                $cart[$product->id]['pick_any_sets'] = array_merge($existingSets, $pickAnySets);
            }
        } else {
            $cart[$product->id] = $this->cartItem($product, $quantity, $pickAnySets);
        }

        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Product added to cart successfully!');
    }

    public function buyNow(Request $request)
    {
        $product = Product::active()->findOrFail($request->product_id);
        $quantity = max(1, (int) ($request->quantity ?? 1));

        if ($error = $this->validateQuantity($product, [], $quantity)) {
            return redirect()->back()->with('error', $error);
        }

        $pickAnySets = null;
        if ($product->is_pick_any_combo) {
            $rawSets = $this->pickAny->extractRawSetsFromRequest($request->input('pick_any', []), $product->id, $quantity);

            if ($error = $this->pickAny->validateSets($rawSets, $quantity)) {
                return redirect()->back()->withInput()->with('error', $error);
            }

            $pickAnySets = $this->pickAny->buildSets($rawSets);
        }

        session()->put('cart', [
            $product->id => $this->cartItem($product, $quantity, $pickAnySets),
        ]);

        session()->put('checkout.intended', route('checkout'));

        if (auth()->check()) {
            return redirect()->route('checkout');
        }

        return redirect()->route('login');
    }

    public function update(Request $request)
    {
        if ($request->id && $request->has('quantity')) {
            $cart = session()->get('cart', []);

            if (! isset($cart[$request->id])) {
                return redirect()->route('cart.index')->with('error', 'Item not found in cart.');
            }

            $product = Product::find($request->id);
            $quantity = max(1, (int) $request->quantity);

            if ($product?->isComingSoon()) {
                return redirect()->route('cart.index')->with('error', 'This product is coming soon and cannot be ordered.');
            }

            if ($product && $product->isPurchasable() && $product->hasAvailableStock($quantity)) {
                if ($product->is_pick_any_combo) {
                    $existingSets = $cart[$request->id]['pick_any_sets'] ?? [];

                    if ($quantity > count($existingSets)) {
                        return redirect()->route('cart.index')->with(
                            'error',
                            'Please open the product page to choose products for additional Pick Any 3 combos.'
                        );
                    }

                    if ($quantity < count($existingSets)) {
                        $cart[$request->id]['pick_any_sets'] = array_slice($existingSets, 0, $quantity);
                    }
                }

                $cart[$request->id]['quantity'] = $quantity;
                $cart[$request->id]['stock'] = $product->stock;
                session()->put('cart', $cart);

                return redirect()->route('cart.index')->with('success', 'Cart updated successfully');
            }

            return redirect()->route('cart.index')->with('error', 'Invalid quantity for this product.');
        }

        return redirect()->route('cart.index');
    }

    public function remove(Request $request)
    {
        if ($request->id) {
            $cart = session()->get('cart');
            if (isset($cart[$request->id])) {
                unset($cart[$request->id]);
                session()->put('cart', $cart);
            }
            session()->flash('success', 'Product removed successfully');
        }

        return redirect()->route('cart.index');
    }

    protected function validateQuantity(Product $product, array $cart, int $quantity): ?string
    {
        if ($product->isComingSoon()) {
            return 'This product is coming soon and cannot be ordered yet.';
        }

        if (! $product->isPurchasable()) {
            return 'This product is out of stock.';
        }

        $existingQty = $cart[$product->id]['quantity'] ?? 0;
        $newQty = $existingQty + $quantity;

        if (! $product->hasAvailableStock($newQty)) {
            return 'Not enough stock available.';
        }

        return null;
    }

    protected function cartItem(Product $product, int $quantity, ?array $pickAnySets = null): array
    {
        $item = [
            'id' => $product->id,
            'name' => $product->name,
            'quantity' => $quantity,
            'price' => $product->price,
            'image' => $product->image,
            'stock' => $product->stock,
            'is_pick_any_combo' => $product->is_pick_any_combo,
        ];

        if ($pickAnySets !== null) {
            $item['pick_any_sets'] = $pickAnySets;
        }

        return $item;
    }
}
