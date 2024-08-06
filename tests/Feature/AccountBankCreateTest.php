<?php

use Illuminate\Support\Str;

//  Teste de Criação com Dados Válidos
it('creates a bank account with a unique name and initial zero balance', function () {
    $uniqString = Str::uuid();
    $response   = $this->postJson(route('account-banks.create'), [
        'name'    => "{$uniqString}-name",
        'balance' => 0.00,
    ]);

    $response->assertStatus(201)
             ->assertJsonStructure([
                 'data' => [
                     'id',
                     'name',
                     'balance',
                     'created_at',
                     'updated_at',
                 ],
             ])
             ->assertJson([
                 'data' => [
                     'name'    => "{$uniqString}-name",
                     'balance' => 0.00,
                 ],
             ]);

    $data = $response->json('data'); // Acessa o conteúdo dentro de 'data'
    $this->assertDatabaseHas('account_banks', [
        'id'      => $data['id'],
        'name'    => "{$uniqString}-name",
        'balance' => 0.00,
    ]);
});

// Teste de Validação do Nome
it('requires a name to create a bank account', function () {
    $response = $this->postJson(route('account-banks.create'), [
        'balance' => 0.00,
    ]);

    $response->assertStatus(422)
             ->assertJsonValidationErrors('name');
});

// testa conta com saldo negativo
it('rejects negative balance', function () {
    $response = $this->postJson(route('account-banks.create'), [
        'name'    => 'Account with Negative Balance',
        'balance' => -100.00,
    ]);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['balance']);
});

// Teste de criação de conta zerada
it('sets initial balance to zero if not provided', function () {
    $response = $this->postJson(route('account-banks.create'), [
        'name' => 'Account with Default Balance',
    ]);

    $response->assertStatus(201)
             ->assertJson([
                 'data' => [
                     'name'    => 'Account with Default Balance',
                     'balance' => 0, // O valor do saldo deve ser igual a zero
                 ],
             ]);

    $data = $response->json('data'); // Acessa o conteúdo dentro de 'data'
    $this->assertDatabaseHas('account_banks', [
        'id'      => $data['id'],
        'name'    => 'Account with Default Balance',
        'balance' => 0.00, // O saldo deve ser zero na base de dados
    ]);
});

// simula erro no servidor
it('handles internal server errors gracefully', function () {
    // Simula uma falha no servidor
    $this->mock(\App\Services\AccountBankService::class, function ($mock) {
        $mock->shouldReceive('create')->andThrow(new \Exception('Server error'));
    });

    $response = $this->postJson(route('account-banks.create'), [
        'name'    => 'Error Account',
        'balance' => 0.00,
    ]);

    $response->assertStatus(500); // Verifica o erro do servidor interno
});
