<?php

namespace App\Rules;

use App\Models\AccountBank;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SenderEnoughAmountRule implements ValidationRule
{
    public function __construct(public readonly string|null $senderId)
    {
    }
    /**
     * Regra de validação para saldo (balance) insuficiente
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->senderId) {
            $fail('O ID do remetente não pode estar vazio.');

            return;
        }

        $accountBank = AccountBank::find($this->senderId);

        if (!$accountBank) {
            $fail('A conta do remetente não foi encontrada.');

            return;
        }

        if ($accountBank->balance < $value) {
            $fail('O saldo é insuficiente para a transação.');

            return;
        }
    }
}
