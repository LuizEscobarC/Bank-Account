<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class DifferentAccountBankIdsRule implements ValidationRule
{
    /**
     * Cria uma nova instância da regra de validação.
     *
     * @param  string|null  $senderId  ID do remetente
     * @param  string|null  $recipientId  ID do destinatário
     */
    public function __construct(
        public readonly string|null $senderId,
        public readonly string|null $recipientId
    ) {
    }

    /**
     * Valida se o remetente e o destinatário são diferentes.
     *
     * @param  string  $attribute  Nome do atributo sendo validado
     * @param  mixed  $value  Valor do atributo sendo validado
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail  Função de falha da validação
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->senderId || !$this->recipientId) {
            $fail('O ID do remetente e o ID do destinatário não podem estar vazios.');

            return;
        }

        // Verifica se os IDs são diferentes
        if ($this->senderId === $this->recipientId) {
            $fail('Não é possível fazer transações para a mesma conta.');

            return;
        }
    }
}
