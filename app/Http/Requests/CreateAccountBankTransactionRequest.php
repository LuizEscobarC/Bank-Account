<?php

namespace App\Http\Requests;

use App\Rules\{
    DifferentAccountBankIdsRule,
    SenderEnoughAmountRule
};
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

/**
 * Requisição para criar uma transação bancária.
 */
class CreateAccountBankTransactionRequest extends FormRequest
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
            'sender_id' => [
                'required', 'uuid', 'exists:account_banks,id',
                new DifferentAccountBankIdsRule(
                    $this->input('sender_id'),
                    $this->input('recipient_id')
                ),
            ],
            'recipient_id' => [
                'required', 'uuid', 'exists:account_banks,id',
            ],
            'amount' => [
                'required', 'numeric', 'min:0',
                new SenderEnoughAmountRule(
                    $this->input('sender_id')
                ),
            ],
            'scheduled_at' => [
                'nullable', 'date_format:Y-m-d', 'after:now',
            ],
        ];
    }

    /**
     * Obtém as mensagens de validação personalizadas para a requisição.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'sender_id.required' => 'O campo de ID do remetente é obrigatório.',
            'sender_id.uuid'     => 'O ID do remetente deve ser um UUID válido.',
            'sender_id.exists'   => 'A conta do remetente não foi encontrada.',

            'recipient_id.required' => 'O campo de ID do destinatário é obrigatório.',
            'recipient_id.uuid'     => 'O ID do destinatário deve ser um UUID válido.',
            'recipient_id.exists'   => 'A conta do destinatário não foi encontrada.',

            'amount.required' => 'O valor da transação é obrigatório.',
            'amount.numeric'  => 'O valor da transação deve ser um número.',
            'amount.min'      => 'O valor da transação deve ser maior ou igual a zero.',

            'scheduled_at.date_format' => 'A data e hora agendadas devem estar no formato Y-m-d.',
            'scheduled_at.after'       => 'A data e hora agendadas devem ser no futuro.',
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
}
