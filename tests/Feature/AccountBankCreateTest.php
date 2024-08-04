<?php

use Illuminate\Support\Str;

//  Teste de Criação com Dados Válidos
it('creates a bank account with a unique name and initial zero balance', function () {
    $uniqString = Str::uuid();
    $response   = $this->postJson('/api/account-banks', [
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
    $response = $this->postJson('/api/account-banks', [
        'balance' => 0.00,
    ]);

    $response->assertStatus(422)
             ->assertJsonValidationErrors('name');
});

// // Teste de Nome Duplicado
// it('does not allow duplicate names for bank accounts', function () {
//     // Cria a primeira conta
//     $this->postJson('/api/account-banks', [
//         'name' => 'Duplicate Account Name',
//         'balance' => 0.00,
//     ]);

//     // Tenta criar uma segunda conta com o mesmo nome
//     $response = $this->postJson('/api/account-banks', [
//         'name' => 'Duplicate Account Name',
//         'balance' => 0.00,
//     ]);

//     $response->assertStatus(422)
//              ->assertJsonValidationErrors('name');
// });
