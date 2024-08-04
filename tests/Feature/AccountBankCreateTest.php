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
