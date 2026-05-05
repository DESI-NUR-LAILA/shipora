<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cuti extends Model
{
    use HasFactory;

    protected $fillable = [
        'pegawai_id',
        'tujuan',
        'tanggal_mulai',
        'tanggal_selesai',
        'lama_hari',
        'hari_libur',
        'sisa_hak_cuti',
        'keterangan',
        'berkendaraan',
        'mengetahui_id',
        'menyetujui_id',
        'status',
        'alasan_penolakan',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function mengetahui()
    {
        return $this->belongsTo(Ttd::class, 'mengetahui_id');
    }
    
    public function menyetujui()
    {
        return $this->belongsTo(Ttd::class, 'menyetujui_id');
    }
}