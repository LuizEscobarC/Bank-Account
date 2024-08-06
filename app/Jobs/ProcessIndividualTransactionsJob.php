<?php

namespace App\Jobs;

use App\Models\AccountBankTransaction;
use App\Services\AccountBankTransactionService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\{
    InteractsWithQueue,
    SerializesModels
};

/**
 * Job para processar transações bancárias individuais.
 */
class ProcessIndividualTransactionsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Cria uma nova instância do job.
     *
     * @param  \App\Models\AccountBankTransaction  $accountBankTransaction
     * @param  \App\Services\AccountBankTransactionService  $accountBankTransactionService
     */
    public function __construct(
        public readonly AccountBankTransaction $accountBankTransaction,
        public readonly AccountBankTransactionService $accountBankTransactionService
    ) {
    }

    /**
     * Manipula o job para processar a transferência individual.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->accountBankTransactionService->updateAccountsBalance([
            'amount'       => $this->accountBankTransaction->amount,
            'sender_id'    => $this->accountBankTransaction->sender_id,
            'recipient_id' => $this->accountBankTransaction->recipient_id,
        ], $this->accountBankTransaction);
    }
}
