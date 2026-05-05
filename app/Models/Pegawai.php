<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'id_card',
        'bagian',
        'penempatan',
        'port_id',
        'asal',
        'ttd_path',
        'isarsip',
    ];
    
    public function port()
    {
        return $this->belongsTo(Port::class, 'port_id');
    }

    public function cuti()
    {
        return $this->hasMany(Cuti::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
