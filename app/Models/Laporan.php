<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    protected $fillable = [
        'pergerakan_id',
        'user_id',
        'jenis_file',
        'nama_file',
        'path_file',
        'no_resi',
        'status',
        'komentar'
    ];

    // Relasi ke tabel pergerakan
    public function pergerakan()
    {
        return $this->belongsTo(Pergerakan::class, 'pergerakan_id');
    }

    // Relasi ke admin (user)
    public function user()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function userUpload()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
