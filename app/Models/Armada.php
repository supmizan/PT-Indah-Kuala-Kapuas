<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Armada extends Model
{
    use HasFactory;

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
