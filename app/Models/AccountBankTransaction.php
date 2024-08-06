<?php

namespace App\Models;

use App\Enums\TransactionStatusEnum;
use App\Observers\GlobalUUIDObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([GlobalUUIDObserver::class])]
class AccountBankTransaction extends SuperModel
{
    protected $fillable = [
        'sender_id',
        'recipient_id',
        'status',
        'amount',
        'scheduled_at',
        'processed_at',
    ];

    protected $casts = [
        'status'       => TransactionStatusEnum::class,
        'amount'       => 'decimal:2',
        'scheduled_at' => 'datetime:Y-m-d H:i:s',
        'processed_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * Get the status attribute.
     *
     * @return \App\Enums\TransactionStatusEnum
     */
    public function getStatusAttribute($value)
    {
        // Return a default value or handle the null case
        return TransactionStatusEnum::tryFrom($value) ?? TransactionStatusEnum::Pending;
    }

    public function authorizeTransaction()
    {
        $this->update([
            'processed_at' => now(),
            'status'       => TransactionStatusEnum::Completed->value,
        ]);
    }

    /**
     *
     * Not Authorized
     *
     */
    public function markInsufficientBalance()
    {
        $this->update([
            'status' => TransactionStatusEnum::InsufficientBalance->value,
        ]);
    }

    /**
     * Not Authorized
     */
    public function markNotAuthorized()
    {
        $this->update([
            'status' => TransactionStatusEnum::NotAuthorized->value,
        ]);
    }
}
