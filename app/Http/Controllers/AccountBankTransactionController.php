<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAccountBankTransaction;
use App\Services\AccountBankTransactionService;

class AccountBankTransactionController extends Controller
{
    public function __construct(
        private readonly AccountBankTransactionService $accountBankTransactionService
    ) {
    }

    public function tranferAmount(CreateAccountBankTransaction $request)
    {
        $accountBank = $this->accountBankTransactionService->create(
            $request->validated()
        );

        return response()->json($accountBank, 200);
    }
}
