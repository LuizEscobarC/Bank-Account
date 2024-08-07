<?php

use App\Models\AccountBankTransaction;

it('has the correct cast types', function () {
    $transaction = new AccountBankTransaction();

    $casts = $transaction->getCasts();

    expect($casts['amount'])->toBe('decimal:2');
    expect($casts['scheduled_at'])->toBe('datetime:Y-m-d H:i:s');
});

it('has the correct fillable attributes', function () {
    $transaction = new AccountBankTransaction();

    $fillable = [
        'sender_id',
        'recipient_id',
        'amount',
        'scheduled_at',
    ];

    expect($transaction->getFillable())->toEqual($fillable);
});
