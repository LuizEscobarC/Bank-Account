<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAccountBankRequest;
use App\Services\AccountBankService;

class AccountBankController extends Controller
{
    /**
     * Cria uma nova conta bancária.
     *
     * @param CreateAccountBankRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function store(
        CreateAccountBankRequest $request,
        AccountBankService $accountBankService
    ) {
        $accountBank = $accountBankService->create(
            $request->validated()
        );

        return response()->json($accountBank, 201);
    }
}
