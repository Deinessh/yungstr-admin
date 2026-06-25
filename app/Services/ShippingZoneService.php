<?php

namespace App\Services;

use App\Models\ShippingZone;
use Illuminate\Support\Collection;

class ShippingZoneService
{
    public function __construct(
        protected PincodeLookupService $pincodeLookup,
    ) {}

    /** @var array<string, string> */
    protected array $stateAliases = [
        'ap' => 'andhra pradesh',
        'andhra pradesh' => 'andhra pradesh',
        'andhrapradesh' => 'andhra pradesh',
        'a.p.' => 'andhra pradesh',
        'ts' => 'telangana',
        'tg' => 'telangana',
        'telangana' => 'telangana',
        'telengana' => 'telangana',
        'ka' => 'karnataka',
        'karnataka' => 'karnataka',
        'tn' => 'tamil nadu',
        'tamil nadu' => 'tamil nadu',
        'tamilnadu' => 'tamil nadu',
        'mh' => 'maharashtra',
        'maharashtra' => 'maharashtra',
        'delhi' => 'delhi',
        'new delhi' => 'delhi',
        'west bengal' => 'west bengal',
        'uttar pradesh' => 'uttar pradesh',
        'up' => 'uttar pradesh',
        'rajasthan' => 'rajasthan',
        'gujarat' => 'gujarat',
        'kerala' => 'kerala',
        'punjab' => 'punjab',
        'haryana' => 'haryana',
        'bihar' => 'bihar',
        'odisha' => 'odisha',
        'orissa' => 'odisha',
        'assam' => 'assam',
        'madhya pradesh' => 'madhya pradesh',
        'mp' => 'madhya pradesh',
        'chhattisgarh' => 'chhattisgarh',
        'jharkhand' => 'jharkhand',
        'uttarakhand' => 'uttarakhand',
        'himachal pradesh' => 'himachal pradesh',
        'hp' => 'himachal pradesh',
        'jammu and kashmir' => 'jammu and kashmir',
        'jammu & kashmir' => 'jammu and kashmir',
    ];

    /**
     * Resolve zone: city first, then state, then pincode, then fallback.
     */
    public function resolve(?string $pincode, ?string $city = null, ?string $state = null): ?ShippingZone
    {
        $pincode = $this->normalizePincode($pincode);
        $city = $this->normalizeText($city);
        $state = $this->normalizeState($state);

        if (! $state && $pincode) {
            $state = $this->inferStateFromPincode($pincode);
        }

        if (! $city && $pincode) {
            $city = $this->inferCityFromPincode($pincode);
        }

        $zones = ShippingZone::query()
            ->where('is_active', true)
            ->where('is_default', false)
            ->orderBy('id')
            ->get();

        if ($city && ($zone = $this->firstMatching($zones->where('match_type', 'city'), $pincode, $city, $state))) {
            return $zone;
        }

        if ($state && ($zone = $this->firstMatching($zones->where('match_type', 'state'), $pincode, $city, $state))) {
            return $zone;
        }

        if ($pincode && ($zone = $this->firstMatching(
            $zones->whereIn('match_type', ['pincode', 'pincode_prefix']),
            $pincode,
            $city,
            $state
        ))) {
            return $zone;
        }

        return ShippingZone::query()
            ->where('is_active', true)
            ->where('is_default', true)
            ->orderBy('id')
            ->first();
    }

    public function quote(float $subtotal, ?string $pincode, ?string $city = null, ?string $state = null): array
    {
        $pincode = $this->normalizePincode($pincode);

        if (! $pincode) {
            return [
                'resolved' => false,
                'zone' => null,
                'zone_id' => null,
                'zone_name' => null,
                'shipping_fee' => null,
                'standard_shipping_fee' => null,
                'free_shipping_threshold' => null,
                'free_shipping_remaining' => null,
                'qualifies_for_free_shipping' => false,
                'message' => 'Enter your 6-digit PIN code to see delivery charges and free-shipping eligibility.',
            ];
        }

        $zone = $this->resolve($pincode, $city, $state);

        if (! $zone) {
            $fallbackFee = app(SettingService::class)->shippingFee();
            $fallbackThreshold = app(SettingService::class)->freeShippingThreshold();
            $shippingFee = $subtotal >= $fallbackThreshold ? 0.0 : $fallbackFee;

            return [
                'resolved' => true,
                'zone' => null,
                'zone_id' => null,
                'zone_name' => 'Standard',
                'shipping_fee' => round($shippingFee, 2),
                'standard_shipping_fee' => $fallbackFee,
                'free_shipping_threshold' => $fallbackThreshold,
                'free_shipping_remaining' => max(0, round($fallbackThreshold - $subtotal, 2)),
                'qualifies_for_free_shipping' => $shippingFee <= 0,
                'message' => $shippingFee <= 0
                    ? 'Free delivery on orders above ₹'.number_format($fallbackThreshold, 0).'.'
                    : 'Delivery: ₹'.number_format($fallbackFee, 0).' (free above ₹'.number_format($fallbackThreshold, 0).').',
            ];
        }

        $threshold = (float) $zone->free_shipping_threshold;
        $standardFee = (float) $zone->shipping_fee;
        $shippingFee = $subtotal >= $threshold ? 0.0 : $standardFee;
        $remaining = max(0, round($threshold - $subtotal, 2));

        $message = $shippingFee <= 0
            ? "Free delivery for {$zone->name} on orders above ₹".number_format($threshold, 0).'.'
            : "Delivery to {$zone->name}: ₹".number_format($standardFee, 0)." (free above ₹".number_format($threshold, 0).').';

        if ($shippingFee > 0 && $remaining > 0) {
            $message .= ' Add ₹'.number_format($remaining, 0).' more for free delivery.';
        }

        return [
            'resolved' => true,
            'zone' => $zone,
            'zone_id' => $zone->id,
            'zone_name' => $zone->name,
            'shipping_fee' => round($shippingFee, 2),
            'standard_shipping_fee' => $standardFee,
            'free_shipping_threshold' => $threshold,
            'free_shipping_remaining' => $remaining,
            'qualifies_for_free_shipping' => $shippingFee <= 0,
            'message' => $message,
        ];
    }

