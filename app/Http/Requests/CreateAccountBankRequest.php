<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

/**
 * Requisição para criar uma nova conta bancária.
 */
class CreateAccountBankRequest extends FormRequest
{
    /**
     * Determina se o usuário está autorizado a fazer essa requisição.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Obtém as regras de validação que se aplicam à requisição.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'    => nameRules(),
            'balance' => 'nullable|numeric|min:0',
        ];
    }

    /**
     * Manipula uma tentativa de validação falhada.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        // Ajusta o retorno da resposta para incluir apenas o primeiro erro
        throw new ValidationException($validator, response()->json([
            'message' => 'Os dados enviados não são válidos.',
            'errors'  => $validator->errors(),
        ], 422));
    }

    /**
     * Obtém as mensagens de validação personalizadas para a requisição.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required'    => 'O campo nome é obrigatório.',
            'name.string'      => 'O campo nome deve ser uma string.',
            'balance.numeric'  => 'O saldo deve ser um número.',
            'balance.min'      => 'O saldo deve ser maior ou igual a zero.',
            'balance.nullable' => 'O saldo pode ser nulo.',
        ];
    }
}
