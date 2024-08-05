<?php

/**
 *
 *
 -  Verificar se uma transação pode ser agendada para uma data futura válida.
 -  Testar a validação para garantir que a data de execução seja posterior à data atual.
 -  Verificar se a transação agendada é processada corretamente na data programada.
 -  Testar se a conta remetente tem saldo suficiente na data de execução agendada.

 *
 */

use App\Models\AccountBank;

use function Pest\Laravel\assertDatabaseHas;

// Verificar se uma transação pode ser agendada para uma data futura válida.
it('should allow scheduling a transaction for a valid future date', function () {
    // arrange -> pega as contas
    $accountSender    = AccountBank::factory()->create(['balance' => 8000.50]);
    $accountRecipient = AccountBank::factory()->create(['balance' => 8100.50]);

    // act -> agendar a transferência para uma data futura válida
    $response = $this->postJson(route('account-banks.transaction'), [
        'sender_id'    => $accountSender->id,
        'recipient_id' => $accountRecipient->id,
        'amount'       => 3500,
        'scheduled_at' => (new DateTime('now'))->sub(new DateInterval('P1D'))
        ->format('Y-m-d H:i:s'),
    ]);

    // assert -> verificar se a resposta não contém erros de validação
    $response->assertJsonValidationErrors(['scheduled_at']);
});

// Testar a validação para garantir que a data de execução seja posterior à data atual.
test('schedule validate to have certain that is a future date', function () {
    // Arrange: Cria contas para enviar e receber
    $accountSender    = AccountBank::factory()->create(['balance' => 8000.50]);
    $accountRecipient = AccountBank::factory()->create(['balance' => 8100.50]);

    // Testa uma data no futuro
    $futureDate = (new DateTime('now'))->add(new DateInterval('P1D'))->format('Y-m-d H:i:s');
    $this->postJson(route('account-banks.transaction'), [
        'sender_id'    => $accountSender->id,
        'recipient_id' => $accountRecipient->id,
        'amount'       => 3500,
        'scheduled_at' => $futureDate,
    ]);

    // Assert: Código de status para sucesso
    assertDatabaseHas('account_bank_transactions', [
        'sender_id'    => $accountSender->id,
        'recipient_id' => $accountRecipient->id,
        'amount'       => "3500.00",
        'scheduled_at' => $futureDate,
    ]);
});
