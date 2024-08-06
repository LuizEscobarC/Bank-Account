<?php

namespace App\Http\Requests;

use App\Rules\{
    DifferentAccountBankIdsRule,
    SenderEnoughAmountRule
};
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class CreateAccountBankTransactionRequest extends FormRequest
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
     * Custom validation messages.
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
}