    protected function firstMatching(Collection $zones, ?string $pincode, ?string $city, ?string $state): ?ShippingZone
    {
        foreach ($zones as $zone) {
            if ($this->matches($zone, $pincode, $city, $state)) {
                return $zone;
            }
        }

        return null;
    }

    protected function matches(ShippingZone $zone, ?string $pincode, ?string $city, ?string $state): bool
    {
        $rawValues = $zone->matchValuesList();

        return match ($zone->match_type) {
            'city' => $city && $this->matchesName($city, $rawValues),
            'state' => ($state && $this->matchesName($state, $rawValues))
                || ($pincode && $this->matchesPincodeValues($pincode, $rawValues)),
            'pincode', 'pincode_prefix' => $pincode && $this->matchesPincodeValues($pincode, $rawValues),
            default => false,
        };
    }

    protected function matchesName(string $name, array $rawValues): bool
    {
        $normalized = $this->normalizeState($name) ?? $this->normalizeText($name);

        foreach ($rawValues as $value) {
            $candidate = $this->normalizeState($value) ?? $this->normalizeText($value);

            if ($candidate && $normalized === $candidate) {
                return true;
            }
        }

        return false;
    }

    protected function matchesPincodeValues(string $pincode, array $values): bool
    {
        foreach ($values as $value) {
            $digits = preg_replace('/\D/', '', $value);

            if ($digits === '') {
                continue;
            }

            if (strlen($digits) === 6 && $pincode === $digits) {
                return true;
            }

            if (strlen($digits) >= 2 && strlen($digits) <= 6 && str_starts_with($pincode, $digits)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Location from India Post PIN directory (online, cached).
     *
     * @return array{pincode: string, state: string, district: string, city: string|null, offices: int}|null
     */
    public function locationFromPincode(?string $pincode): ?array
    {
        return $this->pincodeLookup->lookup($pincode);
    }

    /**
     * Infer state: India Post API first, then prefixes on state shipping zones.
     */
    protected function inferStateFromPincode(string $pincode): ?string
    {
        $fromApi = $this->pincodeLookup->stateForPincode($pincode);

        if ($fromApi) {
            return $this->normalizeState($fromApi);
        }

        $stateZones = ShippingZone::query()
            ->where('is_active', true)
            ->where('match_type', 'state')
            ->get();

        foreach ($stateZones as $zone) {
            if ($this->matchesPincodeValues($pincode, $zone->matchValuesList())) {
                foreach ($zone->matchValuesList() as $value) {
                    $normalized = $this->normalizeState($value);

                    if ($normalized && strlen(preg_replace('/\D/', '', $value)) < 2) {
                        return $normalized;
                    }
                }

                return $this->normalizeState($zone->name) ?? $this->normalizeText($zone->name);
            }
        }

        return null;
    }

    protected function inferCityFromPincode(string $pincode): ?string
    {
        $location = $this->pincodeLookup->lookup($pincode);

        if (! $location || empty($location['city'])) {
            return null;
        }

        return $this->normalizeText($location['city']);
    }

    protected function normalizePincode(?string $pincode): ?string
    {
        if (! $pincode) {
            return null;
        }

        $digits = preg_replace('/\D/', '', $pincode);

        return strlen($digits) === 6 ? $digits : null;
    }

    protected function normalizeText(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        $value = strtolower(trim($value));
        $value = preg_replace('/\s+/', ' ', $value);

        return $value;
    }

    protected function normalizeState(?string $value): ?string
    {
        $value = $this->normalizeText($value);

        if (! $value) {
            return null;
        }

        $collapsed = str_replace(' ', '', $value);

        return $this->stateAliases[$value] ?? $this->stateAliases[$collapsed] ?? $value;
    }
}
