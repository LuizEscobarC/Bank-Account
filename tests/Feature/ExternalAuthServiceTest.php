<?php

use App\Services\Auth\AuthService;
use App\Services\Auth\Clients\ExternalAuthClient;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;

/**
 * Mocks para os serviços de autenticaçãoe
 */
beforeEach(function () {
    $this->clientMock  = Mockery::mock(ExternalAuthClient::class);
    $this->authService = new AuthService($this->clientMock);
});

// teste de autorização externa permitida
test('processAccount success', function () {
    // Arrange
    $this->clientMock
        ->shouldReceive('post')
        ->once()
        ->andReturn(new Response(200, [], json_encode([
            'success'    => true,
            'authorized' => true,
        ], JSON_THROW_ON_ERROR)));

    // Act
    $response = $this->authService->processAccount([
        'sender_id'    => 1,
        'recipient_id' => 2,
        'amount'       => 100,
    ]);

    // Arrange
    expect($response->success)->toBeTrue();
    expect($response->authorized)->toBeTrue();
});

// teste de autorização externa recusada
test('processAccount failure', function () {
    // Arrange
    $this->clientMock
        ->shouldReceive('post')
        ->once()
        ->andReturn(new Response(200, [], json_encode([
            'success'    => true,
            'authorized' => false,
        ], JSON_THROW_ON_ERROR)));
    // Act
    $response = $this->authService->processAccount([
        'sender_id'    => 1,
        'recipient_id' => 2,
        'amount'       => 100,
    ]);

    // Assert
    expect($response->success)->toBeTrue();
    expect($response->authorized)->toBeFalse();
});

// testar o caso de uso de indisponibilidade do serviço externo
test('processAccount service returns 500 Internal Server Error', function () {
    // Arrange
    $this->clientMock
        ->shouldReceive('post')
        ->once()
        ->andThrow(new RequestException(
            'Internal Server Error',
            new \GuzzleHttp\Psr7\Request('POST', 'test'),
            new Response(500, [], json_encode([
                'error' => 'Internal Server Error',
            ], JSON_THROW_ON_ERROR))
        ));

    // Act
    $response = $this->authService->processAccount([
        'sender_id'    => 1,
        'recipient_id' => 2,
        'amount'       => 100,
    ]);

    // Assert
    expect($response->success)->toBeTrue();
    expect($response->authorized)->toBeFalse();
});
