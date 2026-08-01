<?php

namespace Database\Factories;

use App\Models\Pesanan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Pembayaran>
 */
class PembayaranFactory extends Factory
{
    public function definition(): array
    {
        return [
            'pesanan_id' => Pesanan::factory(),
            'order_id' => 'PTIKK-TEST-' . fake()->unique()->numerify('########'),
            'jumlah_tagihan' => 10000000,
            'status' => 'menunggu',
        ];
    }
}
