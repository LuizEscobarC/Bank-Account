<?php

use App\Jobs\ProcessScheduledTransactionsJob;
use Illuminate\Support\Facades\Schedule;

// CRON que processa transferências do dia anterior as 5am
Schedule::job(new ProcessScheduledTransactionsJob())->dailyAt('05:00');
