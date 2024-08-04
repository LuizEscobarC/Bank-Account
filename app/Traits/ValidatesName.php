<?php

namespace App\Traits;

trait ValidatesName
{
    protected function nameRules(): array
    {
        return [
            'required',
            'string',
            'unique:account_banks,name',
            'regex:/^[a-zA-Z0-9\sáàãâêôçíúÁÀÃÂÊÔÇÍÚ_-]+$/',
        ];
    }

    protected function messageRules(): array
    {
        return [
            'unique'   => 'O nome da conta já está em uso.',
            'required' => 'O nome é obrigatório.',
            'string'   => 'O nome deve ser do tipo string.',
            'regex'    => 'Não é permitido caracteres especiais.',
        ];
    }
}
