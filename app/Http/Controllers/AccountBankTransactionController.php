<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAccountBankTransactionRequest;
use App\Services\AccountBankTransactionService;
use Illuminate\Http\JsonResponse;

class AccountBankTransactionController extends Controller
{
    public function transferAmount(
        CreateAccountBankTransactionRequest $request,
        AccountBankTransactionService $accountBankTransactionService
    ): JsonResponse {
        // Valida e processa a transação
        $accountBank = $accountBankTransactionService->create(
            $request->validated()
        );

        // Retorna uma resposta de sucesso
        return response()->json($accountBank, 201);
    }
}
