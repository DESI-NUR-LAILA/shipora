<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Port extends Model
{
    use HasFactory;

    // Kolom yang bisa diisi
    protected $fillable = [
        'port'
    ];

    public function pegawais()
    {
        return $this->hasMany(Pegawai::class, 'port_id');
    }
}
