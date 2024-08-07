<?php

namespace App\Services\Auth;

use App\Services\Auth\Clients\ExternalAuthClient;
use App\Services\Data\{ExternalAuthRequestData, ExternalAuthResponseData};
use GuzzleHttp\Exception\RequestException;
use Spatie\LaravelData\Contracts\BaseData;

/**
 * Serviço de autenticação que processa requisições usando um cliente externo.
 */
class AuthService
{
    /**
     * @param ExternalAuthClient $client Cliente externo de autenticação.
     */
    public function __construct(private ExternalAuthClient $client)
    {
    }

    /**
     * Processa a autenticação de uma conta externa.
     *
     * @param ExternalAuthRequestData $requestData Dados da requisição de autenticação.
     * @return BaseData Dados da resposta de autenticação.
     */
    public function processAccount(ExternalAuthRequestData $requestData): BaseData
    {
        try {
            $response = $this->client->post('auth', ['json' => $requestData]);

            $responseData = json_decode($response->getBody()->getContents(), true, flags: JSON_THROW_ON_ERROR);

            return ExternalAuthResponseData::from([
                'success'    => $responseData['success'] ?? false,
                'authorized' => $responseData['authorized'] ?? false,
            ]);
        } catch (RequestException $e) {
            return ExternalAuthResponseData::from([
                'success'    => true,
                'authorized' => false,
            ]);
        } catch (\JsonException $e) {
            throw new \RuntimeException('Resposta JSON inválida: ' . $e->getMessage());
        }
    }
}
