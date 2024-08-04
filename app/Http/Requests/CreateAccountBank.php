<?php

namespace App\Http\Requests;

use App\Traits\ValidatesName;
use Illuminate\Foundation\Http\FormRequest;

class CreateAccountBank extends FormRequest
{
    use ValidatesName;

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
            'name'    => $this->nameRules(),
            'balance' => 'nullable|numeric|min:0',
        ];
    }
}
