<?php

namespace Database\Seeders;

use App\Models\AccountBankTransaction;
use Illuminate\Database\Seeder;

class AccountBankTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Cria transações bancárias usando IDs
     */
    public function run(): void
    {
        AccountBankTransaction::factory()->count(100)->create();
    }
}
