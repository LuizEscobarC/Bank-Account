<?php

namespace Database\Factories;

use App\Enums\TransactionStatusEnum;
use App\Models\{AccountBank, AccountBankTransaction};
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AccountBankTransaction>
 */
class AccountBankTransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $sender    = AccountBank::factory()->create();
        $recipient = AccountBank::factory()->create();

        return [
            'sender_id'    => $sender->id,
            'recipient_id' => $recipient->id,
            'status'       => TransactionStatusEnum::Pending->value,
            'amount'       => $this->faker->randomFloat(2, 0, 10000),
            'scheduled_at' => now(),
        ];
    }
}
