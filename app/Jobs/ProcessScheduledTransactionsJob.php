<?php

namespace App\Jobs;

use App\Enums\TransactionStatusEnum;
use App\Models\AccountBankTransaction;
use App\Services\AccountBankTransactionService;
use App\Services\Auth\AuthService;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessScheduledTransactionsJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected AccountBankTransactionService $transactionService,
        protected AuthService $authService
    ) {
    }

    /**
     * Job para verificar e processar transações agendadas no dia atual(hoje).
     */
    public function handle(): void
    {
        // Define o início e o fim do dia atual
        $startOfDay = Carbon::now()->startOfDay();
        $endOfDay   = Carbon::now()->endOfDay();

        $transactions = AccountBankTransaction::whereBetween('scheduled_at', [$startOfDay, $endOfDay])
            ->whereNull('processed_at')
            ->where('status', TransactionStatusEnum::Pending->value)
            ->get();

        // Adiciona na fila cada transação agendada
        foreach ($transactions as $transaction) {
            ProcessIndividualTransactionsJob::dispatch($transaction, new AccountBankTransactionService($this->authService));
        }
    }
}
