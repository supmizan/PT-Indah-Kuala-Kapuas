<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    use HasFactory;

    protected $table = 'pesanans';

    protected $fillable = [
        'mitra_id',
        'tanggal',
        'jumlah_bbm',
        'status',
    ];

    public function mitra()
    {
        return $this->belongsTo(Mitra::class, 'mitra_id');
    }

    public function pengirimans()
    {
        return $this->hasMany(Pengiriman::class, 'pesanan_id');
    }

    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class, 'pesanan_id');
    }

    public function sudahLunas(): bool
    {
        return $this->pembayaran && $this->pembayaran->status === 'lunas';
    }
}
