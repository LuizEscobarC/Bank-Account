<?php

use App\Models\AccountBank;

it('creates a valid AccountBank instance using the factory', function () {
    $accountBank = AccountBank::factory()->create();

    expect($accountBank)->toBeInstanceOf(AccountBank::class);
    expect($accountBank->id)->toBeString()->not()->toBeEmpty();
    expect($accountBank->name)->toBeString()->not()->toBeEmpty();
    expect($accountBank->balance)->toBeString()->not()->toBeEmpty();
    ;
});
