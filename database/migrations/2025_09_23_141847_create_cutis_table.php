<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cutis', function (Blueprint $table) {
            $table->id();

            // Pegawai yang mengajukan
            $table->foreignId('pegawai_id')->constrained('pegawais')->onDelete('cascade');

            $table->string('tujuan'); 
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->integer('lama_hari');
            $table->integer('hari_libur')->default(0);
            $table->integer('sisa_hak_cuti');
            $table->string('keterangan');
            $table->enum('berkendaraan', ['Pribadi', 'Umum'])->nullable();

            // Mengetahui & Menyetujui (relasi ke tabel users atau pegawais)
            $table->foreignId('mengetahui_id')->nullable()->constrained('ttds')->nullOnDelete();
            $table->foreignId('menyetujui_id')->nullable()->constrained('ttds')->nullOnDelete();

            // Status cuti
            $table->enum('status', ['pending', 'diketahui', 'disetujui', 'ditolak'])->default('pending');
            $table->text('alasan_penolakan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cutis');
    }
};