<?php

namespace App\Services\Data;

use Spatie\LaravelData\Data;

/**
 * Dados da resposta de autenticação externa.
 */
class ExternalAuthResponseData extends Data
{
    /**
     * Cria uma nova instância dos dados da resposta de autenticação.
     *
     * @param  bool  $success  Indica se a requisição foi bem-sucedida
     * @param  bool  $authorized  Indica se a autenticação foi autorizada
     */
    public function __construct(
        public bool $success,
        public bool $authorized
    ) {
    }
}
