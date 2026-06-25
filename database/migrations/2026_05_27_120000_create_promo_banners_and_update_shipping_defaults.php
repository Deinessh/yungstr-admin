<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promo_banners', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('image');
            $table->string('link_url')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $now = now();
        foreach ([
            'shipping_fee' => '29',
            'free_shipping_threshold' => '399',
            'announcement_3' => 'Free Shipping on Orders Above ₹399',
        ] as $key => $value) {
            DB::table('settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $value, 'updated_at' => $now, 'created_at' => $now]
            );

            try {
                Cache::forget("setting.{$key}");
            } catch (\Throwable) {
                // Ignore cache errors during migration.
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('promo_banners');
    }
};
