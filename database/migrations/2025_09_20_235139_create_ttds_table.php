<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ttds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hak_akses_id')
                  ->constrained('hak_akses')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->foreignId('port_id')
                  ->constrained('ports')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->string('nama');
            $table->string('ttd_path')->nullable();
            $table->boolean('isarsip')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ttds');
    }
};
