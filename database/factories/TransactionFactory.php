<?php

namespace Database\Factories;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'payer_id' => User::factory()->common(),
            'payee_id' => User::factory(),
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'status' => TransactionStatus::Pending,
            'type' => TransactionType::Transfer,
            'description' => $this->faker->optional()->sentence(),
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TransactionStatus::Completed,
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TransactionStatus::Pending,
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TransactionStatus::Failed,
        ]);
    }

    public function withAmount(float $amount): static
    {
        return $this->state(fn (array $attributes) => [
            'amount' => $amount,
        ]);
    }

    public function transfer(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => TransactionType::Transfer,
            'payer_id' => User::factory()->common(),
            'payee_id' => User::factory(),
        ]);
    }

    public function deposit(): static
    {
        $user = User::factory()->create();

        return $this->state(fn (array $attributes) => [
            'type' => TransactionType::Deposit,
            'payer_id' => $user->id,
            'payee_id' => $user->id,
            'description' => 'Depósito',
        ]);
    }

    public function withdrawal(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => TransactionType::Withdrawal,
            'description' => 'Saque',
        ]);
    }
}
