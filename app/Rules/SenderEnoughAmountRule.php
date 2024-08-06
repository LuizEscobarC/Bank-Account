<?php

namespace App\Rules;

use App\Models\AccountBank;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SenderEnoughAmountRule implements ValidationRule
{
    public function __construct(private readonly string $senderId)
    {
    }
    /**
     * Regra de validação para saldo (balance) insuficiente
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $balance = AccountBank::find($this->senderId)->balance;

        if ($balance < $value) {
            $fail('O saldo é insuficiente para a transação.');
        }
    }
}
