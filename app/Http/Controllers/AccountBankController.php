<?php

namespace App\Http\Controllers;

use App\Http\Requests\{CreateAccountBankRequest};
use App\Services\AccountBankService;
use Illuminate\Validation\ValidationException;

class AccountBankController extends Controller
{
    /**
     * Cria uma nova conta bancária.
     *
     * @param CreateAccountBankRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(
        CreateAccountBankRequest $request,
        AccountBankService $accountBankService
    ) {
        try {
            $accountBank = $accountBankService->create(
                $request->validated()
            );

            return response()->json($accountBank, 201);
        } catch (ValidationException $e) {
            response()->json([
                'message' => $e->getMessage(),
            ], 403);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
