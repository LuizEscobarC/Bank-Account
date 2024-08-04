<?php

namespace App\Services;

use App\Models\AccountBank;
use App\Rules\DuplicatedNameAccountBankRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AccountBankService
{
    /**
     * Cria uma nova conta bancária com base nos dados fornecidos.
     *
     * @param array $data
     * @return AccountBank
     * @throws ValidationException
     */
    public function create(array $data): AccountBank
    {
        $this->validate($data);

        return AccountBank::create([
            'name'    => $data['name'],
            'balance' => $data['balance'] ?? 0.00,
        ]);
    }

    /**
     * Valida os dados fornecidos para criar uma conta bancária.
     *
     * @param array $data
     * @return void
     * @throws ValidationException
     */
    protected function validate(array $data): void
    {
        $validator = Validator::make($data, [
            'name'    => ['required', 'string', new DuplicatedNameAccountBankRule()],
            'balance' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
