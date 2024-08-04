<?php

namespace App\Services;

use App\Models\{AccountBank, AccountBankTransaction};
use Illuminate\Support\Facades\DB;

class AccountBankTransactionService
{
    /**
     * Tranfere um valor de uma conta para outra.
     *
     * @param array $data
     * @return AccountBankTransaction
     */
    public function create(array $data): ?AccountBankTransaction
    {
        $accountBankTransaction = null;
        DB::transaction(function () use ($data, &$accountBankTransaction) {
            AccountBank::find($data['sender_id'])->decrement('balance', $data['amount']);
            AccountBank::find($data['recipient_id'])->increment('balance', $data['amount']);
            $accountBankTransaction = AccountBankTransaction::create($data);
        });

        return $accountBankTransaction;
    }
}
