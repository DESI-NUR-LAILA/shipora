<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pergerakan extends Model
{
    use HasFactory;

    // Kolom yang bisa diisi
    protected $fillable = [
        'user_id',
        'ship_name',
        'grt',
        'dwt',
        'flag',
        'principal',
        'ata',
        'last_port',
        'atd',
        'next_port',
        'activities',
        'jetty',
        'cargo',
        'status',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    public function laporans()
    {
        return $this->hasMany(Laporan::class, 'pergerakan_id');
    }
}
