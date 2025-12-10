<?php

namespace Database\Factories;

use App\Enums\UserType;
use App\Models\User;
use App\Support\DocumentGenerator;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $firstName = fake()->firstName();
        $lastName = fake()->lastName();

        $type = $this->faker->randomElement([
            UserType::Common,
            UserType::Merchant,
        ]);

        return [
            'name' => $firstName,
            'full_name' => "{$firstName} {$lastName}",
            'document' => DocumentGenerator::forType($type),
            'type' => $type,
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Configure the model factory.
     * Este método é chamado SEMPRE que a factory cria um model
     */
    public function configure(): static
    {
        return $this->afterCreating(function (User $user) {
            // Criar wallet se não existir
            if (! $user->wallet()->exists()) {
                $user->wallet()->create([
                    'balance' => 0,
                ]);
            }
        });
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function common(): static
    {
        return $this->state(function () {
            return [
                'type' => UserType::Common,
                'document' => DocumentGenerator::cpf(),
            ];
        });
    }

    public function merchant(): static
    {
        return $this->state(function () {
            return [
                'type' => UserType::Merchant,
                'document' => DocumentGenerator::cnpj(),
            ];
        });
    }
}
