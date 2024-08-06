<?php

use App\Models\AccountBankTransaction;

it('has the correct cast types', function () {
    $transaction = new AccountBankTransaction();

    $casts = $transaction->getCasts();

    expect($casts['amount'])->toBe('decimal:2');
    expect($casts['scheduled_at'])->toBe('datetime:Y-m-d H:i:s');
    expect($casts['authorized'])->toBe('boolean');
});

it('has the correct fillable attributes', function () {
    $transaction = new AccountBankTransaction();

    $fillable = [
        'sender_id',
        'recipient_id',
        'status',
        'authorized',
        'amount',
        'scheduled_at',
        'processed_at',
    ];

    expect($transaction->getFillable())->toEqual($fillable);
});
