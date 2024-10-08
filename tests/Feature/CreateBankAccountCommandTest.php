<?php

use Illuminate\Support\Facades\{Artisan, DB};

// testede criação de conta via CLI
it('creates a bank account via CLI', function () {
    $name = 'Unique Account Name';

    Artisan::call('bank-account:create', ['name' => $name]);

    expect(DB::table('account_banks')->where('name', $name)->where('balance', 0.00)->exists())->toBeTrue();
});

// teste de criação sem nome
it('fails to create a bank account without a name', function () {
    Artisan::call('bank-account:create', ['name' => '']);
    expect(Artisan::output())->toContain('O nome é obrigatório.');
});

// teste de nome com espaços
it('does not allow creating a bank account with only whitespace in the name', function () {
    $name = '       ';

    Artisan::call('bank-account:create', ['name' => $name]);

    expect(Artisan::output())->toContain('O nome é obrigatório.');
});

// teste especial chars
it('does not allow creating a bank account with special characters in the name', function () {
    $name = '@InvalidName!'; // Nome com caracteres especiais

    Artisan::call('bank-account:create', ['name' => $name]);
    expect(Artisan::output())->toContain('Não é permitido caracteres especiais.'); // Ajuste a mensagem conforme o tratamento de caracteres especiais
});
