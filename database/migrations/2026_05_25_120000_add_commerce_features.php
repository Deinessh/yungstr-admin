<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('password');
            $table->string('phone')->nullable()->after('email');
            $table->string('referral_code', 20)->nullable()->unique()->after('phone');
            $table->boolean('referral_unlocked')->default(false)->after('referral_code');
            $table->unsignedInteger('successful_referrals_count')->default(0)->after('referral_unlocked');
            $table->foreignId('referred_by_user_id')->nullable()->after('successful_referrals_count')->constrained('users')->nullOnDelete();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->string('sku')->nullable()->after('slug');
            $table->string('benefit_tag')->nullable()->after('description');
            $table->boolean('is_featured')->default(false)->after('image');
            $table->unsignedInteger('featured_sort')->default(0)->after('is_featured');
            $table->boolean('is_active')->default(true)->after('featured_sort');
            $table->string('weight')->nullable()->after('is_active');
            $table->json('key_benefits')->nullable()->after('weight');
            $table->json('nutrition_info')->nullable()->after('key_benefits');
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('type')->default('fixed');
            $table->decimal('value', 10, 2);
            $table->decimal('min_order_amount', 10, 2)->default(0);
            $table->unsignedInteger('max_uses')->nullable();
            $table->unsignedInteger('used_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_referral_reward')->default(false);
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('subtotal', 10, 2)->default(0)->after('total_amount');
            $table->decimal('shipping_fee', 10, 2)->default(0)->after('subtotal');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('shipping_fee');
            $table->string('payment_method')->nullable()->after('discount_amount');
            $table->string('payment_status')->default('pending')->after('payment_method');
            $table->string('razorpay_order_id')->nullable()->after('payment_status');
            $table->string('razorpay_payment_id')->nullable()->after('razorpay_order_id');
            $table->foreignId('coupon_id')->nullable()->after('razorpay_payment_id')->constrained()->nullOnDelete();
            $table->string('coupon_code')->nullable()->after('coupon_id');
            $table->string('referral_code_used')->nullable()->after('coupon_code');
            $table->json('cart_snapshot')->nullable()->after('referral_code_used');
        });

        Schema::create('coupon_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('referred_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('pending');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('contact_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('subject');
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });

        Schema::create('checkout_drafts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->json('cart_data');
            $table->text('shipping_address')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('coupon_code')->nullable();
            $table->string('referral_code')->nullable();
            $table->string('payment_method')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checkout_drafts');
        Schema::dropIfExists('contact_submissions');
        Schema::dropIfExists('referrals');
        Schema::dropIfExists('coupon_usages');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('settings');

        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('coupon_id');
            $table->dropColumn([
                'subtotal', 'shipping_fee', 'discount_amount', 'payment_method',
                'payment_status', 'razorpay_order_id', 'razorpay_payment_id',
                'coupon_code', 'referral_code_used', 'cart_snapshot',
            ]);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'sku', 'benefit_tag', 'is_featured', 'featured_sort', 'is_active',
                'weight', 'key_benefits', 'nutrition_info',
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('referred_by_user_id');
            $table->dropColumn([
                'is_admin', 'phone', 'referral_code', 'referral_unlocked',
                'successful_referrals_count',
            ]);
        });
    }
};
