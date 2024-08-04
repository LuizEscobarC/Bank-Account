<?php

use App\Models\AccountBank;

use function Pest\Laravel\assertDatabaseHas;
use function PHPUnit\Framework\assertEquals;

/**
 *
    1 - Verificar se a transferência de fundos entre duas contas pode ser realizada com sucesso.
    2 - Testar a validação para garantir que o valor da transferência seja positivo e maior que zero.
    3 - Testar se o saldo da conta remetente é suficiente para cobrir a transferência e garantir que o saldo não fique negativo.
    4 - Verificar se a transferência é registrada com a data e hora exata da operação.



 */

it('should be able to transfer balances beetwen two accounts', function () {
    // arrange -> pega as contas
    $accountSender    = AccountBank::factory()->create(['balance' => 8000.50]);
    $accountRecipient = AccountBank::factory()->create(['balance' => 8100.50]);

    // act -> fazer a transferencia
    $post = $this->postJson(route('account-banks.transaction'), [
        'sender_id'    => $accountSender->id,
        'recipient_id' => $accountRecipient->id,
        'amount'       => 3500,
    ]);

    $resultBalanceSender    = $accountSender->balance - 3500;
    $resultBalanceRecipient = $accountRecipient->balance + 3500;

    $accountSender->refresh();
    $accountRecipient->refresh();

    // assert -> garantir que o saldo foi transferido
    assertEquals($resultBalanceSender, $accountSender->balance);
    assertEquals($resultBalanceRecipient, $accountRecipient->balance);
    assertDatabaseHas('account_bank_transactions', [
        'sender_id'    => $accountSender->id,
        'recipient_id' => $accountRecipient->id,
        'amount'       => 3500,
    ]);
});

// Testar a validação para garantir que o valor da transferência seja positivo e maior que zero.

test('validation test', function (object $field) {
    // arrange -> pega as contas
    $accountSender    = AccountBank::factory()->create(['balance' => 8000.50]);
    $accountRecipient = AccountBank::factory()->create(['balance' => 8100.50]);

    // act -> fazer a transferencia
    $post = $this->postJson(route('account-banks.transaction'), [
        'sender_id'    => $accountSender->id,
        'recipient_id' => $accountRecipient->id,
        'amount'       => $field->value,
    ]);

    expect((object)$post->decodeResponseJson()['errors'])->toHaveProperty($field->rule);

})->with([
    'amount::min:0' => (object) ['name' => 'amount', 'value' => -5, 'rule' => 'amount'],
]);

// Testar se o saldo da conta remetente é suficiente para cobrir a transferência e garantir que o saldo não fique negativo.
test('if account balance is enough to transfer', function () {
    // arrange -> pega as contas
    $accountSender    = AccountBank::factory()->create(['balance' => 8000.50]);
    $accountRecipient = AccountBank::factory()->create(['balance' => 8100.50]);

    $amountToSend = 8100;

    // act -> fazer a transferencia
    $post = $this->postJson(route('account-banks.transaction'), [
        'sender_id'    => $accountSender->id,
        'recipient_id' => $accountRecipient->id,
        'amount'       => $amountToSend,
    ]);

    $post->assertJsonValidationErrors('amount');
});

// Verificar se a transferência é registrada com a data e hora exata da operação.
// it('should register schedule at the given time', function() {

// });
