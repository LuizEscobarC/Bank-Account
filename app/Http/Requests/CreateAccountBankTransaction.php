<?php

namespace App\Http\Requests;

use App\Rules\SenderEnoughAmountRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateAccountBankTransaction extends FormRequest
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
                'required', 'exists:account_banks,id', 'uuid',
            ],
            'recipient_id' => [
                'required', 'exists:account_banks,id', 'uuid',
            ],
            'amount' => [
                'required', 'numeric', 'min:0', new SenderEnoughAmountRule($this->sender_id),
            ],
            'scheduled_at' => [
                'nullable', "date_format:Y-m-d H:i:s",
            ],
        ];
    }
}
