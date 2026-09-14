<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lokasi_presensi', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lokasi', 50);
            $table->string('alamat_lokasi', 225);
            $table->string('tipe_lokasi', 50);
            $table->string('latitude', 50);
            $table->string('longitude', 50);
            $table->integer('radius');
            $table->string('zona_waktu', 4);
            $table->time('jam_masuk');
            $table->time('jam_pulang');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lokasi_presensi');
    }
};
