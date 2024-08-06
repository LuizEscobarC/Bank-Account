<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAccountBankTransactionRequest;
use App\Services\AccountBankTransactionService;

class AccountBankTransactionController extends Controller
{
    public function tranferAmount(
        CreateAccountBankTransactionRequest $request,
        AccountBankTransactionService $accountBankTransactionService
    ) {
        $accountBank = $accountBankTransactionService->create(
            $request->validated()
        );

        return response()->json($accountBank, 201);
    }
}
