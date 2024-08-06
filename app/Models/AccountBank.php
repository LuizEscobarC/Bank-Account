<?php

namespace App\Models;

use App\Observers\GlobalUUIDObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([GlobalUUIDObserver::class])]
class AccountBank extends SuperModel
{
    protected $fillable = [
        'name',
        'balance',
    ];

    protected $casts = [
        'id'      => 'string',
        'balance' => 'decimal:2',
    ];

    public function incrementBalance(float $amount): void
    {
        $this->increment('balance', $amount);
    }

    public function decrementBalance(float $amount): void
    {
        $this->decrement('balance', $amount);
    }
}
