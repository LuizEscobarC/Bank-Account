<?php

namespace Database\Factories;

use App\Models\AccountBank;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AccountBankTransaction>
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
        $senderId    = AccountBank::factory()->create();
        $recipientId = AccountBank::factory()->create();

        return [
            'sender_id'    => $senderId,
            'recipient_id' => $recipientId,
            'amount'       => fake()->randomFloat(2, 0, 10000),
            'scheduled_at' => fake()->optional()->dateTime,
        ];
    }
}
