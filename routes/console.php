<?php

use App\Jobs\ProcessScheduledTransactionsJob;
use Illuminate\Support\Facades\Schedule;

// CRON que processa transferências do dia anterior as 5am
Schedule::job(resolve(ProcessScheduledTransactionsJob::class))->dailyAt('05:00');
