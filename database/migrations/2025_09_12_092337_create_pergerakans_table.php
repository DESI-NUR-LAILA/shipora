<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pergerakans', function (Blueprint $table) {
            $table->id(); // Primary key otomatis

            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('ship_name'); // Nama kapal
            $table->integer('grt'); // Gross Register Tonnage (boleh kosong)
            $table->integer('dwt')->nullable(); // Dead Weight Tonnage (boleh kosong)
            $table->string('flag'); // Bendera kapal
            $table->string('principal'); // Nama principal / agen
            $table->date('ata'); // Actual Time of Arrival
            $table->string('last_port'); // Pelabuhan asal
            $table->date('atd')->nullable(); // Actual Time of Departure
            $table->string('next_port')->nullable(); // Pelabuhan tujuan

            // Aktivitas kapal
            $table->enum('activities', ['Discharge', 'Loading', 'Bunker'])->nullable();

            // Dermaga / jetty
            $table->enum('jetty', ['Pertamina', 'Pelindo'])->nullable();

            $table->string('cargo')->nullable(); // Jenis muatan / cargo

            // Status kapal (tidak ada default value)
            $table->enum('status', ['CMP', 'Pihak Ketiga', 'Tugboat'])->nullable();

            $table->timestamps(); // Kolom created_at dan updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pergerakans');
    }
};