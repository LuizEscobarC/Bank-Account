<?php

namespace App\Services;

use App\Enums\TransactionStatusEnum;
use App\Models\{
    AccountBank,
    AccountBankTransaction
};
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
        return DB::transaction(function () use ($data) {
            // Encontra as contas e realiza a transação
            $sender    = AccountBank::findOrFail($data['sender_id']);
            $recipient = AccountBank::findOrFail($data['recipient_id']);

            // Atualiza o saldo das contas envolvidas na transação
            $sender->decrementBalance($data['amount']);
            $recipient->incrementBalance($data['amount']);

            // Cria e retorna a transação
            return AccountBankTransaction::create($data);
        });
    }

    /**
     * Atualiza o saldo das contas após a transação ser processada.
     *
     * @param array $data
     * @param AccountBankTransaction $accountBankTransaction
     * @return bool
     */
    public function updateAccountsBalance(array $data, AccountBankTransaction $accountBankTransaction): bool
    {
        // validar a autenticação
        if (false) {
            throw new AuthenticatedException('Esta ação não foi autenticada');
        }

        $balance = AccountBank::find($accountBankTransaction->sender_id)->balance;

        // atualiza o status como saldo insuficiente
        if ($balance < $accountBankTransaction->amount) {
            $accountBankTransaction->update(['status' => TransactionStatusEnum::InsufficientBalance]);

            return false;
        }

        DB::transaction(function () use ($data, $accountBankTransaction) {
            $sender    = AccountBank::findOrFail($data['sender_id']);
            $recipient = AccountBank::findOrFail($data['recipient_id']);

            // Atualiza o saldo das contas envolvidas na transação
            $sender->decrementBalance($data['amount']);
            $recipient->incrementBalance($data['amount']);

            $accountBankTransaction->update(['processed_at' => now()]);
            $accountBankTransaction->update(['status' => TransactionStatusEnum::Completed]);

        });

        return true;
    }
}
