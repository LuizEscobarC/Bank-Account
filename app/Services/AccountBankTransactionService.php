<?php

namespace App\Services;

use App\Enums\TransactionStatusEnum;
use App\Models\{
    AccountBank,
    AccountBankTransaction
};
use App\Services\Auth\AuthService;
use App\Services\Data\ExternalAuthRequestData;
use Illuminate\Support\Facades\DB;

class AccountBankTransactionService
{
    public function __construct(protected AuthService $authService)
    {
    }
    /**
     * Tranfere um valor de uma conta para outra.
     *
     * @param array $data
     * @return AccountBankTransaction
     */
    public function create(array $data): ?AccountBankTransaction
    {
        // é agendamento futuro
        $accountBankTransaction = AccountBankTransaction::create($data);

        if (!empty($data['scheduled_at'])) {
            return $accountBankTransaction;
        }

        $responseEsternalAuth = $this->processExternalAuth($data);

        DB::transaction(function () use ($data, &$accountBankTransaction, $responseEsternalAuth) {
            // Encontra as contas e realiza a transação
            $sender    = AccountBank::findOrFail($data['sender_id']);
            $recipient = AccountBank::findOrFail($data['recipient_id']);

            // Valida se o saldo é suficiente
            if ($sender->balance < $data['amount']) {
                throw new \Exception('Saldo insuficiente para a transação.');
            }

            // Atualiza os saldos
            if ($responseEsternalAuth) {
                $this->transferAmount($sender, $recipient, $data['amount']);
                $accountBankTransaction->authorizeTransaction();
            }
        });

        return $accountBankTransaction;
    }

    /**
     * Atualiza o saldo das contas após a transação ser processada pela fila.
     *
     * @param array $data
     * @param AccountBankTransaction $accountBankTransaction
     * @return bool
     */
    public function updateAccountsBalance(array $data, AccountBankTransaction $accountBankTransaction): bool
    {
        $sender  = AccountBank::find($accountBankTransaction->sender_id);
        $balance = $sender->balance;

        // atualiza o status como saldo insuficiente
        if ($balance < $accountBankTransaction->amount) {
            $accountBankTransaction->update(['status' => TransactionStatusEnum::InsufficientBalance->value]);

            return false;
        }

        $responseExternalAuth = $this->processExternalAuth($data);

        if (!$responseExternalAuth) {
            $accountBankTransaction->markNotAuthorized();
        }

        DB::transaction(function () use ($data, $accountBankTransaction) {
            $sender    = AccountBank::findOrFail($data['sender_id']);
            $recipient = AccountBank::findOrFail($data['recipient_id']);

            // Atualiza o saldo das contas envolvidas na transação
            $this->transferAmount($sender, $recipient, $data['amount']);

            $accountBankTransaction->update([
                'processed_at' => now(),
                'status'       => TransactionStatusEnum::Completed->value,
            ]);

        });

        return true;
    }

    /**
     * Processa a autenticação externa.
     *
     * @param array $data
     * @return bool
     */
    private function processExternalAuth(array $data): bool
    {
        // Cria um objeto ExternalAuthRequestData com os dados fornecidos
        $dtoExternalRequestData = new ExternalAuthRequestData(
            sender: $data['sender_id'],
            receiver: $data['recipient_id'],
            amount: $data['amount']
        );

        // Processa a autenticação e verifica se foi autorizada
        $response = $this->authService->processAccount($dtoExternalRequestData);

        // Verifica se a resposta possui a propriedade 'authorized'
        // Certifique-se de que o método processAccount retorna um objeto adequado
        return $response->authorized ?? false;
    }

    /**
     * Realiza a transferência de valor entre duas contas.
     *
     * @param AccountBank $sender
     * @param AccountBank $recipient
     * @param float $amount
     */
    private function transferAmount(AccountBank $sender, AccountBank $recipient, float $amount): void
    {
        $sender->decrementBalance($amount);
        $recipient->incrementBalance($amount);
    }
}
