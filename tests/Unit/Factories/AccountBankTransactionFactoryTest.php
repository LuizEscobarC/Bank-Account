<?php

use App\Models\AccountBankTransaction;

it('creates a valid AccountBankTransaction instance using the factory', function () {

    $transaction = AccountBankTransaction::factory()->create();

    expect($transaction)->toBeInstanceOf(AccountBankTransaction::class);
    expect($transaction->id)->toBeString()->not()->toBeEmpty();
    expect($transaction->sender_id)->toBeString()->not()->toBeEmpty();
    expect($transaction->recipient_id)->toBeString()->not()->toBeEmpty();
    // uuid validation length
    expect($transaction->id)->toHaveLength(36);
    expect($transaction->sender_id)->toHaveLength(36);
    expect($transaction->recipient_id)->toHaveLength(36);

    expect($transaction->amount)->toBeEnum();
    expect($transaction->scheduled_at === null || $transaction->scheduled_at instanceof \DateTime)->toBeTrue();
});
