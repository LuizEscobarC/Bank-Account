<?php

namespace App\Console\Commands;

use App\Services\AccountBankService;
use App\Traits\ValidatesName;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CreateBankAccountCommand extends Command
{
    use ValidatesName;

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
        try {
            $name = $this->argument('name');

            $this->validateName($name);

            $this->accountBankService->create([
                'name'    => $name,
                'balance' => 0.00,
            ]);

            $this->info("Conta Bancaria '{$name}' criada com sucesso.");
        } catch(\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    private function validateName(string $name)
    {
        $validator = Validator::make(['name' => $name], [
            'name' => $this->nameRules(),
        ], $this->messageRules());

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
