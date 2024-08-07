<?php

namespace App\Models;

use App\Enums\TransactionStatusEnum;
use App\Observers\GlobalUUIDObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

/**
 * Modelo que representa uma transação bancária.
 *
 * @property string $id
 * @property string $sender_id
 * @property string $recipient_id
 * @property \App\Enums\TransactionStatusEnum $status
 * @property float $amount
 * @property \Carbon\Carbon|null $scheduled_at
 * @property \Carbon\Carbon|null $processed_at
 */
#[ObservedBy([GlobalUUIDObserver::class])]
class AccountBankTransaction extends SuperModel
{
    /**
     * Atributos que podem ser atribuídos em massa.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'sender_id',
        'recipient_id',
        'amount',
        'scheduled_at',
    ];

    /**
     * Casts para conversão de atributos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status'       => TransactionStatusEnum::class,
        'amount'       => 'decimal:2',
        'scheduled_at' => 'datetime:Y-m-d H:i:s',
        'processed_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * Obtém o atributo de status.
     *
     * @param  mixed  $value
     * @return \App\Enums\TransactionStatusEnum
     */
    public function getStatusAttribute($value)
    {
        // Retorna o status padrão se o valor for nulo ou inválido
        return TransactionStatusEnum::tryFrom($value) ?? TransactionStatusEnum::Pending;
    }

    /**
     * Autoriza a transação e atualiza o status e a data de processamento.
     *
     * @return void
     */
    public function authorizeTransaction()
    {
        $this->update([
            'processed_at' => now(),
            'status'       => TransactionStatusEnum::Completed->value,
        ]);
    }

    /**
     * Marca a transação como não autorizada devido a saldo insuficiente.
     *
     * @return void
     */
    public function markInsufficientBalance()
    {
        $this->update([
            'status' => TransactionStatusEnum::InsufficientBalance->value,
        ]);
    }

    /**
     * Marca a transação como não autorizada.
     *
     * @return void
     */
    public function markNotAuthorized()
    {
        $this->update([
            'status' => TransactionStatusEnum::NotAuthorized->value,
        ]);
    }
}
