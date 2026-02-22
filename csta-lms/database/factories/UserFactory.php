<?php

namespace Database\Factories;

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
        return [
            'id_number'      => fake()->unique()->numerify('STU-######'),
            'full_name'      => fake()->name(),
            'contact_number' => fake()->phoneNumber(),
            'role'           => 'student',
            'status'         => true,
            'password'       => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    public function teacher(): static
    {
        return $this->state(fn (array $attributes) => [
            'id_number' => fake()->unique()->numerify('TCH-######'),
            'role'      => 'teacher',
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'id_number' => fake()->unique()->numerify('ADM-######'),
            'role'      => 'admin',
        ]);
    }
}
