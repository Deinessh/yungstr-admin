<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearPincodeLookupCache extends Command
{
    protected $signature = 'pincode:clear-cache';

    protected $description = 'Clear cached PIN code lookups (use after API/offline data updates)';

    public function handle(): int
    {
        Cache::forget('pincode.offline.ranges.index');
        Cache::forget('pincode.prefix.state.map');
        $this->info('Cleared PIN offline index and prefix map. Run php artisan cache:clear to drop per-PIN API cache if needed.');

        return self::SUCCESS;
    }
}
