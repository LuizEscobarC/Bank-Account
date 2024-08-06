<?php

use App\Jobs\ProcessScheduledTransactionsJob;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new ProcessScheduledTransactionsJob())->everyMinute();
