<?php

namespace Database\Seeders;

use App\Models\Presensi;
use Illuminate\Database\Seeder;

class PresensiSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'id' => 1,
                'id_pegawai' => 2,
                'tanggal_masuk' => '2026-09-08',
                'jam_masuk' => '07:55:00',
                'foto_masuk' => 'masuk_siti_20260908.jpg',
                'tanggal_keluar' => '2026-09-08',
                'jam_keluar' => '17:05:00',
                'foto_keluar' => 'keluar_siti_20260908.jpg',
            ],
            [
                'id' => 2,
                'id_pegawai' => 3,
                'tanggal_masuk' => '2026-09-08',
                'jam_masuk' => '08:20:00',
                'foto_masuk' => 'masuk_andi_20260908.jpg',
                'tanggal_keluar' => null,
                'jam_keluar' => null,
                'foto_keluar' => null,
            ],
        ];

        foreach ($data as $item) {
            Presensi::updateOrCreate(['id' => $item['id']], $item);
        }
    }
}
