<?php

namespace App\Console\Commands;

use App\Jobs\AutoQueueRequests;
use Illuminate\Console\Command;

class MarkStaleRequestsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:mark-stale-requests';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark stale requests as expired.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        AutoQueueRequests::dispatchSync();
    }
}
