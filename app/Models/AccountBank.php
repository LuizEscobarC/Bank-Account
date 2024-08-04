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
}
