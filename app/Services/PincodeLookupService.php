<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PincodeLookupService
{
    /** @var list<array{start: int, end: int, state: string, district: string}>|null */
    protected static ?array $offlineRanges = null;

    /**
     * Resolve city, district, and state for a 6-digit PIN (API + offline fallback, cached).
     *
     * @return array{pincode: string, state: string, district: string, city: string|null, offices: int, source?: string}|null
     */
    public function lookup(?string $pincode): ?array
    {
        $pincode = $this->normalizePincode($pincode);

        if (! $pincode) {
            return null;
        }

        $cacheKey = 'pincode.lookup.'.$pincode;

        if (Cache::has($cacheKey)) {
            $cached = Cache::get($cacheKey);

            return is_array($cached) ? $cached : null;
        }

        $result = $this->fetchFromApi($pincode)
            ?? $this->lookupFromOfflineRanges($pincode);

        if ($result !== null) {
            Cache::put(
                $cacheKey,
                $result,
                now()->addDays(max(1, (int) config('pincode.cache_days', 30)))
            );
        }

        return $result;
    }

    public function stateForPincode(?string $pincode): ?string
    {
        return $this->lookup($pincode)['state'] ?? null;
    }

    /**
     * Fill city and state from PIN data (used at checkout).
     *
     * @param  array{city?: string|null, state?: string|null}  $fields
     * @return array{city: string|null, state: string|null}
     */
    public function applyToAddress(string $pincode, array $fields = []): array
    {
        $lookup = $this->lookup($pincode);

        if (! $lookup) {
            return [
                'city' => $fields['city'] ?? null,
                'state' => $fields['state'] ?? null,
            ];
        }

        return [
            'city' => $lookup['city'] ?? $lookup['district'] ?? ($fields['city'] ?? null),
            'state' => $lookup['state'],
        ];
    }

    /**
     * @return array{pincode: string, state: string, district: string, city: string|null, offices: int, source?: string}|null
     */
    protected function fetchFromApi(string $pincode): ?array
    {
        $url = str_replace('{pincode}', $pincode, config('pincode.api_url'));

        try {
            $request = Http::timeout((int) config('pincode.timeout_seconds', 12))
                ->retry(3, 400)
                ->withHeaders(['User-Agent' => 'S7MilletCo/1.0 (+https://s7.onesample.online)'])
                ->acceptJson();

            if (! config('pincode.verify_ssl', true)) {
                $request = $request->withoutVerifying();
            }

            $response = $request->get($url);
        } catch (\Throwable $e) {
            Log::warning('Pincode API request failed', [
                'pincode' => $pincode,
                'message' => $e->getMessage(),
            ]);

            return null;
        }

        if (! $response->successful()) {
            return null;
        }

        $payload = $response->json();

        if (! is_array($payload) || ($payload[0]['Status'] ?? '') !== 'Success') {
            return null;
        }

        $offices = $payload[0]['PostOffice'] ?? [];

        if (! is_array($offices) || $offices === []) {
            return null;
        }

        $primary = $offices[0];
        $state = $this->normalizeLocationName($primary['State'] ?? null);
        $district = $this->normalizeLocationName($primary['District'] ?? null);

        if (! $state) {
            return null;
        }

        $city = $this->pickCityName($primary) ?? $district;

        return [
            'pincode' => $pincode,
            'state' => $state,
            'district' => $district ?? '',
            'city' => $city,
            'offices' => count($offices),
            'source' => 'api',
        ];
    }

    /**
     * Offline India Post PIN ranges (all states) when the live API is unavailable.
     *
     * @return array{pincode: string, state: string, district: string, city: string|null, offices: int, source: string}|null
     */
    protected function lookupFromOfflineRanges(string $pincode): ?array
    {
        $numeric = (int) $pincode;
        $best = null;
        $bestSpan = PHP_INT_MAX;

        foreach ($this->offlineRanges() as $range) {
            if ($numeric < $range['start'] || $numeric > $range['end']) {
                continue;
            }

            $span = $range['end'] - $range['start'];

            if ($span >= $bestSpan) {
                continue;
            }

            $state = $this->normalizeLocationName($range['state']);
            $district = $this->normalizeLocationName($range['district']);

            if (! $state) {
                continue;
            }

            $bestSpan = $span;
            $best = [
                'pincode' => $pincode,
                'state' => $state,
                'district' => $district ?? '',
                'city' => $district ?? $state,
                'offices' => 0,
                'source' => 'offline',
            ];
        }

        return $best ?? $this->lookupFromPinPrefix($pincode);
    }

    /**
     * Coarse state guess from PIN prefix when range data has gaps (e.g. 110001).
     */
    protected function lookupFromPinPrefix(string $pincode): ?array
    {
        $prefixMap = $this->pinPrefixStateMap();
        $prefix = substr($pincode, 0, 3);

        if (! isset($prefixMap[$prefix])) {
            return null;
        }

        $state = $this->normalizeLocationName($prefixMap[$prefix]);

        if (! $state) {
            return null;
        }

        return [
            'pincode' => $pincode,
            'state' => $state,
            'district' => '',
            'city' => $state,
            'offices' => 0,
            'source' => 'prefix',
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function pinPrefixStateMap(): array
    {
        return Cache::rememberForever('pincode.prefix.state.map', function () {
            $map = [];
            $maxSpan = 25000;

            foreach ($this->offlineRanges() as $range) {
                $span = $range['end'] - $range['start'];

                if ($span > $maxSpan) {
                    continue;
                }

                $prefix = substr((string) $range['start'], 0, 3);
                $state = strtoupper(trim($range['state']));

                if ($prefix === '' || $state === '') {
                    continue;
                }

                if (! isset($map[$prefix]) || $span < ($map[$prefix]['span'] ?? PHP_INT_MAX)) {
                    $map[$prefix] = ['state' => $state, 'span' => $span];
                }
            }

            $flat = [];

            foreach ($map as $prefix => $entry) {
                $flat[$prefix] = $entry['state'];
            }

            return $flat;
        });
    }

    /**
     * @return list<array{start: int, end: int, state: string, district: string}>
     */
    protected function offlineRanges(): array
    {
        if (static::$offlineRanges !== null) {
            return static::$offlineRanges;
        }

        static::$offlineRanges = Cache::rememberForever('pincode.offline.ranges.index', function () {
            $path = storage_path('app/data/india-pincode-ranges.json');

            if (! is_file($path)) {
                return [];
            }

            $data = json_decode((string) file_get_contents($path), true);

            if (! is_array($data)) {
                return [];
            }

            $flat = [];

            foreach ($data['states'] ?? [] as $stateBlock) {
                foreach ($stateBlock['districts'] ?? [] as $district) {
                    $start = (int) ($district['pincodeStart'] ?? 0);
                    $end = (int) ($district['pincodeEnd'] ?? 0);

                    if ($start < 100000 || $end < $start) {
                        continue;
                    }

                    $flat[] = [
                        'start' => $start,
                        'end' => $end,
                        'state' => (string) ($district['stateName'] ?? $stateBlock['stateName'] ?? ''),
                        'district' => (string) ($district['districtName'] ?? ''),
                    ];
                }
            }

            return $flat;
        });

        return static::$offlineRanges;
    }

    protected function pickCityName(array $office): ?string
    {
        foreach (['Division', 'District', 'Block'] as $key) {
            $name = $this->normalizeLocationName($office[$key] ?? null);

            if ($name) {
                return preg_replace('/\s+City$/i', '', $name);
            }
        }

        return null;
    }

    protected function normalizePincode(?string $pincode): ?string
    {
        if (! $pincode) {
            return null;
        }

        $digits = preg_replace('/\D/', '', $pincode);

        return strlen($digits) === 6 ? $digits : null;
    }

    protected function normalizeLocationName(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        $value = strtolower(trim($value));
        $value = preg_replace('/\s+/', ' ', $value);

        return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
    }
}
