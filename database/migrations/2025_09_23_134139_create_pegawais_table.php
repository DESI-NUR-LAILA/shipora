<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pegawais', function (Blueprint $table) {
            $table->id();
            $table->string('id_card')->unique();
            $table->string('nama');
            $table->string('bagian')->default('PT. Pertamina Trans Kontinental');

            // relasi ke tabel ports
            $table->foreignId('port_id')->constrained('ports')->onDelete('cascade');

            $table->string('jenis_pekerjaan')->default('Keagenan Domestik');
            $table->string('asal');
            $table->string('ttd_path')->nullable();
            $table->boolean('isarsip')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pegawais');
    }
};