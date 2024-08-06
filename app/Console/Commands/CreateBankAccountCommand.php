<?php

namespace App\Console\Commands;

use App\Services\AccountBankService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CreateBankAccountCommand extends Command
{
    /**
     * Nome e assinatura do comando.
     *
     * @var string
     */
    protected $signature = 'bank-account:create {name : Nome da conta bancária}';

    /**
     * Descrição do comando.
     *
     * @var string
     */
    protected $description = 'Cria uma nova conta bancária com saldo inicial de zero';

    /**
     * Instância do serviço de conta bancária.
     *
     * @var \App\Services\AccountBankService
     */
    private AccountBankService $accountBankService;

    /**
     * Cria uma nova instância do comando.
     *
     * @param \App\Services\AccountBankService $accountBankService Serviço de conta bancária
     */
    public function __construct(AccountBankService $accountBankService)
    {
        parent::__construct();
        $this->accountBankService = $accountBankService;
    }

    /**
     * Executa o comando para criar uma conta bancária.
     *
     * @return void
     */
    public function handle(): void
    {
        $name = $this->argument('name');

        try {
            $this->validateName($name);
            $this->accountBankService->create(['name' => $name, 'balance' => 0.00]);
            $this->info("Conta bancária '{$name}' criada com sucesso.");
        } catch (ValidationException $e) {
            $this->error('Erro de validação: ' . $e->getMessage());
        } catch (\Exception $e) {
            $this->error('Erro: ' . $e->getMessage());
        }
    }

    /**
     * Valida o nome da conta bancária.
     *
     * @param string $name
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     */
    private function validateName(string $name): void
    {
        $validator = Validator::make(['name' => $name], [
            'name' => nameRules(),
        ], messageRules());

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
