<?php

use App\Enums\TransactionStatusEnum;
use App\Jobs\{
    ProcessIndividualTransactionsJob,
    ProcessScheduledTransactionsJob};
use App\Models\{
    AccountBank,
    AccountBankTransaction
};
use App\Services\AccountBankTransactionService;
use App\Services\Auth\AuthService;
use Carbon\Carbon;
use Illuminate\Support\Facades\{
    DB,
    Queue
};

it('dispatches individual job instances for each scheduled transaction', function () {
    $transacoes = AccountBankTransaction::factory()->count(3)->create([
        'scheduled_at' => Carbon::now(),
        'status'       => TransactionStatusEnum::Pending->value,
        'processed_at' => null,
    ]);

    // Mock do AccountBankTransactionService
    $serviceMock = Mockery::mock(AccountBankTransactionService::class);
    $serviceMock->shouldReceive('updateAccountsBalance')->andReturn(true);

    // Mock do serviço de autenticação
    $authServiceMock = Mockery::mock(AuthService::class);

    // Mock da facade Queue, despacha o job e após isso verifica se o
    // ProcessIndividualTransactionsJob foi despachado para cada transação
    Queue::fake();

    $job = new ProcessScheduledTransactionsJob($serviceMock, $authServiceMock);
    $job->handle();

    foreach ($transacoes as $transacao) {
        Queue::assertPushed(ProcessIndividualTransactionsJob::class, function ($job) use ($transacao) {
            return $job->accountBankTransaction->is($transacao);
        });
    }
});

// Testar se a conta remetente tem saldo suficiente na data de execução agendada.
it('checks if the account of the sender has sufficient funds on the scheduled execution date', function () {
    $contaRemetente    = AccountBank::factory()->create(['balance' => 1000.00]);
    $contaDestinatario = AccountBank::factory()->create(['balance' => 500.00]);

    $transacao = AccountBankTransaction::factory()->create([
        'amount'       => 100.00,
        'sender_id'    => $contaRemetente->id,
        'recipient_id' => $contaDestinatario->id,
        'scheduled_at' => now(),
        'status'       => TransactionStatusEnum::Pending->value,
        'processed_at' => null,
    ]);

    // Mock do serviço AccountBankTransactionService para simular a lógica de
    // transação de conta bancária com agendamento
    $serviceMock = Mockery::mock(AccountBankTransactionService::class);
    $serviceMock->shouldReceive('updateAccountsBalance')->once()->with([
        'amount'       => $transacao->amount,
        'sender_id'    => $transacao->sender_id,
        'recipient_id' => $transacao->recipient_id,
    ], $transacao)->andReturnUsing(function ($data, $transacao) use ($contaRemetente) {
        $saldo = AccountBank::find($transacao->sender_id)->balance;

        if ($saldo < $transacao->amount) {
            $transacao->update(['status' => TransactionStatusEnum::InsufficientBalance->value]);

            return false;
        }

        DB::transaction(function () use ($data, $transacao) {
            $remetente    = AccountBank::findOrFail($data['sender_id']);
            $destinatario = AccountBank::findOrFail($data['recipient_id']);

            $remetente->decrement('balance', $data['amount']);
            $destinatario->increment('balance', $data['amount']);

            $transacao->update(['processed_at' => now(), 'status' => TransactionStatusEnum::Completed->value]);
        });

        return true;
    });

    // Despacha o job de transação individual e verifica se a transação
    // foi processada e os saldos foram atualizados corretamente
    $job = new ProcessIndividualTransactionsJob($transacao, $serviceMock);
    $job->handle();

    $transacao->refresh();
    $contaRemetente->refresh();
    $contaDestinatario->refresh();

    expect($transacao->status)->toBe(TransactionStatusEnum::Completed);
    expect((float)$contaRemetente->balance)->toBe(900.00); // 1000.00 - 100.00
    expect((float)$contaDestinatario->balance)->toBe(600.00); // 500.00 + 100.00
});
