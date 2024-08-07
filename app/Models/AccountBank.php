<?php

namespace App\Models;

use App\Observers\GlobalUUIDObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

/**
 * Modelo que representa uma conta bancária.
 *
 * @property string $id
 * @property string $name
 * @property float $balance
 */
#[ObservedBy([GlobalUUIDObserver::class])]
class AccountBank extends SuperModel
{
    /**
     * Atributos que podem ser atribuídos em massa.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'name',
        'balance',
    ];

    /**
     * Casts para conversão de atributos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'balance' => 'decimal:2',
    ];

    /**
     * Incrementa o saldo da conta bancária.
     *
     * @param float $amount Valor a ser incrementado.
     * @return void
     */
    public function incrementBalance(float $amount): void
    {
        $this->increment('balance', $amount);
    }

    /**
     * Decrementa o saldo da conta bancária.
     *
     * @param float $amount Valor a ser decrementado.
     * @return void
     */
    public function decrementBalance(float $amount): void
    {
        $this->decrement('balance', $amount);
    }
}
