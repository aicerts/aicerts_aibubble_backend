<?php

namespace App\Console\Commands;

use App\Helpers\Helpers;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Helpers\IntraDayHelper;
use Illuminate\Support\Facades\Log;

class IntraDayUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:intraday';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perform intraday updates every 5 minutes';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (Helpers::isIntraDay()) {
            $this->info('Running intraday update at: ' . Carbon::now('America/New_York')->toDateTimeString());
            Helpers::intradayUpdate();
        } else {
            $this->info('Current time is outside the scheduled time range.');
        }
    }
}
