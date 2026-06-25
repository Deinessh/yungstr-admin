<?php

namespace Database\Seeders;

use App\Models\ShippingZone;
use Illuminate\Database\Seeder;

class ShippingZoneSeeder extends Seeder
{
    public function run(): void
    {
        $zones = [
            [
                'name' => 'Hyderabad & Secunderabad',
                'match_type' => 'city',
                'match_values' => "Hyderabad\nSecunderabad",
                'shipping_fee' => 29,
                'free_shipping_threshold' => 399,
                'is_active' => true,
                'is_default' => false,
            ],
            [
                'name' => 'Telangana',
                'match_type' => 'state',
                'match_values' => 'Telangana',
                'shipping_fee' => 49,
                'free_shipping_threshold' => 599,
                'is_active' => true,
                'is_default' => false,
            ],
            [
                'name' => 'Hyderabad Metro PIN',
                'match_type' => 'pincode',
                'match_values' => "500\n501\n502",
                'shipping_fee' => 29,
                'free_shipping_threshold' => 399,
                'is_active' => true,
                'is_default' => false,
            ],
            [
                'name' => 'Rest of India',
                'match_type' => 'default',
                'match_values' => null,
                'shipping_fee' => 79,
                'free_shipping_threshold' => 799,
                'is_active' => true,
                'is_default' => true,
            ],
        ];

        foreach ($zones as $zone) {
            ShippingZone::updateOrCreate(
                ['name' => $zone['name']],
                $zone
            );
        }

        // Migrate legacy pincode_prefix rows to pincode type
        ShippingZone::query()
            ->where('match_type', 'pincode_prefix')
            ->update(['match_type' => 'pincode']);
    }
}