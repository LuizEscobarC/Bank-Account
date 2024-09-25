<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAccountBankRequest;
use App\Http\Resources\AccountBankResource;
use App\Services\AccountBankService;
use App\Models\AccountBank;
use Illuminate\Http\JsonResponse;


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
     * @bodyParam name string required O nome do titular da conta. Exemplo: John Doe
     * @bodyParam balance numeric nullable O saldo inicial da conta. Exemplo: 1000.00
     *
     * @response 201 {
     *     "id": "uuid",
     *     "name": "John Doe",
     *     "balance": 1000.00,
     *     "created_at": "2024-08-06 19:46:33",
     *     "updated_at": "2024-08-06 19:46:33"
     * }
     * @response 422 {
     *     "errors": {
     *         "name": ["O campo nome é obrigatório."],
     *         "balance": [
     *             "O saldo deve ser um número.",
     *             "O saldo deve ser maior ou igual a zero."
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

    public function show($id): JsonResponse
    {
        // Tenta encontrar a conta bancária pelo ID
        $accountBank = AccountBank::find($id);

        // Verifica se a conta foi encontrada
        if (!$accountBank) {
            return response()->json([
                'error' => 'Account not found'
            ], 404); // Retorna um erro 404 se não encontrar
        }

        // Retorna os dados da conta em formato JSON
        return response()->json([
            'id' => $accountBank->id,
            'name' => $accountBank->name,
            'balance' => $accountBank->balance
        ]);
    }

}
