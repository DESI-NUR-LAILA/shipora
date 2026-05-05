<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ttd extends Model
{
    use HasFactory;

    // Kolom yang bisa diisi
    protected $fillable = [
        'hak_akses_id',
        'port_id',
        'nama',
        'ttd_path',
        'isarsip',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hak_akses()
    {
        return $this->belongsTo(HakAkses::class, 'hak_akses_id');
    }

    public function port()
    {
        return $this->belongsTo(Port::class, 'port_id');
    }
}
 