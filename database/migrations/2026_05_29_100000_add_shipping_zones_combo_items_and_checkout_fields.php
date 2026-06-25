<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('match_type'); // pincode, pincode_prefix, city, state, default
            $table->text('match_values')->nullable();
            $table->decimal('shipping_fee', 10, 2)->default(0);
            $table->decimal('free_shipping_threshold', 10, 2)->default(399);
            $table->unsignedInteger('priority')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        Schema::create('product_combo_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('combo_product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('included_product_id')->constrained('products')->cascadeOnDelete();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->unique(['combo_product_id', 'included_product_id']);
            $table->timestamps();
        });

        Schema::table('checkout_drafts', function (Blueprint $table) {
            $table->string('shipping_name')->nullable()->after('shipping_address');
            $table->string('shipping_city')->nullable()->after('shipping_name');
            $table->string('shipping_state')->nullable()->after('shipping_city');
            $table->string('shipping_pincode', 10)->nullable()->after('shipping_state');
            $table->date('delivery_date')->nullable()->after('shipping_pincode');
            $table->text('customer_notes')->nullable()->after('delivery_date');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('shipping_zone_id')->nullable()->after('shipping_fee')->constrained('shipping_zones')->nullOnDelete();
            $table->string('shipping_zone_name')->nullable()->after('shipping_zone_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('shipping_zone_id');
            $table->dropColumn('shipping_zone_name');
        });

        Schema::table('checkout_drafts', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_name', 'shipping_city', 'shipping_state',
                'shipping_pincode', 'delivery_date', 'customer_notes',
            ]);
        });

        Schema::dropIfExists('product_combo_items');
        Schema::dropIfExists('shipping_zones');
    }
};
