<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('shipping_zones')
            ->where('match_type', 'pincode_prefix')
            ->update(['match_type' => 'pincode']);

        Schema::table('shipping_zones', function (Blueprint $table) {
            $table->dropColumn('priority');
        });
    }

    public function down(): void
    {
        Schema::table('shipping_zones', function (Blueprint $table) {
            $table->unsignedInteger('priority')->default(0)->after('free_shipping_threshold');
        });
    }
};
