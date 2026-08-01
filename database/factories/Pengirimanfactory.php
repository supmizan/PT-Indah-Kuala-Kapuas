<?php

namespace Database\Factories;

use App\Models\Armada;
use App\Models\Driver;
use App\Models\Pesanan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Pengiriman>
 */
class PengirimanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'pesanan_id' => Pesanan::factory(),
            'driver_id' => Driver::factory(),
            'armada_id' => Armada::factory(),
            'tanggal_kirim' => now()->addDay()->toDateString(),
            'status' => 'proses',
        ];
    }

    public function selesai(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'selesai',
        ]);
    }
}
