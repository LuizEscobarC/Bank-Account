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
                'id',
                'name',
                'balance',
                'created_at',
                'updated_at',
            ])
             ->assertJson([
                 'name'    => "{$uniqString}-name",
                 'balance' => '0.00',
             ]);

    $this->assertDatabaseHas('account_banks', [
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

// Teste de Nome Duplicado
it('does not allow duplicate names for bank accounts', function () {
    $this->postJson(route('account-banks.create'), [
        'name'    => 'Duplicate Account Name',
        'balance' => 0.00,
    ]);

    // Tenta criar uma segunda conta com o mesmo nome
    $response = $this->postJson(route('account-banks.create'), [
        'name'    => 'Duplicate Account Name',
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
                 'name'    => 'Account with Default Balance',
                 'balance' => 0,
             ]);

    $data = $response->json();
    $this->assertDatabaseHas('account_banks', [
        'id'      => $data['id'],
        'name'    => 'Account with Default Balance',
        'balance' => 0.00,
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
