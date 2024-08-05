<?php

namespace App\Jobs;

use App\Models\AccountBankTransaction;
use App\Services\AccountBankTransactionService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessScheduledTransactionsJob implements ShouldQueue
{
    use Queueable;

    /**
     * Job para verificar e processar transações agendadas na data atual:
     */
    public function handle(): void
    {

        Log::info('Verificando transações agendadas.');
        echo "Verificando transações agendadas!";

        $transactions = AccountBankTransaction::whereDate('scheduled_at', now()->toDateString())->get();

        foreach ($transactions as $transaction) {
            ProcessIndividualTransactionsJob::dispatch($transaction, new AccountBankTransactionService());
        }
    }
}
