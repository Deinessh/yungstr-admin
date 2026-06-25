<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('cashfree_order_id')->nullable()->after('razorpay_payment_id');
            $table->string('cashfree_payment_id')->nullable()->after('cashfree_order_id');
        });

        $defaults = [
            'razorpay_enabled' => '1',
            'cashfree_enabled' => '0',
            'cashfree_environment' => 'sandbox',
            'theme_primary' => '#355E3B',
            'theme_accent' => '#F26A2E',
            'theme_background' => '#F6EFE3',
            'theme_text' => '#3E2A1F',
            'theme_soft' => '#FCE4D6',
            'qr_redirect_url' => config('app.url'),
            'qr_scan_base_url' => rtrim((string) config('app.url'), '/'),
        ];

        foreach ($defaults as $key => $value) {
            if (DB::table('settings')->where('key', $key)->doesntExist()) {
                DB::table('settings')->insert([
                    'key' => $key,
                    'value' => $value,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['cashfree_order_id', 'cashfree_payment_id']);
        });
    }
};
