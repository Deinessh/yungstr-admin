<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_name')->nullable()->after('shipping_address');
            $table->string('shipping_city')->nullable()->after('shipping_name');
            $table->string('shipping_state')->nullable()->after('shipping_city');
            $table->string('shipping_pincode', 10)->nullable()->after('shipping_state');
            $table->string('awb_code')->nullable()->after('shipping_pincode');
            $table->string('velocity_order_id')->nullable()->after('awb_code');
            $table->string('velocity_shipment_id')->nullable()->after('velocity_order_id');
            $table->string('carrier_name')->nullable()->after('velocity_shipment_id');
            $table->text('label_url')->nullable()->after('carrier_name');
            $table->string('tracking_url')->nullable()->after('label_url');
            $table->string('shipping_status')->default('pending')->after('tracking_url');
            $table->text('shipping_error')->nullable()->after('shipping_status');
            $table->string('invoice_number')->nullable()->after('shipping_error');
            $table->timestamp('invoiced_at')->nullable()->after('invoice_number');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->decimal('weight_kg', 8, 3)->nullable()->after('weight');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_name', 'shipping_city', 'shipping_state', 'shipping_pincode',
                'awb_code', 'velocity_order_id', 'velocity_shipment_id', 'carrier_name',
                'label_url', 'tracking_url', 'shipping_status', 'shipping_error',
                'invoice_number', 'invoiced_at',
            ]);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('weight_kg');
        });
    }
};
