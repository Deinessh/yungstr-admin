<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('mrp', 10, 2)->nullable()->after('price');
            $table->text('combo_includes')->nullable()->after('description');
            $table->boolean('is_pick_any_combo')->default(false)->after('is_hot');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->string('product_name')->nullable()->after('price');
            $table->string('product_sku')->nullable()->after('product_name');
            $table->string('product_weight')->nullable()->after('product_sku');
            $table->text('combo_includes')->nullable()->after('product_weight');
            $table->decimal('mrp', 10, 2)->nullable()->after('combo_includes');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->text('customer_notes')->nullable()->after('delivery_date');
            $table->timestamp('confirmation_sent_at')->nullable()->after('invoiced_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['customer_notes', 'confirmation_sent_at']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['product_name', 'product_sku', 'product_weight', 'combo_includes', 'mrp']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['mrp', 'combo_includes', 'is_pick_any_combo']);
        });
    }
};
