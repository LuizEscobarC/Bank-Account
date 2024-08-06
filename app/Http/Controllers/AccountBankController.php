<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAccountBankRequest;
use App\Http\Resources\AccountBankResource;
use App\Services\AccountBankService;

/**
 * @group Account Bank
 *
 * APIs para criar e gerenciar contas bancárias.
 */
class AccountBankController extends Controller
{
    /**
     * Cria uma nova conta bancária.
     *
     * @bodyParam name string required O nome do titular da conta. Example: John Doe
     * @bodyParam balance numeric nullable O saldo inicial da conta. Example: 1000
     *
     * @response 201 {
     *     "id": "uuid",
     *     "name": "John Doe",
     *     "balance": 1000,
     *     "created_at": "2024-08-06T00:00:00.000000Z",
     *     "updated_at": "2024-08-06T00:00:00.000000Z"
     * }
     * @response 422 {
     *     "errors": {
     *         "name": ["O campo nome é obrigatório."],
     *         "balance": [
     *             "O saldo deve ser um número.",
     *             "O saldo deve ser maior ou igual a zero.",
     *             "O campo nome é opcional."
     *         ]
     *     }
     * }
     *
     * @param CreateAccountBankRequest $request
     * @param AccountBankService $accountBankService
     * @return AccountBankResource
     * @throws \Exception
     */
    public function store(
        CreateAccountBankRequest $request,
        AccountBankService $accountBankService
    ): AccountBankResource {
        $accountBank = $accountBankService->create(
            $request->validated()
        );

        return AccountBankResource::make($accountBank);
    }
}
