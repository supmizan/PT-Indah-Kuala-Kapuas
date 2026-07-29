<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengiriman extends Model
{
    protected $table = 'pengirimen'; // explicitly map to 'pengirimen'

    protected $fillable = [
        'pesanan_id',
        'driver_id',
        'armada_id',
        'tanggal_kirim',
        'status',
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id');
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    public function armada()
    {
        return $this->belongsTo(Armada::class, 'armada_id');
    }

    public function trackings()
    {
        return $this->hasMany(Tracking::class, 'pengiriman_id');
    }

    public function laporan()
    {
        return $this->hasOne(Laporan::class, 'pengiriman_id');
    }
}
