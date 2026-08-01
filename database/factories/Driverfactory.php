<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Driver>
 */
class DriverFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->driver(),
            'no_hp' => fake()->numerify('08##########'),
            'alamat' => fake()->address(),
            'status' => 'aktif',
        ];
    }

    public function nonaktif(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'nonaktif',
        ]);
    }
}
