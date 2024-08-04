<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAccountBank;
use App\Services\AccountBankService;
use Illuminate\Validation\ValidationException;

class AccountBankController extends Controller
{
    protected AccountBankService $accountBankService;

    public function __construct(AccountBankService $accountBankService)
    {
        $this->accountBankService = $accountBankService;
    }

    /**
     * Cria uma nova conta bancária.
     *
     * @param CreateAccountBank $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateAccountBank $request)
    {
        try {
            $accountBank = $this->accountBankService->create(
                $request->validated()
            );

            return response()->json($accountBank, 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }
}
