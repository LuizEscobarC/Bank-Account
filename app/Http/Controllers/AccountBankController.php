<?php

namespace App\Http\Controllers;

use App\Models\AccountBank;
use Illuminate\Http\Request;

class AccountBankController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|unique:account_banks,name',
            'balance' => 'required|numeric|between:0,99999999999999.99',
        ]);

        $accountBank = AccountBank::create([
            'name'    => $validated['name'],
            'balance' => $validated['balance'],
        ]);

        return response()->json($accountBank, 201);
    }
}
