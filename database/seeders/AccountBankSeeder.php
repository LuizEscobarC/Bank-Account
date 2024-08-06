<?php

namespace Database\Seeders;

use App\Models\AccountBank;
use Illuminate\Database\Seeder;

class AccountBankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AccountBank::factory()->count(100)->create();
    }
}
