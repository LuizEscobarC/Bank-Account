<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAccountBankTransactionRequest;
use App\Http\Resources\AccountBankTransactionResource;
use App\Services\AccountBankTransactionService;

/**
 * @group Account Bank Transactions
 *
 * APIs para gerenciar transações entre contas bancárias.
 */
class AccountBankTransactionController extends Controller
{
    /**
     * Realiza uma transferência de valores entre contas.
     *
     * @bodyParam amount numeric required O valor a ser transferido. Example: 1000.00
     * @bodyParam sender_id uuid required O ID da conta de origem. Example: 1a2b3c4d-5e6f-7g8h-9i0j-k1l2m3n4o5p6
     * @bodyParam recipient_id uuid required O ID da conta de destino. Example: 6p5o4n3m-2l1k-j0i9-h8g7-f6e5d4c3b2a1
     * @bodyParam scheduled_at date_format:Y-m-d nullable A data e hora agendadas para a transação. Example: 2024-08-07
     *
     * @response 201 {
     *     "id": "uuid",
     *     "amount": 1000.00,
     *     "sender_id": "1a2b3c4d-5e6f-7g8h-9i0j-k1l2m3n4o5p6",
     *     "recipient_id": "6p5o4n3m-2l1k-j0i9-h8g7-f6e5d4c3b2a1",
     *     "status": "completed",
     *     "scheduled_at": "2024-08-07T00:00:00.000000Z",
     *     "created_at": "2024-08-06T00:00:00.000000Z",
     *     "updated_at": "2024-08-06T00:00:00.000000Z"
     * }
     * @response 422 {
     *     "errors": {
     *         "amount": [
     *             "O valor da transação é obrigatório.",
     *             "O valor da transação deve ser um número.",
     *             "O valor da transação deve ser maior ou igual a zero."
     *         ],
     *         "sender_id": [
     *             "O campo de ID do remetente é obrigatório.",
     *             "O ID do remetente deve ser um UUID válido.",
     *             "A conta do remetente não foi encontrada."
     *         ],
     *         "recipient_id": [
     *             "O campo de ID do destinatário é obrigatório.",
     *             "O ID do destinatário deve ser um UUID válido.",
     *             "A conta do destinatário não foi encontrada."
     *         ],
     *         "scheduled_at": [
     *             "A data e hora agendadas devem estar no formato Y-m-d.",
     *             "A data e hora agendadas devem ser no futuro."
     *         ]
     *     }
     * }
     *
     * @param CreateAccountBankTransactionRequest $request
     * @param AccountBankTransactionService $accountBankTransactionService
     * @return AccountBankTransactionResource
     */
    public function transferAmount(
        CreateAccountBankTransactionRequest $request,
        AccountBankTransactionService $accountBankTransactionService
    ): AccountBankTransactionResource {
        // Valida e processa a transação
        $accountBank = $accountBankTransactionService->create(
            $request->validated()
        );

        return AccountBankTransactionResource::make($accountBank);
    }
}
