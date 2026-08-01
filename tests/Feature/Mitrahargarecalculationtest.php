<?php

namespace Tests\Feature;

use App\Models\Mitra;
use App\Models\Pesanan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MitraHargaRecalculationTest extends TestCase
{
    use RefreshDatabase;

    private function submitHargaBaru(Mitra $mitra, float $hargaBaru)
    {
        $admin = User::factory()->admin()->create();

        return $this->actingAs($admin)->post(route('admin.mitra.update', $mitra->id), [
            'name' => $mitra->user->name,
            'email' => $mitra->user->email,
            'nama_perusahaan' => $mitra->nama_perusahaan,
            'no_hp' => $mitra->no_hp,
            'alamat' => $mitra->alamat,
            'harga_per_liter' => $hargaBaru,
        ]);
    }

    public function test_tagihan_ikut_berubah_untuk_pesanan_berstatus_menunggu(): void
    {
        $mitra = Mitra::factory()->create(['harga_per_liter' => 10000]);
        $pesanan = Pesanan::factory()->for($mitra)->create(['jumlah_bbm' => 1000]);
        $pembayaran = $pesanan->pembayaran()->create([
            'order_id' => 'PTIKK-TEST-1',
            'jumlah_tagihan' => 1000 * 10000,
            'status' => 'menunggu',
        ]);

        $this->submitHargaBaru($mitra, 15000)->assertRedirect(route('admin.mitra.index'));

        $this->assertEquals(1000 * 15000, $pembayaran->fresh()->jumlah_tagihan);
    }

    public function test_tagihan_ikut_berubah_untuk_pesanan_berstatus_ditolak(): void
    {
        $mitra = Mitra::factory()->create(['harga_per_liter' => 10000]);
        $pesanan = Pesanan::factory()->for($mitra)->create(['jumlah_bbm' => 500]);
        $pembayaran = $pesanan->pembayaran()->create([
            'order_id' => 'PTIKK-TEST-2',
            'jumlah_tagihan' => 500 * 10000,
            'status' => 'ditolak',
        ]);

        $this->submitHargaBaru($mitra, 12000);

        $this->assertEquals(500 * 12000, $pembayaran->fresh()->jumlah_tagihan);
    }

    public function test_tagihan_tidak_berubah_untuk_pesanan_yang_sudah_menunggu_verifikasi(): void
    {
        $mitra = Mitra::factory()->create(['harga_per_liter' => 10000]);
        $pesanan = Pesanan::factory()->for($mitra)->create(['jumlah_bbm' => 1000]);
        $pembayaran = $pesanan->pembayaran()->create([
            'order_id' => 'PTIKK-TEST-3',
            'jumlah_tagihan' => 1000 * 10000,
            'status' => 'menunggu_verifikasi',
        ]);

        $this->submitHargaBaru($mitra, 20000);

        // Nominal tetap pakai harga lama karena mitra sudah terlanjur transfer & upload bukti.
        $this->assertEquals(1000 * 10000, $pembayaran->fresh()->jumlah_tagihan);
    }

    public function test_tagihan_tidak_berubah_untuk_pesanan_yang_sudah_lunas(): void
    {
        $mitra = Mitra::factory()->create(['harga_per_liter' => 10000]);
        $pesanan = Pesanan::factory()->for($mitra)->create(['jumlah_bbm' => 1000]);
        $pembayaran = $pesanan->pembayaran()->create([
            'order_id' => 'PTIKK-TEST-4',
            'jumlah_tagihan' => 1000 * 10000,
            'status' => 'lunas',
        ]);

        $this->submitHargaBaru($mitra, 20000);

        $this->assertEquals(1000 * 10000, $pembayaran->fresh()->jumlah_tagihan);
    }

    public function test_mitra_lain_tidak_ikut_terpengaruh(): void
    {
        $mitraA = Mitra::factory()->create(['harga_per_liter' => 10000]);
        $mitraB = Mitra::factory()->create(['harga_per_liter' => 10000]);

        $pesananB = Pesanan::factory()->for($mitraB)->create(['jumlah_bbm' => 1000]);
        $pembayaranB = $pesananB->pembayaran()->create([
            'order_id' => 'PTIKK-TEST-5',
            'jumlah_tagihan' => 1000 * 10000,
            'status' => 'menunggu',
        ]);

        // Yang diubah harganya adalah mitra A, bukan mitra B.
        $this->submitHargaBaru($mitraA, 99000);

        $this->assertEquals(1000 * 10000, $pembayaranB->fresh()->jumlah_tagihan);
    }
}
