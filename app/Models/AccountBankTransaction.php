<?php

namespace App\Models;

use App\Enums\TransactionStatusEnum;
use App\Observers\GlobalUUIDObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy([GlobalUUIDObserver::class])]
class AccountBankTransaction extends Model
{
    protected $fillable = [
        'sender_id',
        'recipient_id',
        'status',
        'authorized',
        'amount',
        'scheduled_at',
        'processed_at',
    ];

    protected $casts = [
        'id'           => 'string',
        'status'       => TransactionStatusEnum::class,
        'authorized'   => 'boolean',
        'amount'       => 'decimal:2',
        'scheduled_at' => 'datetime:Y-m-d H:i:s',
        'processed_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function authorizeTransaction()
    {
        $this->update([
            'authorized'   => true,
            'status'       => TransactionStatusEnum::Completed->value,
            'processed_at' => now(),
        ]);
    }

    public function markInsufficientBalance()
    {
        $this->update([
            'status' => TransactionStatusEnum::InsufficientBalance->value,
        ]);
    }

    public function markNotAuthorized()
    {
        $this->update([
            'status' => TransactionStatusEnum::NotAuthorized->value,
        ]);
    }
}
