<?php

namespace App\Console\Commands;

use App\Services\AccountBankService;
use Illuminate\Console\Command;

class CreateBankAccountCommand extends Command
{
    protected $signature = 'bank-account:create {name : The name of the bank account}';

    protected $description = 'Create a new bank account with a unique name and zero balance';

    private AccountBankService $accountBankService;

    public function __construct(AccountBankService $accountBankService)
    {
        parent::__construct();
        $this->accountBankService = $accountBankService;
    }

    public function handle()
    {
        $name = $this->argument('name');

        try {
            $this->accountBankService->create([
                'name'    => $name,
                'balance' => 0.00,
            ]);

            $this->info("Conta Bancaria '{$name}' criada com sucesso.");
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
