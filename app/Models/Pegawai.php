<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pegawai extends Model
{
    use HasFactory;

    protected $table = 'pegawai';

    protected $fillable = [
        'nrg',
        'nama',
        'jenis_kelamin',
        'alamat',
        'no_handphone',
        'jabatan',
        'lokasi_presensi',
        'foto',
    ];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id_pegawai');
    }

    public function presensis(): HasMany
    {
        return $this->hasMany(Presensi::class, 'id_pegawai');
    }

    public function ketidakhadirans(): HasMany
    {
        return $this->hasMany(Ketidakhadiran::class, 'id_pegawai');
    }

    public function lokasiPresensiModel()
    {
        return LokasiPresensi::where('nama_lokasi', $this->lokasi_presensi)->first();
    }
}
