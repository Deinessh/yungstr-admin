<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_best_seller')->default(false)->after('is_featured');
            $table->boolean('is_hot')->default(false)->after('is_best_seller');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->date('delivery_date')->nullable()->after('contact_number');
        });

        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('role')->nullable();
            $table->text('quote');
            $table->unsignedTinyInteger('rating')->default(5);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonials');

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('delivery_date');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['is_best_seller', 'is_hot']);
        });
    }
};
