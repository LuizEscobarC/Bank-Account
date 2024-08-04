<?php

namespace App\Http\Controllers;

use App\Services\AccountBankService;
use Illuminate\Http\Request;
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
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $accountBank = $this->accountBankService->create($request->all());

            return response()->json($accountBank, 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }
}
