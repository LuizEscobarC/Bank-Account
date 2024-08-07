<?php

namespace App\Jobs;

use App\Models\{AccountBankTransaction};
use App\Services\AccountBankTransactionService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\{
    InteractsWithQueue,
    SerializesModels
};

class ProcessIndividualTransactionsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    /** Ambas as injeções estão públicas para o teste conseguir acessar */
    public function __construct(public readonly AccountBankTransaction $accountBankTransaction, public readonly AccountBankTransactionService $accountBankTransactionService)
    {
    }
    /**
     * JOB - faz a transferencia individual de cada solicitação agendada
     */
    public function handle()
    {
        $this->accountBankTransactionService->updateAccountsBalance([
            'amount'       => $this->accountBankTransaction->amount,
            'sender_id'    => $this->accountBankTransaction->sender_id,
            'recipient_id' => $this->accountBankTransaction->recipient_id,
        ], $this->accountBankTransaction);
    }
}
