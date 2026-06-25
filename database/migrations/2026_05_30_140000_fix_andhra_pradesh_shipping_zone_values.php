<?php

use App\Models\ShippingZone;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $ap = ShippingZone::query()
            ->where('match_type', 'state')
            ->where(function ($query) {
                $query->where('name', 'like', '%Andhra%')
                    ->orWhere('name', 'like', '%andhra%');
            })
            ->first();

        if ($ap && ! trim((string) $ap->match_values)) {
            $ap->update([
                'match_values' => "Andhra Pradesh\nAP\n533\n534\n531\n532\n535\n516\n517\n518",
            ]);
        }
    }

    public function down(): void
    {
        // no-op
    }
};
