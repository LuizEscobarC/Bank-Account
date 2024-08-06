<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class CreateAccountBankRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
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
     * Handle a failed validation attempt.
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
            'message' => 'OS dados enviados não são válidos.',
            'errors'  => $validator->errors(),
        ], 422));
    }

    /**
     * Get the custom validation messages for the request.
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
