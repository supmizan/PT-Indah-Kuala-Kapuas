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
        'snap_token',
        'status',
        'metode_pembayaran',
        'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'jumlah_tagihan' => 'decimal:2',
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id');
    }

    public function isLunas(): bool
    {
        return $this->status === 'lunas';
    }
}
