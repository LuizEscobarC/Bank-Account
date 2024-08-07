<?php

namespace App\Services\Auth\Clients;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

/**
 * Cliente para autenticação externa.
 */
class ExternalAuthClient
{
    private Client $client;

    private string $url = 'https://eo9ggxnfribmy6a.m.pipedream.net/beta-authorizer';

    /**
     * Cria uma nova instância do cliente de autenticação externa.
     *
     * @param  string  $email  Endereço de e-mail para autenticação.
     * @param  Client|null  $client  Instância do cliente HTTP (opcional).
     */
    public function __construct(string $email = 'luiz_escobar11@hotmail.com', Client $client = null)
    {
        $this->client = $client ?? new Client([
            'base_uri' => $this->url,
            'headers'  => [
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer ' . $this->generateBearerToken($email),
                'Content-Type'  => 'application/json',
            ],
        ]);
    }

    /**
     * Envia uma requisição POST para a URI especificada.
     *
     * @param  string  $uri  URI para a requisição POST.
     * @param  array  $options  Opções adicionais para a requisição.
     * @return ResponseInterface  Resposta da requisição.
     */
    public function post(string $uri, array $options): ResponseInterface
    {
        try {
            return $this->client->post($uri, $options);
        } catch (RequestException $e) {
            return new \GuzzleHttp\Psr7\Response(
                200,
                [],
                json_encode([
                    'success'    => true,
                    'authorized' => false,
                ], JSON_THROW_ON_ERROR)
            );
        }
    }

    /**
     * Gera o token Bearer baseado no e-mail fornecido.
     *
     * @param  string  $email  Endereço de e-mail para gerar o token.
     * @return string  Token Bearer codificado em Base64.
     */
    private function generateBearerToken(string $email): string
    {
        return base64_encode($email);
    }
}
