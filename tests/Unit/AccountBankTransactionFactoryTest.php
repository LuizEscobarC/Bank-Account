<?php

use App\Models\AccountBankTransaction;

it('creates a valid AccountBankTransaction instance using the factory', function () {

    $transaction = AccountBankTransaction::factory()->create();

    expect($transaction)->toBeInstanceOf(AccountBankTransaction::class);
    expect($transaction->id)->toBeString()->not()->toBeEmpty();
    expect($transaction->sender_id)->toBeString()->not()->toBeEmpty();
    expect($transaction->recipient_id)->toBeString()->not()->toBeEmpty();
    expect($transaction->amount)->toBeEnum();
    expect($transaction->scheduled_at === null || $transaction->scheduled_at instanceof \DateTime)->toBeTrue();
    expect($transaction->authorized)->toBeBool();
});
