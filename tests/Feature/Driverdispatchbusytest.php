<?php

namespace Tests\Feature;

use App\Models\Armada;
use App\Models\Driver;
use App\Models\Mitra;
use App\Models\Pengiriman;
use App\Models\Pesanan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DriverDispatchBusyTest extends TestCase
{
    use RefreshDatabase;

    private function pesananLunas(): Pesanan
    {
        $mitra = Mitra::factory()->create();
        $pesanan = Pesanan::factory()->for($mitra)->create();
        $pesanan->pembayaran()->create([
            'order_id' => 'PTIKK-TEST-' . $pesanan->id,
            'jumlah_tagihan' => 10000000,
            'status' => 'lunas',
        ]);

        return $pesanan;
    }

    public function test_driver_yang_sedang_bertugas_ditandai_disabled_di_form_dispatch(): void
    {
        $admin = User::factory()->admin()->create();
        $driverSibuk = Driver::factory()->create();
        $driverBebas = Driver::factory()->create();

        // driverSibuk sedang menjalankan pengiriman lain (status proses).
        Pengiriman::factory()->create([
            'driver_id' => $driverSibuk->id,
            'status' => 'proses',
        ]);

        $pesananBaru = $this->pesananLunas();

        $response = $this->actingAs($admin)->get(route('admin.pesanan.dispatch.form', $pesananBaru->id));

        $response->assertOk();
        $response->assertViewHas('busyDriverIds', function ($busyDriverIds) use ($driverSibuk, $driverBebas) {
            return in_array($driverSibuk->id, $busyDriverIds)
                && !in_array($driverBebas->id, $busyDriverIds);
        });
    }

    public function test_driver_yang_sedang_bertugas_ditolak_saat_dijadwalkan_ke_pesanan_lain(): void
    {
        $admin = User::factory()->admin()->create();
        $driverSibuk = Driver::factory()->create();
        $armada = Armada::factory()->create();

        Pengiriman::factory()->create([
            'driver_id' => $driverSibuk->id,
            'status' => 'proses',
        ]);

        $pesananBaru = $this->pesananLunas();

        $response = $this->actingAs($admin)->post(route('admin.pesanan.dispatch', $pesananBaru->id), [
            'driver_id' => $driverSibuk->id,
            'armada_id' => $armada->id,
            'tanggal_kirim' => now()->addDay()->toDateString(),
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('pengirimen', [
            'pesanan_id' => $pesananBaru->id,
        ]);
    }

    public function test_driver_yang_bebas_tetap_bisa_dijadwalkan(): void
    {
        $admin = User::factory()->admin()->create();
        $driverBebas = Driver::factory()->create();
        $armada = Armada::factory()->create();

        $pesananBaru = $this->pesananLunas();

        $response = $this->actingAs($admin)->post(route('admin.pesanan.dispatch', $pesananBaru->id), [
            'driver_id' => $driverBebas->id,
            'armada_id' => $armada->id,
            'tanggal_kirim' => now()->addDay()->toDateString(),
        ]);

        $response->assertRedirect(route('admin.pesanan.index'));
        $this->assertDatabaseHas('pengirimen', [
            'pesanan_id' => $pesananBaru->id,
            'driver_id' => $driverBebas->id,
            'status' => 'proses',
        ]);
    }
}
