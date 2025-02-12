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
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'verified_at' => $verified = fake()->boolean(60) ? now() : null,
            'approved_at' => $verified ? (fake()->boolean(60) ? $verified : null) : null,
        ];
    }

    /**
     * Default superuser user account.
     */
    public function superuser(): static
    {
        return $this->state(fn () => [
            'name' => 'Superuser',
            'email' => 'superuser@local.dev',
            'role' => 'admin',
            'verified_at' => 1,
            'approved_at' => 1,
            'password' => '$2y$12$.jM7SD37qQAvDhmCHz414uToHIWwl9129xyMTgbDXlT8/KvKfXxU.',
            'remember_token' => null,
        ]);
    }
}
