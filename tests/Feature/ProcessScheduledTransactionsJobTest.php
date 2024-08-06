<?php

use App\Enums\TransactionStatusEnum;
use App\Jobs\{ProcessIndividualTransactionsJob, ProcessScheduledTransactionsJob};
use App\Models\{AccountBankTransaction};
use Carbon\Carbon;
use Illuminate\Support\Facades\{Queue};

// Verificar se a transação agendada é processada corretamente na data programada.
it('dispatches individual job instances for each scheduled transaction', function () {
    $transacoes = AccountBankTransaction::factory()->count(3)->create([
        'scheduled_at' => Carbon::now(),
        'status'       => TransactionStatusEnum::Pending,
        'processed_at' => null,
    ]);

    // Mock da facade Queue, despacha o job e após isso Verifica se o
    // ProcessIndividualTransactionsJob foi despachado para cada transação
    Queue::fake();

    $job = new ProcessScheduledTransactionsJob();
    $job->handle();

    foreach ($transacoes as $transacao) {
        Queue::assertPushed(ProcessIndividualTransactionsJob::class, function ($job) use ($transacao) {
            return $job->accountBankTransaction->is($transacao);
        });
    }
});
