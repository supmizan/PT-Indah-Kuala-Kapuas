<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mitra extends Model
{
    use HasFactory;

    protected $table = 'mitras';

    protected $fillable = [
        'user_id',
        'nama_perusahaan',
        'alamat',
        'latitude',
        'longitude',
        'no_hp',
        'harga_per_liter',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pesanans()
    {
        return $this->hasMany(Pesanan::class, 'mitra_id');
    }
}