<?php

namespace App\Jobs;

use App\Models\{AccountBank, AccountBankTransaction};
use App\Services\AccountBankTransactionService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};

class ProcessIndividualTransactionsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(private readonly AccountBankTransaction $accountBankTransaction, private readonly AccountBankTransactionService $accountBankTransactionService)
    {
    }
    /**
     * JOB - faz a transferencia individual de cada solicitação agendada
     */
    public function handle()
    {
        // usar resolve()
        // separar para um service - CHAMAR O SERVICE
        $balance = AccountBank::find($this->accountBankTransaction->sender_id)->balance;

        if ($balance > $this->accountBankTransaction->amount) {
            $this->accountBankTransactionService->create([
                'amount'       => $this->accountBankTransaction->amount,
                'sender_id'    => $this->accountBankTransaction->sender_id,
                'recipient_id' => $this->accountBankTransaction->recipient_id,
            ]);
        }

        $this->accountBankTransaction->delete();
    }
}
