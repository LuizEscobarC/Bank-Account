<?php
/**
 * Suporte de regras de validação de nome de conta bancária
 */
function nameRules(): array
{
    return [
        'required',
        'string',
        'regex:/^[a-zA-Z0-9\sáàãâêôçíúÁÀÃÂÊÔÇÍÚ_-]+$/',
    ];
}

/**
 * Retorna as mensagens de regra para cada validação de criação de uma conta bancária
 */
function messageRules(): array
{
    return [
        'required' => 'O nome é obrigatório.',
        'string'   => 'O nome deve ser do tipo string.',
        'regex'    => 'Não é permitido caracteres especiais.',
    ];
}
