<?php

namespace App\Services;

use App\Enums\TransactionStatusEnum;
use App\Models\{AccountBank, AccountBankTransaction};
use App\Services\Auth\AuthService;
use App\Services\Data\ExternalAuthRequestData;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AccountBankTransactionService
{
    /**
     * Cria uma transação bancária e realiza a transferência se necessário.
     *
     * @param  array  $data  Dados da transação.
     * @return AccountBankTransaction|null  A transação criada ou null se for um agendamento futuro.
     * @throws ValidationException  Se a transação falhar devido a saldo insuficiente.
     */
    public function create(array $data): ?AccountBankTransaction
    {
        $transaction = AccountBankTransaction::create($data);

        if (!empty($data['scheduled_at'])) {
            return $transaction;
        }

        $externalAuthAuthorized = $this->processExternalAuth($data);

        DB::transaction(function () use ($data, $transaction, $externalAuthAuthorized) {
            $sender    = AccountBank::findOrFail($data['sender_id']);
            $recipient = AccountBank::findOrFail($data['recipient_id']);

            if ($sender->balance < $data['amount']) {
                throw new \Exception('Saldo insuficiente para a transação.');
            }

            if ($externalAuthAuthorized) {
                $this->transferAmount($sender, $recipient, $data['amount']);
                $transaction->authorizeTransaction();
            }
        });

        return $transaction;
    }

    /**
     * Atualiza o saldo das contas após a transação ser processada.
     *
     * @param  array  $data  Dados da transação.
     * @param  AccountBankTransaction  $transaction  A transação a ser atualizada.
     * @return bool  Indica se a atualização foi bem-sucedida.
     */
    public function updateAccountsBalance(array $data, AccountBankTransaction $transaction): bool
    {
        $sender = AccountBank::findOrFail($transaction->sender_id);

        if ($sender->balance < $transaction->amount) {
            $transaction->update(['status' => TransactionStatusEnum::InsufficientBalance->value]);

            return false;
        }

        $externalAuthAuthorized = $this->processExternalAuth($data);

        if (!$externalAuthAuthorized) {
            $transaction->markNotAuthorized();

            return true;
        }

        DB::transaction(function () use ($data, $transaction) {
            $sender    = AccountBank::findOrFail($data['sender_id']);
            $recipient = AccountBank::findOrFail($data['recipient_id']);

            $this->transferAmount($sender, $recipient, $data['amount']);

            $transaction->update([
                'processed_at' => now(),
                'status'       => TransactionStatusEnum::Completed->value,
            ]);
        });

        return true;
    }

    /**
     * Processa a autenticação externa.
     *
     * @param  array  $data  Dados da autenticação.
     * @return bool  Indica se a autenticação foi autorizada.
     */
    private function processExternalAuth(array $data): bool
    {
        $requestData = new ExternalAuthRequestData(
            sender: $data['sender_id'],
            receiver: $data['recipient_id'],
            amount: $data['amount']
        );

        $response = resolve(AuthService::class)->processAccount($requestData);

        return $response->authorized ?? false;
    }

    /**
     * Realiza a transferência de valor entre duas contas.
     *
     * @param  AccountBank  $sender  Conta de onde o valor será debitado.
     * @param  AccountBank  $recipient  Conta para onde o valor será creditado.
     * @param  float  $amount  Valor a ser transferido.
     */
    private function transferAmount(AccountBank $sender, AccountBank $recipient, float $amount): void
    {
        $sender->decrementBalance($amount);
        $recipient->incrementBalance($amount);
    }
}
