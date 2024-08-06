<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\AccountBankTransaction
 */
class AccountBankTransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'           => $this->id,
            'amount'       => (float)$this->amount,
            'sender_id'    => $this->sender_id,
            'recipient_id' => $this->recipient_id,
            'status'       => $this->status->value,
            'scheduled_at' => $this->scheduled_at ? $this->scheduled_at->toDateString() : null,
            'created_at'   => $this->created_at->toDateTimeString(),
            'updated_at'   => $this->updated_at->toDateTimeString(),
        ];
    }
}
