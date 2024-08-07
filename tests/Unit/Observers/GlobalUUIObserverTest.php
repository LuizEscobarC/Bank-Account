<?php
use App\Observers\GlobalUUIDObserver;
use App\Services\UUIDService;

it('assigns a UUID when creating a model', function () {
    $uuidServiceMock = Mockery::mock(UUIDService::class);
    $fakeUuid        = '123e4567-e89b-12d3-a456-426614174000';

    // Define o comportamento do mock
    $uuidServiceMock->shouldReceive('generate')
        ->once()
        ->andReturn($fakeUuid);

    $observer = new GlobalUUIDObserver($uuidServiceMock);
    $modelSpy = Mockery::spy(\App\Models\SuperModel::class);
    $modelSpy->shouldReceive('getAttribute')->with('id')->andReturn(null);
    $modelSpy->shouldReceive('setAttribute')->with('id', $fakeUuid);

    $observer->creating($modelSpy);

    // Verifica se o UUID foi atribuído ao modelo
    $modelSpy->shouldHaveReceived('setAttribute')->with('id', $fakeUuid);
});

afterEach(function () {
    Mockery::close();
});
