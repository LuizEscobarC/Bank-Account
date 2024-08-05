<?php

use App\Jobs\ProcessScheduledTransactionsJob;
use Illuminate\Support\Facades\Artisan;

Artisan::command('faz:transacoes', function () {
    ProcessScheduledTransactionsJob::dispatch();
})->purpose('Processa as transações agendadas')->everyMinute();
