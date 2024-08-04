<?php

namespace App\Models;

use App\Observers\GlobalUUIDObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([GlobalUUIDObserver::class])]
class AccountBankTransaction extends SuperModel
{
    protected $fillable = [
        'sender_id', 'recipient_id', 'amount', 'scheduled_at',
    ];

    protected $casts = [
        'id'           => 'string',
        'amount'       => 'decimal:2',
        'scheduled_at' => 'datetime',
        'authorized'   => 'boolean',
    ];
}
