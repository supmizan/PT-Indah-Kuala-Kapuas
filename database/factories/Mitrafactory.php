<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Mitra>
 */
class MitraFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'nama_perusahaan' => fake()->company(),
            'alamat' => fake()->address(),
            'latitude' => fake()->latitude(-1, 1),
            'longitude' => fake()->longitude(108, 110),
            'no_hp' => fake()->numerify('08##########'),
            'harga_per_liter' => 10000,
        ];
    }
}
