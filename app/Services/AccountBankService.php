<?php

namespace App\Services;

use App\Models\AccountBank;
use Illuminate\Validation\ValidationException;

class AccountBankService
{
    /**
     * Cria uma nova conta bancária com base nos dados fornecidos.
     *
     * @param  array  $data  Dados para criar a conta bancária
     * @return AccountBank  Instância da conta bancária criada
     * @throws ValidationException  Exceção lançada se os dados fornecidos forem inválidos
     */
    public function create(array $data): AccountBank
    {
        return AccountBank::create([
            'name'    => $data['name'],
            'balance' => $data['balance'] ?? 0.00,
        ]);
    }
}
