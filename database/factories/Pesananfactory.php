<?php

namespace Database\Factories;

use App\Models\Mitra;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Pesanan>
 */
class PesananFactory extends Factory
{
    public function definition(): array
    {
        return [
            'mitra_id' => Mitra::factory(),
            'tanggal' => now()->addDay()->toDateString(),
            'jumlah_bbm' => 1000,
            'status' => 'pending',
        ];
    }
}
