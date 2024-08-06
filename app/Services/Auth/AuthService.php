<?php

namespace App\Services\Auth;

use App\Services\Auth\Clients\ExternalAuthClient;
use App\Services\Data\{ExternalAuthRequestData, ExternalAuthResponseData};
use GuzzleHttp\Exception\RequestException;
use Spatie\LaravelData\Contracts\BaseData;

class AuthService
{
    /**
     * Injeta o External Auth Service
     */
    public function __construct(private ExternalAuthClient $client)
    {
    }

    public function processAccount(array $data): BaseData
    {
        try {
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

            $responseDecoded = json_decode($response->getBody()->getContents(), true, flags: JSON_THROW_ON_ERROR);

            if (!data_get($responseDecoded, 'success') && !data_get($responseDecoded, 'authorized')) {
                return ExternalAuthResponseData::from([
                    'success'    => $responseDecoded['success'],
                    'authorized' => $responseDecoded['authorized'],
                ]);
            }

            return ExternalAuthResponseData::from([
                'success'    => $responseDecoded['success'],
                'authorized' => $responseDecoded['authorized'],
            ]);

        } catch (RequestException $e) {
            // Caso o serviço externo retorne erro 500, retorna um JSON com sucesso e autorização igual a false
            return ExternalAuthResponseData::from([
                'success'    => true,
                'authorized' => false,
            ]);
        } catch (\JsonException $e) {
            throw new \RuntimeException('Invalid JSON response: ' . $e->getMessage());
        }
    }
}
