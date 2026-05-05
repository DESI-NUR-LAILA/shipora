<?php

namespace Database\Seeders;

use App\Models\HakAkses;
use App\Models\Port;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        HakAkses::Create([
            'nama_hak_akses' => 'Sekretaris',
        ]);

        HakAkses::Create([
            'nama_hak_akses' => 'HOA',
        ]);

        HakAkses::Create([
            'nama_hak_akses' => 'Supervisor',
        ]);

        HakAkses::Create([
            'nama_hak_akses' => 'Admin',
        ]);

        HakAkses::Create([
            'nama_hak_akses' => 'PIC',
        ]);

        Port::Create([
            'port' => 'Jagir',
        ]);
        User::create([
            'email' => 'sekretaris@gmail.com',
            'hak_akses_id' => '1',
            'port_id' => '1',
            'password' => Hash::make('12345678'),
        ]);
    }
}
