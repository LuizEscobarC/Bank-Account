<?php

namespace App\Jobs;

use App\Models\AccountBankTransaction;
use App\Services\AccountBankTransactionService;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessScheduledTransactionsJob implements ShouldQueue
{
    use Queueable;

    /**
     * Job para verificar e processar transações agendadas na data atual:
     */
    public function handle(): void
    {

        $startOfDay = Carbon::now()->startOfDay();
        $endOfDay   = Carbon::now()->endOfDay();

        $transactions = AccountBankTransaction::whereBetween('scheduled_at', [$startOfDay, $endOfDay])->get();

        foreach ($transactions as $transaction) {
            ProcessIndividualTransactionsJob::dispatch($transaction, new AccountBankTransactionService());
        }
    }
}
