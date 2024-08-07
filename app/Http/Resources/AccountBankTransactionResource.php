<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Recurso para a representação de uma transação bancária.
 *
 * @mixin \App\Models\AccountBankTransaction
 */
class AccountBankTransactionResource extends JsonResource
{
    /**
     * Transforma o recurso em um array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id'           => $this->id,
            'amount'       => (float)$this->amount,
            'sender_id'    => $this->sender_id,
            'recipient_id' => $this->recipient_id,
            'status'       => $this->status->value,
            'scheduled_at' => $this->scheduled_at ? $this->scheduled_at->toDateString() : null,
            'processed_at' => $this->processed_at ? $this->processed_at->toDateString() : null,
            'created_at'   => $this->created_at->toDateTimeString(),
            'updated_at'   => $this->updated_at->toDateTimeString(),
        ];
    }
}
