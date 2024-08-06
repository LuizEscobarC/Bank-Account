<?php

namespace Database\Factories;

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
            'status'       => $this->faker->randomElement([
                'pending', 'completed', 'insufficient-balance', 'not-authorized',
            ]),
            'amount'       => $this->faker->randomFloat(2, 0, 10000),
            'scheduled_at' => $this->faker->dateTimeBetween('now', '+1 week')->format('Y-m-d H:i:s'),
            'processed_at' => $this->faker->dateTimeBetween('now', '+1 week')->format('Y-m-d H:i:s'),
        ];
    }
}
