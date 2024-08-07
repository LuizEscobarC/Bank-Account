<?php

use App\Services\UUIDService;

use function PHPUnit\Framework\{assertEquals, assertIsString, assertNotNull};

it('generate_an_uuid', function () {
    $uuidService = app(UUIDService::class);
    $uuid        = $uuidService->generate();

    assertNotNull($uuid);
    assertIsString($uuid);
    assertEquals(36, strlen($uuid));
});
