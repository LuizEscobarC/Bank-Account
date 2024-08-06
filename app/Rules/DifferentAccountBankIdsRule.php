<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class DifferentAccountBankIdsRule implements ValidationRule
{
    public function __construct(
        public readonly string|null $senderId,
        public readonly string|null $recipientId
    ) {
    }
    /**
     * Regra de validação para saldo (balance) insuficiente
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->senderId || !$this->recipientId) {
            $fail('O ID do remetente, destinatário não podem estar vazio.');

            return;
        }

        // Verifica se os IDs são diferentes
        if($this->senderId === $this->recipientId) {
            $fail('Não é possível fazer transações para a mesma conta.');

            return;
        }
    }
}
