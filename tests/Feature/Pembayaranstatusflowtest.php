<?php

namespace Tests\Feature;

use App\Models\Mitra;
use App\Models\Pesanan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PembayaranStatusFlowTest extends TestCase
{
    use RefreshDatabase;

    private function buatMitraDenganPesanan(): array
    {
        $userMitra = User::factory()->create();
        $mitra = Mitra::factory()->create(['user_id' => $userMitra->id]);
        $pesanan = Pesanan::factory()->for($mitra)->create();
        $pembayaran = $pesanan->pembayaran()->create([
            'order_id' => 'PTIKK-TEST-' . $pesanan->id,
            'jumlah_tagihan' => 10000000,
            'status' => 'menunggu',
        ]);

        return [$userMitra, $mitra, $pesanan, $pembayaran];
    }

    public function test_mitra_upload_bukti_transfer_mengubah_status_jadi_menunggu_verifikasi(): void
    {
        Storage::fake('local');
        [$userMitra, , $pesanan, $pembayaran] = $this->buatMitraDenganPesanan();

        $response = $this->actingAs($userMitra)->post(route('mitra.pembayaran.upload', $pesanan->id), [
            'bukti_transfer' => UploadedFile::fake()->image('bukti.jpg'),
        ]);

        $response->assertRedirect(route('mitra.pesanan.index'));
        $this->assertEquals('menunggu_verifikasi', $pembayaran->fresh()->status);
        Storage::disk('local')->assertExists($pembayaran->fresh()->bukti_transfer);
    }

    public function test_admin_verifikasi_mengubah_status_jadi_lunas(): void
    {
        [, , , $pembayaran] = $this->buatMitraDenganPesanan();
        $pembayaran->update(['status' => 'menunggu_verifikasi']);
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.pembayaran.verifikasi', $pembayaran->id));

        $response->assertRedirect(route('admin.pesanan.index'));
        $pembayaran->refresh();
        $this->assertEquals('lunas', $pembayaran->status);
        $this->assertEquals($admin->id, $pembayaran->diverifikasi_oleh);
        $this->assertNotNull($pembayaran->diverifikasi_at);
    }

    public function test_admin_tolak_mengubah_status_jadi_ditolak_dengan_catatan(): void
    {
        [, , , $pembayaran] = $this->buatMitraDenganPesanan();
        $pembayaran->update(['status' => 'menunggu_verifikasi']);
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.pembayaran.tolak', $pembayaran->id), [
            'catatan_admin' => 'Nominal tidak sesuai tagihan.',
        ]);

        $response->assertRedirect(route('admin.pesanan.index'));
        $pembayaran->refresh();
        $this->assertEquals('ditolak', $pembayaran->status);
        $this->assertEquals('Nominal tidak sesuai tagihan.', $pembayaran->catatan_admin);
    }

    public function test_mitra_tidak_bisa_upload_ulang_kalau_sudah_lunas(): void
    {
        Storage::fake('local');
        [$userMitra, , $pesanan, $pembayaran] = $this->buatMitraDenganPesanan();
        $pembayaran->update(['status' => 'lunas']);

        $response = $this->actingAs($userMitra)->post(route('mitra.pembayaran.upload', $pesanan->id), [
            'bukti_transfer' => UploadedFile::fake()->image('bukti-lagi.jpg'),
        ]);

        $response->assertRedirect(route('mitra.pesanan.index'));
        $response->assertSessionHas('error');
        $this->assertEquals('lunas', $pembayaran->fresh()->status);
    }
}
