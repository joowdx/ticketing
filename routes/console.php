<?php

use App\Console\Commands\AutoQueueRequestsCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', fn () => $this->comment(Inspiring::quote()))->purpose('Display an inspiring quote');

Schedule::command(AutoQueueRequestsCommand::class)->withoutOverlapping()->everyFifteenSeconds();
