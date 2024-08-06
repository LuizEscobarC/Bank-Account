<?php

namespace App\Services\Auth\Clients;

use App\Services\Data\{ExternalAuthRequestData, ExternalAuthResponseData};
use Illuminate\Auth\AuthenticationException;
use Spatie\LaravelData\Contracts\BaseData;

class AuthService
{
    private $client;

    public function __construct()
    {
        $this->client = resolve(ExternalAuthClient::class, [
            'email' => 'email@teste.com',
        ]);
    }

    public function processAccount(array $data): BaseData
    {
        $response = $this->client->post(
            'auth',
            [
                'json' => ExternalAuthRequestData::from([
                    'sender'   => $data['sender_id'],
                    'receiver' => $data['recipient_id'],
                    'amount'   => $data['amount'],
                ]),
            ]
        );

        $responseDecoded = json_decode($response->getBody()->getContents());

        if (!data_get($responseDecoded, 'success')) {
            throw new AuthenticationException("Voce nao foi autorizado a realizar essa transferencia");
        }

        return ExternalAuthResponseData::from([
            'success'    => $responseDecoded->success,
            'authorized' => $responseDecoded->authorized,
        ]);
    }

}
