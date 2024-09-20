<?php

// app/Console/Commands/EodUpdate.php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Helpers\Helpers;

class EodUpdate extends Command
{
    protected $signature = 'update:eod';

    protected $description = 'Perform end of day updates';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Running end-of-day update at: ' . Carbon::now('America/New_York')->toDateTimeString());
        Helpers::eodUpdate();
    }
}
