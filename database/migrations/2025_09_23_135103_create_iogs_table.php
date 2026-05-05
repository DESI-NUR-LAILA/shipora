<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('iogs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawais')->onDelete('cascade');
            $table->string('nomor_surat')->unique();
            $table->unsignedInteger('lampiran')->nullable();
            $table->string('nama_kapal');
            $table->string('master');
            $table->string('bendera')->default('Indonesia');
            $table->float('grt');
            $table->string('pemilik')->default('PT. Pertamina Trans Kontinental');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iog');
    }
};