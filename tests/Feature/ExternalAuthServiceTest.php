<?php

use App\Models\AccountBank;
use App\Services\Auth\AuthService;
use App\Services\Auth\Clients\ExternalAuthClient;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;

/**
 * Mocks para os serviços de autenticação
 */
beforeEach(function () {
    $this->clientMock  = Mockery::mock(ExternalAuthClient::class);
    $this->authService = new AuthService($this->clientMock);
    $this->sender      = AccountBank::factory()->create();
    $this->reciever    = AccountBank::factory()->create();
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

    $requestData = new \App\Services\Data\ExternalAuthRequestData(
        sender:  $this->sender->id,
        receiver: $this->reciever->id,
        amount: 100
    );

    // Act
    $response = $this->authService->processAccount($requestData);

    // Assert
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

    $requestData = new \App\Services\Data\ExternalAuthRequestData(
        sender:  $this->sender->id,
        receiver: $this->reciever->id,
        amount: 100
    );

    // Act
    $response = $this->authService->processAccount($requestData);

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

    $requestData = new \App\Services\Data\ExternalAuthRequestData(
        sender:  $this->sender->id,
        receiver: $this->reciever->id,
        amount: 100
    );

    // Act
    $response = $this->authService->processAccount($requestData);

    // Assert
    expect($response->success)->toBeTrue();  // Espera-se que o sucesso seja falso
    expect($response->authorized)->toBeFalse(); // Espera-se que a autorização seja falsa
});
