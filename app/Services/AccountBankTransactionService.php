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
            // separar para model
            AccountBank::find($data['sender_id'])->decrement('balance', $data['amount']);
            AccountBank::find($data['recipient_id'])->increment('balance', $data['amount']);
            $accountBankTransaction = AccountBankTransaction::create($data);
        });

        return $accountBankTransaction;
    }

    public function updateAccountsBalance(array $data, AccountBankTransaction $accountBankTransaction): bool
    {
        DB::transaction(function () use ($data) {
            // separar para model
            AccountBank::find($data['sender_id'])->decrement('balance', $data['amount']);
            AccountBank::find($data['recipient_id'])->increment('balance', $data['amount']);
        });
        // adicionar regra de agendamento completo
        // obs mudar migration
        $accountBankTransaction->update(['processed_at' => now()]);

        return true;
    }
}
