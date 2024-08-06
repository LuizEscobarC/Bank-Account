<?php

use App\Models\AccountBank;

use function Pest\Laravel\assertDatabaseHas;

// Testa se uma transação pode ser agendada para uma data futura válida
it('should allow scheduling a transaction for a valid future date', function () {
    // Arrange:
    $accountSender    = AccountBank::factory()->create(['balance' => 8000.55]);
    $accountRecipient = AccountBank::factory()->create(['balance' => 8100.55]);

    // Act:
    $response = $this->postJson(route('account-banks.transaction'), [
        'sender_id'    => $accountSender->id,
        'recipient_id' => $accountRecipient->id,
        'amount'       => 3500,
        'scheduled_at' => (new DateTime('now'))->sub(new DateInterval('P1D'))->format('Y-m-d H:i:s'),
    ]);

    // Assert: Verifica se há erros de validação na resposta
    $response->assertJsonValidationErrors(['scheduled_at']);
});

// Testa a validação para garantir que a data de execução seja uma data futura válida
test('schedule validate to have certain that is a future date', function () {
    // Arrange:
    $accountSender    = AccountBank::factory()->create(['balance' => 8000.50]);
    $accountRecipient = AccountBank::factory()->create(['balance' => 8100.50]);

    // Act:
    $futureDate = (new DateTime('now'))->add(new DateInterval('P1D'))->format('Y-m-d');
    $this->postJson(route('account-banks.transaction'), [
        'sender_id'    => $accountSender->id,
        'recipient_id' => $accountRecipient->id,
        'amount'       => 3500,
        'scheduled_at' => $futureDate,
    ]);

    // Assert: Verifica se a transação foi registrada na base de dados com a data futura
    assertDatabaseHas('account_bank_transactions', [
        'sender_id'    => $accountSender->id,
        'recipient_id' => $accountRecipient->id,
        'amount'       => "3500.00",
        'scheduled_at' => $futureDate,
    ]);
});
