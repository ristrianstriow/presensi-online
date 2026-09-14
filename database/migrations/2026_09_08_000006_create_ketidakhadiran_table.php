<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ketidakhadiran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pegawai')->constrained('pegawai')->cascadeOnDelete();
            $table->string('keterangan', 50);
            $table->date('tanggal');
            $table->string('deskripsi', 225)->nullable();
            $table->string('file', 225)->nullable();
            $table->string('status_pengajuan', 20)->default('menunggu');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ketidakhadiran');
    }
};
