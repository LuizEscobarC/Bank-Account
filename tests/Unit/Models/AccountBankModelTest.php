<?php

use App\Models\AccountBank;

// Teste para criar uma conta bancária com UUID
it('creates an account bank with a UUID', function () {
    $accountBank = AccountBank::factory()->create();

    expect($accountBank->id)->not()->toBeNull();
    expect($accountBank->id)->toBeString();
    expect(strlen($accountBank->id))->toEqual(36);
});
