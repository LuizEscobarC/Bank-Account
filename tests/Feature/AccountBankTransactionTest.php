<?php

use App\Models\AccountBank;

use function Pest\Laravel\{assertDatabaseHas, postJson};

it('should be able to transfer balances between two accounts', function () {
    // Arrange
    $accountSender    = AccountBank::factory()->create(['balance' => 8000]);
    $accountRecipient = AccountBank::factory()->create(['balance' => 8100]);

    // Act
    $post = postJson(route('account-banks.transaction'), [
        'sender_id'    => $accountSender->id,
        'recipient_id' => $accountRecipient->id,
        'amount'       => 3500,
    ]);

    $resultBalanceSender    = $accountSender->balance - 3500;
    $resultBalanceRecipient = $accountRecipient->balance + 3500;

    $accountSender->refresh();
    $accountRecipient->refresh();

    // Assert
    expect((float)$accountSender->balance)->toBe($resultBalanceSender);
    expect((float)$accountRecipient->balance)->toBe($resultBalanceRecipient);
    assertDatabaseHas('account_bank_transactions', [
        'sender_id'    => $accountSender->id,
        'recipient_id' => $accountRecipient->id,
        'amount'       => 3500,
    ]);
});

test('validation test', function (object $field) {
    // Arrange
    $accountSender    = AccountBank::factory()->create(['balance' => 8000.50]);
    $accountRecipient = AccountBank::factory()->create(['balance' => 8100.50]);

    // Act
    $post = postJson(route('account-banks.transaction'), [
        'sender_id'    => $accountSender->id,
        'recipient_id' => $accountRecipient->id,
        'amount'       => $field->value,
    ]);

    // Assert
    $post->assertJsonValidationErrors($field->rule);
})->with([
    'amount::min:0' => (object) ['name' => 'amount', 'value' => -5, 'rule' => 'amount'],
]);

test('if account balance is enough to transfer', function () {
    // Arrange
    $accountSender    = AccountBank::factory()->create(['balance' => 8000.50]);
    $accountRecipient = AccountBank::factory()->create(['balance' => 8100.50]);

    $amountToSend = 8100;

    // Act
    $post = postJson(route('account-banks.transaction'), [
        'sender_id'    => $accountSender->id,
        'recipient_id' => $accountRecipient->id,
        'amount'       => $amountToSend,
    ]);

    // Assert
    $post->assertJsonValidationErrors('amount');
});

it('should register schedule at the given time', function () {
    // Arrange
    $accountSender    = AccountBank::factory()->create(['balance' => 8000.50]);
    $accountRecipient = AccountBank::factory()->create(['balance' => 8100.50]);

    // Act
    $post = postJson(route('account-banks.transaction'), [
        'sender_id'    => $accountSender->id,
        'recipient_id' => $accountRecipient->id,
        'amount'       => 3500,
        'scheduled_at' => '2001-05-20 20:01',
    ]);

    // Assert
    $post->assertJsonValidationErrors('scheduled_at');
});
