<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Armada;
use App\Models\Driver;
use App\Models\Mitra;
use App\Models\Pesanan;
use App\Models\Pengiriman;
use App\Models\Tracking;
use App\Models\Laporan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Admin
        User::create([
            'name' => 'Admin Mizan',
            'email' => 'admin@ikk.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // 2. Mitra
        $mitraData = [
            [
                'name' => 'Mitra Borneo',
                'email' => 'borneo@mitra.com',
                'company' => 'PT Borneo Logistik',
                'address' => 'Jl. Ahmad Yani No. 12, Pontianak',
                'phone' => '081234567890',
                'lat' => -0.0393,
                'lng' => 109.3421,
            ],
            [
                'name' => 'Mitra Khatulistiwa',
                'email' => 'khatulistiwa@mitra.com',
                'company' => 'CV Khatulistiwa Jaya',
                'address' => 'Jl. Gajah Mada No. 45, Kubu Raya',
                'phone' => '082345678901',
                'lat' => -0.0270,
                'lng' => 109.3500,
            ],
            [
                'name' => 'Mitra Kapuas',
                'email' => 'kapuas@mitra.com',
                'company' => 'PT Kapuas Raya',
                'address' => 'Jl. Tanjungpura No. 89, Pontianak',
                'phone' => '083456789012',
                'lat' => -0.0157,
                'lng' => 109.3389,
            ]
        ];

        $mitraModels = [];
        foreach ($mitraData as $md) {
            $user = User::create([
                'name' => $md['name'],
                'email' => $md['email'],
                'password' => Hash::make('password'),
                'role' => 'mitra',
            ]);

            $mitraModels[] = Mitra::create([
                'user_id' => $user->id,
                'nama_perusahaan' => $md['company'],
                'alamat' => $md['address'],
                'latitude' => $md['lat'],
                'longitude' => $md['lng'],
                'no_hp' => $md['phone'],
            ]);
        }

        // 3. Driver
        $driverData = [
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@driver.com',
                'phone' => '085234567811',
                'address' => 'Jl. Paris 2, Pontianak'
            ],
            [
                'name' => 'Agus Wijaya',
                'email' => 'agus@driver.com',
                'phone' => '085234567822',
                'address' => 'Jl. Sungai Raya Dalam, Kubu Raya'
            ],
            [
                'name' => 'Hendra Wijaya',
                'email' => 'hendra@driver.com',
                'phone' => '085234567833',
                'address' => 'Jl. Danau Sentarum, Pontianak'
            ]
        ];

        $driverModels = [];
        foreach ($driverData as $dd) {
            $user = User::create([
                'name' => $dd['name'],
                'email' => $dd['email'],
                'password' => Hash::make('password'),
                'role' => 'driver',
            ]);

            $driverModels[] = Driver::create([
                'user_id' => $user->id,
                'no_hp' => $dd['phone'],
                'alamat' => $dd['address'],
                'status' => 'aktif',
            ]);
        }

        // 4. Armada
        $armadas = [
            Armada::create([
                'kode_armada' => 'ARM-001',
                'no_polisi' => 'KB 1234 AA',
                'jenis' => 'Tangki Fuso Hino',
                'kapasitas' => 8000,
                'status' => 'digunakan'
            ]),
            Armada::create([
                'kode_armada' => 'ARM-002',
                'no_polisi' => 'KB 5678 BB',
                'jenis' => 'Tangki Isuzu Giga',
                'kapasitas' => 10000,
                'status' => 'aktif'
            ]),
            Armada::create([
                'kode_armada' => 'ARM-003',
                'no_polisi' => 'KB 9012 CC',
                'jenis' => 'Tangki Mitsubishi Colt',
                'kapasitas' => 5000,
                'status' => 'aktif'
            ]),
            Armada::create([
                'kode_armada' => 'ARM-004',
                'no_polisi' => 'KB 3456 DD',
                'jenis' => 'Tangki Toyota Dyna',
                'kapasitas' => 8000,
                'status' => 'maintenance'
            ]),
        ];

        // 5. Pesanan & Pengiriman Samples
        // Pesanan 1 (Selesai)
        $p1 = Pesanan::create([
            'mitra_id' => $mitraModels[0]->id,
            'tanggal' => now()->subDays(2)->format('Y-m-d'),
            'jumlah_bbm' => 5000,
            'status' => 'selesai'
        ]);

        $d1 = Pengiriman::create([
            'pesanan_id' => $p1->id,
            'driver_id' => $driverModels[0]->id,
            'armada_id' => $armadas[2]->id, // Mitsubishi Colt 5000L
            'tanggal_kirim' => now()->subDays(2)->format('Y-m-d'),
            'status' => 'selesai'
        ]);

        Laporan::create([
            'pengiriman_id' => $d1->id,
            'keterangan' => 'Pengangkutan BBM 5000 Liter selesai dan telah diterima dengan baik oleh PT Borneo Logistik. Dokumen serah terima terlampir.'
        ]);

        // Pesanan 2 (Sedang diproses / dikirim)
        $p2 = Pesanan::create([
            'mitra_id' => $mitraModels[1]->id,
            'tanggal' => now()->format('Y-m-d'),
            'jumlah_bbm' => 8000,
            'status' => 'diproses'
        ]);

        $d2 = Pengiriman::create([
            'pesanan_id' => $p2->id,
            'driver_id' => $driverModels[1]->id,
            'armada_id' => $armadas[0]->id, // KB 1234 AA (status digunakan)
            'tanggal_kirim' => now()->format('Y-m-d'),
            'status' => 'proses'
        ]);

        // Add trackings for active delivery
        Tracking::create([
            'pengiriman_id' => $d2->id,
            'latitude' => -0.0247,
            'longitude' => 109.3425,
            'waktu' => now()->subMinutes(10)
        ]);
        Tracking::create([
            'pengiriman_id' => $d2->id,
            'latitude' => -0.0270,
            'longitude' => 109.3500,
            'waktu' => now()
        ]);

        // Pesanan 3 (Pending)
        Pesanan::create([
            'mitra_id' => $mitraModels[2]->id,
            'tanggal' => now()->addDay()->format('Y-m-d'),
            'jumlah_bbm' => 8000,
            'status' => 'pending'
        ]);
    }
}