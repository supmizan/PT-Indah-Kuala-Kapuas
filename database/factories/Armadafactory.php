<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Armada>
 */
class ArmadaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'kode_armada' => 'ARM-' . fake()->unique()->numerify('###'),
            'no_polisi' => strtoupper(fake()->unique()->bothify('B #### ??')),
            'jenis' => fake()->randomElement(['Truk Tangki', 'Pickup Tangki']),
            'kapasitas' => fake()->randomElement([5000, 8000, 10000]),
            'status' => 'aktif',
        ];
    }
}
