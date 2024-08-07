<?php

namespace App\Rules;

use App\Models\AccountBank;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SenderEnoughAmountRule implements ValidationRule
{
    /**
     * Cria uma nova instância da regra de validação.
     *
     * @param  string|null  $senderId  ID do remetente
     */
    public function __construct(public readonly string|null $senderId)
    {
    }

    /**
     * Valida se o remetente possui saldo suficiente para a transação.
     *
     * @param  string  $attribute  Nome do atributo sendo validado
     * @param  mixed  $value  Valor do atributo sendo validado (o montante da transação)
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail  Função de falha da validação
     * @return void
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
