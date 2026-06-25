<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $steps = [
            ['icon' => 'fas fa-blender', 'title' => 'Add Water', 'desc' => 'Add water to the mix.'],
            ['icon' => 'fas fa-clock', 'title' => 'Mix for 30 Seconds', 'desc' => 'Mix well to make a smooth batter.'],
            ['icon' => 'fas fa-pan-frying', 'title' => 'Cook Fresh Dosa & Idly', 'desc' => 'Cook and enjoy soft & healthy dosa or idly.'],
        ];

        $encoded = json_encode($steps);

        DB::table('settings')
            ->where('key', 'home_how_it_works')
            ->update(['value' => $encoded]);

        if (DB::table('settings')->where('key', 'home_how_it_works')->doesntExist()) {
            DB::table('settings')->insert([
                'key' => 'home_how_it_works',
                'value' => $encoded,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        $steps = json_encode([
            ['icon' => 'fas fa-droplet', 'title' => 'Add Water', 'desc' => 'Add water to the mix.'],
            ['icon' => 'fas fa-clock', 'title' => 'Mix for 30 Seconds', 'desc' => 'Mix well to make a smooth batter.'],
            ['icon' => 'fas fa-fire-burner', 'title' => 'Cook Fresh Dosa & Idly', 'desc' => 'Cook and enjoy soft & healthy dosa or idly.'],
        ]);

        DB::table('settings')
            ->where('key', 'home_how_it_works')
            ->update(['value' => $steps]);
    }
};
