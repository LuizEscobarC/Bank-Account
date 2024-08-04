<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class BlockEspecialCharsRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!preg_match("/^[a-zA-Z0-9\sáàãâêôçíúÁÀÃÂÊÔÇÍÚ_-]+$/", $value)) {
            $fail('Não é permitido caracteres especiais.');
        }
    }
}
