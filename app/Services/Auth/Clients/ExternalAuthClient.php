<?php

namespace App\Services\Auth\Clients;

use GuzzleHttp\Client;

class ExternalAuthClient extends Client
{
    private $url = 'https://eo9ggxnfribmy6a.m.pipedream.net/beta-authorizer';

    public function __construct(string $email)
    {
        $config = [
            'base_uri' => $this->url,
            'headers'  => [
                'Accept'        => 'application/json',
                'authorization' => 'Bearer ' . $this->getBearerToken($email),
                'Content-Type'  => 'application/json',
            ],
        ];

        parent::__construct(
            $config
        );
    }

    /**
     * Codifica email em base64
     */
    protected function getBearerToken(string $email): string
    {
        return base64_encode($email);
    }

}
