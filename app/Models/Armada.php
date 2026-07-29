<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Armada extends Model
{
    protected $table = 'armadas';

    protected $fillable = [
        'kode_armada',
        'no_polisi',
        'jenis',
        'kapasitas',
        'status',
    ];

    public function pengirimans()
    {
        return $this->hasMany(Pengiriman::class, 'armada_id');
    }
}
