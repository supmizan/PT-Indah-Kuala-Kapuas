<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'pembayarans';

    protected $fillable = [
        'pesanan_id',
        'order_id',
        'jumlah_tagihan',
        'status',
        'metode_pembayaran',
        'bukti_transfer',
        'diverifikasi_oleh',
        'diverifikasi_at',
        'catatan_admin',
    ];

    protected $casts = [
        'diverifikasi_at' => 'datetime',
        'jumlah_tagihan' => 'decimal:2',
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id');
    }

    public function verifikator()
    {
        return $this->belongsTo(\App\Models\User::class, 'diverifikasi_oleh');
    }

    public function isLunas(): bool
    {
        return $this->status === 'lunas';
    }
}
