<?php

namespace App\Services\Data;

use Spatie\LaravelData\Data;

/**
 * Dados da requisição de autenticação externa.
 */
class ExternalAuthRequestData extends Data
{
    /**
     * Cria uma nova instância dos dados da requisição de autenticação.
     *
     * @param  string  $sender  ID do remetente
     * @param  string  $receiver  ID do destinatário
     * @param  float  $amount  Valor da transação
     */
    public function __construct(
        public string $sender,
        public string $receiver,
        public float $amount
    ) {
    }
}
