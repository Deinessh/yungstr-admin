<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingZone;
use Illuminate\Http\Request;

class ShippingZoneController extends Controller
{
    public function index()
    {
        $zones = ShippingZone::query()
            ->orderByRaw("FIELD(match_type, 'city', 'state', 'pincode', 'pincode_prefix', 'default')")
            ->orderBy('name')
            ->get();

        return view('admin.shipping-zones.index', compact('zones'));
    }

    public function create()
    {
        return view('admin.shipping-zones.form', [
            'zone' => new ShippingZone([
                'shipping_fee' => 29,
                'free_shipping_threshold' => 399,
                'is_active' => true,
            ]),
            'matchTypes' => ShippingZone::matchTypes(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        if ($data['is_default']) {
            ShippingZone::query()->update(['is_default' => false]);
        }

        ShippingZone::create($data);

        return redirect()->route('admin.shipping-zones.index')->with('success', 'Shipping zone created.');
    }

    public function edit(ShippingZone $shippingZone)
    {
        return view('admin.shipping-zones.form', [
            'zone' => $shippingZone,
            'matchTypes' => ShippingZone::matchTypes(),
        ]);
    }

    public function update(Request $request, ShippingZone $shippingZone)
    {
        $data = $this->validated($request);

        if ($data['is_default']) {
            ShippingZone::query()->where('id', '!=', $shippingZone->id)->update(['is_default' => false]);
        }

        $shippingZone->update($data);

        return redirect()->route('admin.shipping-zones.index')->with('success', 'Shipping zone updated.');
    }

    public function destroy(ShippingZone $shippingZone)
    {
        if ($shippingZone->is_default) {
            return back()->with('error', 'The default fallback zone cannot be deleted. Edit it instead.');
        }

        $shippingZone->delete();

        return back()->with('success', 'Shipping zone deleted.');
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'match_type' => 'required|in:pincode,city,state,default',
            'match_values' => 'nullable|string|max:5000',
            'shipping_fee' => 'required|numeric|min:0',
            'free_shipping_threshold' => 'required|numeric|min:0',
            'is_active' => 'nullable|boolean',
            'is_default' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $data['is_default'] = $request->boolean('is_default');

        if ($data['match_type'] === 'default') {
            $data['match_values'] = null;
        } elseif (trim((string) ($data['match_values'] ?? '')) === '') {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'match_values' => 'Add at least one city, state name, or PIN prefix for this zone.',
            ]);
        }

        return $data;
    }
}
