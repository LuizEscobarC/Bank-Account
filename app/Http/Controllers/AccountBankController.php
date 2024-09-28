<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAccountBankRequest;
use App\Http\Resources\AccountBankResource;
use App\Models\AccountBank;
use App\Services\AccountBankService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


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

    public function show($id)
    {
        $accountBank = AccountBank::findOrFail($id);

        return new AccountBankResource($accountBank);

    }

    public function update(Request $request, string $id){
        
        $accountBank = AccountBank::findOrFail($id);
        
        $validator = Validator::make($request->all(),
        [
            'name'=> 'required|string|max:255',
            'balance'=> 'required|integer',
        ]);
        if($validator->fails())
        {
            return response()->json([
                'message' => 'all fields are required',
                'error' => $validator->messages(),
            ], 422);
        }

        $accountBank->update([

            'name' => $request->name,
            'balance' => $request->balance,

        ]);
        
        
         return new AccountBankResource($accountBank);

    }
     

    
}
