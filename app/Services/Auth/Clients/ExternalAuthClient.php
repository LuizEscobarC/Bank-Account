<?php

namespace App\Services\Auth\Clients;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class ExternalAuthClient
{
    private Client $client;

    private string $url = 'https://eo9ggxnfribmy6a.m.pipedream.net/beta-authorizer';

    public function __construct(string $email, Client $client = null)
    {
        $config = [
            'base_uri' => $this->url,
            'headers'  => [
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer ' . $this->getBearerToken($email),
                'Content-Type'  => 'application/json',
            ],
        ];

        $this->client = $client ?? new Client($config);
    }

    public function post(string $uri, array $options): \Psr\Http\Message\ResponseInterface
    {
        try {
            return $this->client->post($uri, $options);
        } catch (RequestException $e) {
            return new \GuzzleHttp\Psr7\Response(200, [], json_encode([
                'success'    => true,
                'authorized' => false,
            ], JSON_THROW_ON_ERROR));
        }
    }

    private function getBearerToken(string $email): string
    {
        return base64_encode($email);
    }
}
