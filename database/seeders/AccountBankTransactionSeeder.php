<?php

namespace Database\Seeders;

use App\Models\AccountBankTransaction;
use Illuminate\Database\Seeder;

class AccountBankTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Obtém todos os IDs de contas bancárias existentes
     * Cria transações bancárias usando IDs referenciados
     */
    public function run(): void
    {
        AccountBankTransaction::factory()->count(100)->create();
    }
}
