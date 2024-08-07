<?php

use App\Models\AccountBank;

use function Pest\Laravel\postJson;

use Symfony\Component\HttpFoundation\Response;

// Testa a transferência de dinheiro sem data de agendamento
test('processes transaction without scheduled_at', function () {
    // Arrange:
    $accountSender    = AccountBank::factory()->create(['balance' => 8000]);
    $accountRecipient = AccountBank::factory()->create(['balance' => 8100]);

    // Act:
    $post = postJson(route('account-banks.transaction'), [
        'sender_id'    => $accountSender->id,
        'recipient_id' => $accountRecipient->id,
        'amount'       => 1000,
    ]);

    // Assert:
    $post->assertStatus(Response::HTTP_CREATED);
});

// Testa a aceitação de uma data de agendamento futura
test('accepts scheduled_at in the future', function () {
    // Arrange:
    $accountSender    = AccountBank::factory()->create(['balance' => 8000]);
    $accountRecipient = AccountBank::factory()->create(['balance' => 8100]);

    // Act:
    $post = postJson(route('account-banks.transaction'), [
        'sender_id'    => $accountSender->id,
        'recipient_id' => $accountRecipient->id,
        'amount'       => 3500,
        'scheduled_at' => now()->addDays(1)->format('Y-m-d'), // Data no futuro
    ]);

    // Assert:
    $post->assertStatus(Response::HTTP_CREATED);
});

// Testa se a transferência falha quando o valor excede o saldo do remetente
test('fails when transfer amount exceeds sender balance', function () {
    // Arrange:
    $accountSender    = AccountBank::factory()->create(['balance' => 500]);
    $accountRecipient = AccountBank::factory()->create(['balance' => 8100]);

    // Act:
    $post = postJson(route('account-banks.transaction'), [
        'sender_id'    => $accountSender->id,
        'recipient_id' => $accountRecipient->id,
        'amount'       => 1000, // Valor que excede o saldo disponível
    ]);

    // Assert: Verifica se a resposta tem status HTTP 422 (Entidade não processável) e que há erros de validação no campo 'amount'
    $post->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
         ->assertJsonValidationErrors('amount');
});

// Testa se a requisição falha quando a conta do destinatário não existe
test('fails when recipient account does not exist', function () {
    // Arrange: Cria uma conta bancária para o remetente
    $accountSender = AccountBank::factory()->create(['balance' => 8000]);

    // Act: Envia uma requisição POST para a rota de transações com um ID de destinatário inválido
    $post = postJson(route('account-banks.transaction'), [
        'sender_id'    => $accountSender->id,
        'recipient_id' => 9999, // ID de destinatário inválido
        'amount'       => 1000,
    ]);

    // Assert: Verifica se a resposta tem status HTTP 422 (Entidade não processável) e que há erros de validação no campo 'recipient_id'
    $post->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
         ->assertJsonValidationErrors('recipient_id');
});

// Testa a validação quando o ID do remetente ou do destinatário é inválido
test('fails validation when sender or recipient ID is invalid', function () {
    // Arrange: Cria uma conta bancária para o destinatário
    $accountRecipient = AccountBank::factory()->create(['balance' => 8100]);

    // Act: Envia uma requisição POST com um ID de remetente inválido
    $post = postJson(route('account-banks.transaction'), [
        'sender_id'    => 9999, // ID de remetente inválido
        'recipient_id' => $accountRecipient->id,
        'amount'       => 1000,
    ]);

    // Assert: Verifica se a resposta tem status HTTP 422 (Entidade não processável) e que há erros de validação no campo 'sender_id'
    $post->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
         ->assertJsonValidationErrors('sender_id');
});

// Testa a validação de campos com valores inválidos
test('validation test', function (object $field) {
    // Arrange: Cria contas bancárias para remetente e destinatário
    $accountSender    = AccountBank::factory()->create(['balance' => 8000]);
    $accountRecipient = AccountBank::factory()->create(['balance' => 8100]);

    // Act: Envia uma requisição POST com valores inválidos para o campo especificado
    $post = postJson(route('account-banks.transaction'), [
        'sender_id'    => $accountSender->id,
        'recipient_id' => $accountRecipient->id,
        'amount'       => $field->value,
    ]);

    // Assert: Verifica se a resposta contém erros de validação para o campo especificado
    $post->assertJsonValidationErrors($field->rule);
})->with([
    'amount::min:0' => (object) ['name' => 'amount', 'value' => -5, 'rule' => 'amount'],
]);

// Testa se a transferência falha quando o valor excede o saldo do remetente
test('if account balance is enough to transfer', function () {
    // Arrange: Cria contas bancárias com saldo insuficiente para a transferência
    $accountSender    = AccountBank::factory()->create(['balance' => 8000]);
    $accountRecipient = AccountBank::factory()->create(['balance' => 8100]);

    $amountToSend = 8100; // Valor que excede o saldo do remetente

    // Act: Envia uma requisição POST para a rota de transações com um valor maior que o saldo disponível
    $post = postJson(route('account-banks.transaction'), [
        'sender_id'    => $accountSender->id,
        'recipient_id' => $accountRecipient->id,
        'amount'       => $amountToSend,
    ]);

    // Assert: Verifica se a resposta contém erros de validação para o campo 'amount'
    $post->assertJsonValidationErrors('amount');
});

// Testa o registro de um agendamento com uma data específica
it('should register schedule at the given time', function () {
    // Arrange: Cria contas bancárias para remetente e destinatário
    $accountSender    = AccountBank::factory()->create(['balance' => 8000]);
    $accountRecipient = AccountBank::factory()->create(['balance' => 8100]);

    // Act: Envia uma requisição POST com uma data de agendamento específica
    $post = postJson(route('account-banks.transaction'), [
        'sender_id'    => $accountSender->id,
        'recipient_id' => $accountRecipient->id,
        'amount'       => 3500,
        'scheduled_at' => '2001-05-20 20:01', // Data de agendamento específica
    ]);

    // Assert: Verifica se a resposta contém erros de validação para o campo 'scheduled_at'
    $post->assertJsonValidationErrors('scheduled_at');
});
