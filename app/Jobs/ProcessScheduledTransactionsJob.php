<?php

namespace App\Jobs;

use App\Enums\TransactionStatusEnum;
use App\Models\AccountBankTransaction;
use App\Services\AccountBankTransactionService;
use App\Services\Auth\AuthService;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Job para verificar e processar transações agendadas no dia atual.
 */
class ProcessScheduledTransactionsJob implements ShouldQueue
{
    use Queueable;

    /**
     * Cria uma nova instância do job.
     *
     * @param  \App\Services\AccountBankTransactionService  $transactionService
     * @param  \App\Services\Auth\AuthService  $authService
     */
    public function __construct(
        protected AccountBankTransactionService $transactionService,
        protected AuthService $authService
    ) {
    }

    /**
     * Manipula o job para verificar e processar transações agendadas no dia atual.
     *
     * @return void
     */
    public function handle(): void
    {
        // Define o início e o fim do dia atual
        $startOfDay = Carbon::now()->startOfDay();
        $endOfDay   = Carbon::now()->endOfDay();

        // Obtém transações agendadas para o dia atual que ainda não foram processadas
        $transactions = AccountBankTransaction::whereBetween('scheduled_at', [$startOfDay, $endOfDay])
            ->whereNull('processed_at')
            ->where('status', TransactionStatusEnum::Pending->value)
            ->get();

        // Adiciona na fila cada transação agendada
        foreach ($transactions as $transaction) {
            ProcessIndividualTransactionsJob::dispatch($transaction, $this->transactionService);
        }
    }
}
