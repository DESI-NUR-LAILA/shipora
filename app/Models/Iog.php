<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Iog extends Model
{
    use HasFactory;

    // Kolom yang bisa diisi
    protected $fillable = [
        'pegawai_id',
        'nomor_surat',
        'lampiran',
        'nama_kapal',
        'master',
        'bendera',
        'grt',
        'pemilik',
        'tanggal_mulai',
        'tanggal_selesai',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }
}
